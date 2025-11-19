<?php

namespace App\Http\Controllers\GestionConfiguracion;

use App\Http\Controllers\Controller;
use App\Models\ComiteCambio;
use App\Models\ElementoConfiguracion;
use App\Models\ItemCambio;
use App\Models\Proyecto;
use App\Models\SolicitudCambio;
use App\Models\VotoCCB;
use App\Models\VersionEc;
use App\Services\ImpactoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use App\Jobs\ImplementarSolicitudAprobadaJob;
use App\Notifications\Cambios\NuevaSolicitudCambio;
use App\Notifications\Cambios\SolicitudAprobada;
use App\Notifications\Cambios\SolicitudRechazada;

class SolicitudCambioController extends Controller
{
    protected $impactoService;

    public function __construct(ImpactoService $impactoService)
    {
        $this->impactoService = $impactoService;
    }

    /**
     * Listar solicitudes de cambio de un proyecto
     */
    public function index(Proyecto $proyecto)
    {
        $this->verificarAccesoProyecto($proyecto);

        $solicitudes = $proyecto->hasMany(SolicitudCambio::class, 'proyecto_id')
            ->with(['solicitante', 'items.elementoConfiguracion', 'votos.usuario'])
            ->orderBy('creado_en', 'desc')
            ->get();

        $ccb = $proyecto->hasOne(ComiteCambio::class, 'proyecto_id')->first();
        $esMiembroCCB = $ccb && $ccb->esMiembro(Auth::id());

        return view('gestionConfiguracion.solicitudes.index', compact('proyecto', 'solicitudes', 'esMiembroCCB'));
    }

    /**
     * Mostrar formulario de creación de solicitud
     */
    public function create(Proyecto $proyecto)
    {
        $this->verificarAccesoProyecto($proyecto);

        // Obtener elementos de configuración disponibles
        $elementos = $proyecto->elementosConfiguracion()
            ->whereIn('estado', ['PENDIENTE', 'BORRADOR', 'EN_REVISION', 'APROBADO', 'LIBERADO'])
            ->with('versionActual')
            ->orderBy('codigo_ec')
            ->get();

        return view('gestionConfiguracion.solicitudes.create', compact('proyecto', 'elementos'));
    }

    /**
     * Almacenar nueva solicitud de cambio
     */
    public function store(Request $request, Proyecto $proyecto)
    {
        $this->verificarAccesoProyecto($proyecto);

        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion_cambio' => 'required|string',
            'motivo_cambio' => 'required|string',
            'prioridad' => 'required|in:BAJA,MEDIA,ALTA,CRITICA',
            'elementos' => 'required|array|min:1',
            'elementos.*.ec_id' => 'required|exists:elementos_configuracion,id',
            'elementos.*.nota' => 'nullable|string',
            'origen_cambio' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            // Crear solicitud
            $solicitud = new SolicitudCambio();
            $solicitud->id = Str::uuid()->toString();
            $solicitud->proyecto_id = $proyecto->id;
            $solicitud->titulo = $validated['titulo'];
            $solicitud->descripcion_cambio = $validated['descripcion_cambio'];
            $solicitud->motivo_cambio = $validated['motivo_cambio'];
            $solicitud->prioridad = $validated['prioridad'];
            $solicitud->estado = 'ABIERTA';
            $solicitud->solicitante_id = Auth::id();
            $solicitud->origen_cambio = $validated['origen_cambio'] ?? null;
            $solicitud->save();

            // Crear items de cambio
            foreach ($validated['elementos'] as $item) {
                $ec = ElementoConfiguracion::find($item['ec_id']);

                $itemCambio = new ItemCambio();
                $itemCambio->solicitud_cambio_id = $solicitud->id;
                $itemCambio->ec_id = $ec->id;
                $itemCambio->version_actual_ec_id = $ec->version_actual_id;
                $itemCambio->nota = $item['nota'] ?? null;
                $itemCambio->save();
            }

            DB::commit();

            // 🔔 NOTIFICAR a todos los miembros del CCB
            try {
                $ccb = $proyecto->hasOne(ComiteCambio::class, 'proyecto_id')->first();
                if ($ccb) {
                    $miembrosCCB = $ccb->miembros;
                    Notification::send($miembrosCCB, new NuevaSolicitudCambio($solicitud));
                }
            } catch (\Exception $e) {
                Log::warning('Error al enviar notificaciones de solicitud: ' . $e->getMessage());
            }

            return redirect()
                ->route('proyectos.solicitudes.show', [$proyecto, $solicitud])
                ->with('success', 'Solicitud de cambio creada exitosamente. Ahora debe evaluarse el impacto.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Error al crear solicitud: ' . $e->getMessage()]);
        }
    }

    /**
     * Mostrar detalles de la solicitud
     */
    public function show(Proyecto $proyecto, SolicitudCambio $solicitud)
    {
        $this->verificarAccesoProyecto($proyecto);

        if ($solicitud->proyecto_id !== $proyecto->id) {
            abort(404);
        }

        $solicitud->load([
            'solicitante',
            'items.elementoConfiguracion.versionActual',
            'votos.usuario',
        ]);

        $ccb = $proyecto->hasOne(ComiteCambio::class, 'proyecto_id')->first();
        $esMiembroCCB = $ccb && $ccb->esMiembro(Auth::id());
        $yaVoto = false;

        if ($esMiembroCCB && $solicitud->estado === 'EN_REVISION') {
            $yaVoto = VotoCCB::where('solicitud_cambio_id', $solicitud->id)
                ->where('usuario_id', Auth::id())
                ->exists();
        }

        // Calcular estadísticas de votación
        $estadisticasVotos = [
            'total_miembros' => $ccb ? $ccb->miembros()->count() : 0,
            'votos_emitidos' => $solicitud->votos->count(),
            'aprobar' => $solicitud->votos->where('voto', 'APROBAR')->count(),
            'rechazar' => $solicitud->votos->where('voto', 'RECHAZAR')->count(),
            'abstenerse' => $solicitud->votos->where('voto', 'ABSTENERSE')->count(),
            'quorum' => $ccb?->quorum ?? 1,
        ];

        return view('gestionConfiguracion.solicitudes.show', compact(
            'proyecto',
            'solicitud',
            'ccb',
            'esMiembroCCB',
            'yaVoto',
            'estadisticasVotos'
        ));
    }

    /**
     * Evaluar impacto de la solicitud
     */
    public function evaluarImpacto(Proyecto $proyecto, SolicitudCambio $solicitud)
    {
        $this->verificarAccesoProyecto($proyecto);

        if ($solicitud->proyecto_id !== $proyecto->id) {
            abort(404);
        }

        $solicitud->load('items.elementoConfiguracion');

        // Obtener IDs de elementos afectados
        $ecIds = $solicitud->items->pluck('ec_id')->toArray();

        // Analizar impacto usando el servicio
        $analisisImpacto = $this->impactoService->analizarImpacto($ecIds);

        return view('gestionConfiguracion.solicitudes.evaluar-impacto', compact(
            'proyecto',
            'solicitud',
            'analisisImpacto'
        ));
    }

    /**
     * Enviar solicitud al CCB para revisión
     */
    public function enviarACCB(Proyecto $proyecto, SolicitudCambio $solicitud)
    {
        $this->verificarAccesoProyecto($proyecto);

        if ($solicitud->proyecto_id !== $proyecto->id) {
            abort(404);
        }

        if ($solicitud->estado !== 'ABIERTA') {
            return back()->withErrors(['error' => 'Solo se pueden enviar solicitudes en estado ABIERTA']);
        }

        // Verificar que existe un CCB
        $ccb = ComiteCambio::where('proyecto_id', $proyecto->id)->first();
        if (!$ccb) {
            return back()->withErrors(['error' => 'Este proyecto no tiene un Comité de Control de Cambios configurado']);
        }

        // Evaluar impacto y guardarlo
        $ecIds = $solicitud->items->pluck('ec_id')->toArray();
        $analisisImpacto = $this->impactoService->analizarImpacto($ecIds);

        $resumenImpacto = sprintf(
            "Nivel: %s\nTotal afectados: %d (Directos: %d, Indirectos: %d)\n\nRecomendaciones:\n- %s",
            $analisisImpacto['nivel_impacto'],
            $analisisImpacto['total_afectados'],
            count($analisisImpacto['ec_afectados_directos']),
            count($analisisImpacto['ec_afectados_indirectos']),
            implode("\n- ", $analisisImpacto['recomendaciones'])
        );

        $solicitud->resumen_impacto = $resumenImpacto;
        $solicitud->estado = 'EN_REVISION';
        $solicitud->save();

        // TODO: Enviar notificaciones a miembros del CCB

        return redirect()
            ->route('proyectos.solicitudes.show', [$proyecto, $solicitud])
            ->with('success', 'Solicitud enviada al CCB para revisión');
    }

    /**
     * Mostrar formulario para emitir voto
     */
    public function mostrarFormularioVoto(Proyecto $proyecto, SolicitudCambio $solicitud)
    {
        $this->verificarAccesoProyecto($proyecto);

        if ($solicitud->proyecto_id !== $proyecto->id) {
            abort(404);
        }

        if ($solicitud->estado !== 'EN_REVISION') {
            return back()->withErrors(['error' => 'Solo se pueden votar solicitudes en revisión']);
        }

        $ccb = ComiteCambio::where('proyecto_id', $proyecto->id)->first();
        if (!$ccb || !$ccb->esMiembro(Auth::id())) {
            return back()->withErrors(['error' => 'No eres miembro del CCB de este proyecto']);
        }

        // Verificar que no haya votado ya
        $yaVoto = VotoCCB::where('solicitud_cambio_id', $solicitud->id)
            ->where('usuario_id', Auth::id())
            ->exists();

        if ($yaVoto) {
            return back()->withErrors(['error' => 'Ya has emitido tu voto en esta solicitud']);
        }

        return view('gestionConfiguracion.solicitudes.votar', compact('proyecto', 'solicitud'));
    }
    public function votar(Request $request, Proyecto $proyecto, SolicitudCambio $solicitud)
    {
        $this->verificarAccesoProyecto($proyecto);

        if ($solicitud->proyecto_id !== $proyecto->id) {
            abort(404);
        }

        if ($solicitud->estado !== 'EN_REVISION') {
            return back()->withErrors(['error' => 'Solo se pueden votar solicitudes en revisión']);
        }

        $ccb = ComiteCambio::where('proyecto_id', $proyecto->id)->first();
        if (!$ccb || !$ccb->esMiembro(Auth::id())) {
            return back()->withErrors(['error' => 'No eres miembro del CCB de este proyecto']);
        }

        // Verificar que no haya votado ya
        $yaVoto = VotoCCB::where('solicitud_cambio_id', $solicitud->id)
            ->where('usuario_id', Auth::id())
            ->exists();

        if ($yaVoto) {
            return back()->withErrors(['error' => 'Ya has emitido tu voto en esta solicitud']);
        }

        $validated = $request->validate([
            'voto' => 'required|in:APROBAR,RECHAZAR,ABSTENERSE',
            'comentario' => 'nullable|string|max:1000',
        ]);

        // Registrar voto
        $voto = new VotoCCB();
        $voto->ccb_id = $ccb->id;
        $voto->solicitud_cambio_id = $solicitud->id;
        $voto->usuario_id = Auth::id();
        $voto->voto = $validated['voto'];
        $voto->comentario = $validated['comentario'];
        $voto->save();

        // Verificar si se alcanzó el quorum y procesar
        $this->verificarYProcesarQuorum($proyecto, $solicitud, $ccb);

        return redirect()
            ->route('proyectos.solicitudes.show', [$proyecto, $solicitud])
            ->with('success', 'Voto registrado exitosamente');
    }

    /**
     * Verificar si se alcanzó el quorum y procesar la decisión
     */
    private function verificarYProcesarQuorum(Proyecto $proyecto, SolicitudCambio $solicitud, ComiteCambio $ccb)
    {
        $totalVotos = $solicitud->votos()->count();
        $totalMiembros = $ccb->miembros()->count();

        // Verificar si todos votaron o si se alcanzó el quorum
        if ($totalVotos >= $ccb->quorum) {
            $votosAprobar = $solicitud->votos()->where('voto', 'APROBAR')->count();
            $votosRechazar = $solicitud->votos()->where('voto', 'RECHAZAR')->count();

            // Decisión: mayoría simple de aprobaciones sobre el quorum
            if ($votosAprobar >= $ccb->quorum) {
                // APROBADA - Registrar auditoría
                $solicitud->update([
                    'estado' => 'APROBADA',
                    'aprobado_por' => Auth::id(),
                    'aprobado_en' => now(),
                ]);

                // 🚀 NUEVA FUNCIONALIDAD: Encolar implementación automática
                ImplementarSolicitudAprobadaJob::dispatch($solicitud);

                // 🔔 Notificar aprobación
                try {
                    // Notificar al creador
                    $solicitud->solicitante->notify(new SolicitudAprobada($solicitud));

                    // Notificar a todos los miembros del CCB
                    $miembrosCCB = $ccb->miembros;
                    Notification::send($miembrosCCB, new SolicitudAprobada($solicitud));
                } catch (\Exception $e) {
                    Log::warning('Error al enviar notificaciones de aprobación: ' . $e->getMessage());
                }

            } elseif ($votosRechazar >= $ccb->quorum) {
                // RECHAZADA - Registrar auditoría
                $solicitud->update([
                    'estado' => 'RECHAZADA',
                    'rechazado_por' => Auth::id(),
                    'rechazado_en' => now(),
                    'motivo_rechazo' => 'Rechazada por mayoría del CCB',
                ]);

                // 🔔 Notificar rechazo
                try {
                    // Notificar al creador
                    $solicitud->solicitante->notify(new SolicitudRechazada($solicitud));

                    // Notificar a todos los miembros del CCB
                    $miembrosCCB = $ccb->miembros;
                    Notification::send($miembrosCCB, new SolicitudRechazada($solicitud));
                } catch (\Exception $e) {
                    Log::warning('Error al enviar notificaciones de rechazo: ' . $e->getMessage());
                }
            }
        }
    }

    /**
     * Implementar cambio aprobado (crear nuevas versiones de EC)
     */
    public function implementar(Proyecto $proyecto, SolicitudCambio $solicitud)
    {
        $this->verificarAccesoProyecto($proyecto);

        if ($solicitud->proyecto_id !== $proyecto->id) {
            abort(404);
        }

        if ($solicitud->estado !== 'APROBADA') {
            return back()->withErrors(['error' => 'Solo se pueden implementar solicitudes aprobadas']);
        }

        DB::beginTransaction();
        try {
            foreach ($solicitud->items as $item) {
                $ec = $item->elementoConfiguracion;

                // Calcular nueva versión (incrementar minor)
                $versionActual = $ec->versionActual;
                $versionParts = explode('.', $versionActual?->version ?? '0.0.0');

                if ($versionParts[0] === '0') {
                    $nuevaVersion = '1.0.0';
                } else {
                    $versionParts[1] = (int)$versionParts[1] + 1;
                    $versionParts[2] = 0;
                    $nuevaVersion = implode('.', $versionParts);
                }

                // Crear nueva versión
                $version = new VersionEc();
                $version->id = Str::uuid()->toString();
                $version->ec_id = $ec->id;
                $version->version = $nuevaVersion;
                $version->registro_cambios = "Cambio aprobado por CCB: {$solicitud->titulo}\n\n{$item->nota}";
                $version->creado_por = Auth::id();
                $version->aprobado_por = Auth::id();
                $version->aprobado_en = now();
                $version->save();

                // Actualizar EC
                $ec->version_actual_id = $version->id;
                $ec->estado = 'APROBADO';
                $ec->save();
            }

            // Marcar solicitud como implementada
            $solicitud->estado = 'IMPLEMENTADA';
            $solicitud->save();

            DB::commit();

            return redirect()
                ->route('proyectos.solicitudes.show', [$proyecto, $solicitud])
                ->with('success', 'Cambios implementados exitosamente. Se crearon nuevas versiones de los EC afectados.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al implementar cambios: ' . $e->getMessage()]);
        }
    }

    /**
     * Cerrar solicitud (sin implementar)
     */
    public function cerrar(Proyecto $proyecto, SolicitudCambio $solicitud)
    {
        $this->verificarAccesoProyecto($proyecto);

        if ($solicitud->proyecto_id !== $proyecto->id) {
            abort(404);
        }

        if (!in_array($solicitud->estado, ['RECHAZADA', 'IMPLEMENTADA'])) {
            return back()->withErrors(['error' => 'Solo se pueden cerrar solicitudes rechazadas o implementadas']);
        }

        $solicitud->estado = 'CERRADA';
        $solicitud->save();

        return redirect()
            ->route('proyectos.solicitudes.index', $proyecto)
            ->with('success', 'Solicitud cerrada');
    }

    /**
     * Verificar acceso al proyecto
     */
    private function verificarAccesoProyecto(Proyecto $proyecto)
    {
        $usuarioId = Auth::id();

        // Es creador del proyecto
        $esCreador = $proyecto->creado_por === $usuarioId;

        // Es miembro directo del proyecto (tabla usuarios_roles)
        $esMiembroDirecto = $proyecto->usuarios()->where('usuario_id', $usuarioId)->exists();

        // Es miembro de algún equipo del proyecto
        $esMiembroEquipo = $proyecto->equipos()
            ->whereHas('miembros', function($query) use ($usuarioId) {
                $query->where('usuario_id', $usuarioId);
            })
            ->exists();

        if (!$esCreador && !$esMiembroDirecto && !$esMiembroEquipo) {
            abort(403, 'No tienes acceso a este proyecto');
        }
    }
}
