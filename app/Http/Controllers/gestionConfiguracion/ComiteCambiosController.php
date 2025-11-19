<?php

namespace App\Http\Controllers\GestionConfiguracion;

use App\Http\Controllers\Controller;
use App\Models\ComiteCambio;
use App\Models\Proyecto;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ComiteCambiosController extends Controller
{
    /**
     * Dashboard del CCB (para miembros)
     */
    public function dashboard(Proyecto $proyecto)
    {
        $this->verificarAccesoProyecto($proyecto);

        $ccb = ComiteCambio::where('proyecto_id', $proyecto->id)->first();

        if (!$ccb) {
            // Si no existe CCB, redirigir a configuración (solo para líder)
            if ($proyecto->esLider(Auth::id())) {
                return redirect()
                    ->route('proyectos.ccb.configurar', $proyecto)
                    ->with('info', 'Primero debes configurar el Comité de Control de Cambios (CCB)');
            } else {
                abort(403, 'El CCB de este proyecto aún no ha sido configurado');
            }
        }

        $esMiembro = $ccb->esMiembro(Auth::id());
        $esLider = $proyecto->esLider(Auth::id());

        // Permitir acceso si es miembro del CCB o líder del proyecto
        if (!$esMiembro && !$esLider) {
            return view('gestionConfiguracion.ccb.sin-acceso', compact('proyecto', 'ccb'));
        }

        // Obtener solicitudes pendientes de votación
        $solicitudesPendientes = $ccb->solicitudesPendientes();

        // Obtener solicitudes donde ya voté
        $misVotos = DB::table('votos_ccb')
            ->where('usuario_id', Auth::id())
            ->pluck('solicitud_cambio_id')
            ->toArray();

        // Estadísticas
        $estadisticas = [
            'total_solicitudes' => $solicitudesPendientes->count(),
            'pendientes_mi_voto' => $solicitudesPendientes->filter(function($sol) use ($misVotos) {
                return !in_array($sol->id, $misVotos);
            })->count(),
            'ya_vote' => count($misVotos),
        ];

        return view('gestionConfiguracion.ccb.dashboard', compact(
            'proyecto',
            'ccb',
            'solicitudesPendientes',
            'misVotos',
            'estadisticas'
        ));
    }

    /**
     * Configurar CCB (solo líder del proyecto)
     */
    public function configurar(Proyecto $proyecto)
    {
        if (!$proyecto->esLider(Auth::id())) {
            abort(403, 'Solo el líder del proyecto puede configurar el CCB');
        }

        $ccb = ComiteCambio::where('proyecto_id', $proyecto->id)->first();

        // Obtener usuarios que tienen acceso al proyecto
        $usuariosProyecto = $proyecto->usuarios()->get();

        // Obtener roles desde la tabla `roles` para mostrarlos dinámicamente
        // en la vista. Preferimos mostrar únicamente los roles marcados como
        // específicos para CCB (columna `es_para_ccb`) cuando exista esa
        // columna; si no existe, hacemos fallback a todos los roles para
        // mantener compatibilidad con instalaciones previas.
        $rolesQuery = DB::table('roles')->select('id', 'nombre');

        if (Schema::hasColumn('roles', 'es_para_ccb')) {
            $rolesQuery->where('es_para_ccb', true);
        }

        $roles = $rolesQuery->orderBy('nombre')->get();

        // Si no hay usuarios asignados todavía (proyecto nuevo), asegurarnos
        // de incluir al creador/líder del proyecto como opción mínima para
        // poder crear el CCB. También eliminamos duplicados por id.
        if ($usuariosProyecto->isEmpty() && $proyecto->creador) {
            $usuariosProyecto = collect([$proyecto->creador]);
        } else {
            // Asegurar que el creador siempre esté disponible en la lista
            if ($proyecto->creador) {
                $usuariosProyecto->push($proyecto->creador);
                $usuariosProyecto = $usuariosProyecto->unique('id')->values();
            }
        }

    return view('gestionConfiguracion.ccb.configurar', compact('proyecto', 'ccb', 'usuariosProyecto', 'roles'));
    }

    /**
     * Crear o actualizar CCB
     */
    public function guardarConfiguracion(Request $request, Proyecto $proyecto)
    {
        if (!$proyecto->esLider(Auth::id())) {
            abort(403, 'Solo el líder del proyecto puede configurar el CCB');
        }

        $validated = $request->validate([
            'nombre' => 'nullable|string|max:255',
            'miembros' => 'required|array|min:1',
            'miembros.*' => 'exists:usuarios,id',
            'roles_ccb' => 'nullable|array',
            'roles_ccb.*' => 'nullable|string|max:100',
        ]);

        DB::beginTransaction();
        try {
            $ccb = ComiteCambio::firstOrNew(['proyecto_id' => $proyecto->id]);
            $ccb->nombre = $validated['nombre'] ?? 'CCB - ' . $proyecto->nombre;
            $ccb->save();

            // Sincronizar miembros
            $miembrosData = [];
            foreach ($validated['miembros'] as $index => $usuarioId) {
                $miembrosData[$usuarioId] = [
                    'rol_en_ccb' => $validated['roles_ccb'][$index] ?? 'Miembro'
                ];
            }

            $ccb->miembros()->sync($miembrosData);

            // Calcular quorum automáticamente
            $ccb->calcularQuorum();

            DB::commit();

            return redirect()
                ->route('proyectos.ccb.dashboard', $proyecto)
                ->with('success', 'Comité de Control de Cambios configurado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Error al configurar CCB: ' . $e->getMessage()]);
        }
    }

    /**
     * Ver miembros del CCB
     */
    public function verMiembros(Proyecto $proyecto)
    {
        $this->verificarAccesoProyecto($proyecto);

        $ccb = ComiteCambio::where('proyecto_id', $proyecto->id)->first();

        if (!$ccb) {
            if ($proyecto->esLider(Auth::id())) {
                return redirect()
                    ->route('proyectos.ccb.configurar', $proyecto)
                    ->with('info', 'Primero debes configurar el CCB');
            } else {
                abort(403, 'El CCB de este proyecto aún no ha sido configurado');
            }
        }

        $esMiembro = $ccb->esMiembro(Auth::id());
        $esLider = $proyecto->esLider(Auth::id());

        if (!$esMiembro && !$esLider) {
            return view('gestionConfiguracion.ccb.sin-acceso', compact('proyecto', 'ccb'));
        }

        $miembros = $ccb->miembros()
            ->withPivot('rol_en_ccb')
            ->get();

        // Estadísticas por miembro
        $estadisticasMiembros = [];
        foreach ($miembros as $miembro) {
            $estadisticasMiembros[$miembro->id] = [
                'total_votos' => DB::table('votos_ccb')
                    ->where('ccb_id', $ccb->id)
                    ->where('usuario_id', $miembro->id)
                    ->count(),
                'aprobar' => DB::table('votos_ccb')
                    ->where('ccb_id', $ccb->id)
                    ->where('usuario_id', $miembro->id)
                    ->where('voto', 'APROBAR')
                    ->count(),
                'rechazar' => DB::table('votos_ccb')
                    ->where('ccb_id', $ccb->id)
                    ->where('usuario_id', $miembro->id)
                    ->where('voto', 'RECHAZAR')
                    ->count(),
            ];
        }

        // Cargar roles desde la tabla `roles` para mostrarlos en la vista.
        // Preferimos filtrar por `es_para_ccb` si la columna existe.
        $rolesQuery = DB::table('roles')->select('nombre');
        if (Schema::hasColumn('roles', 'es_para_ccb')) {
            $rolesQuery->where('es_para_ccb', true);
        }
        $roles = $rolesQuery->orderBy('nombre')->get();

        return view('gestionConfiguracion.ccb.miembros', compact(
            'proyecto',
            'ccb',
            'miembros',
            'estadisticasMiembros',
            'roles'
        ));
    }

    /**
     * Historial de votaciones
     */
    public function historialVotos(Proyecto $proyecto)
    {
        $this->verificarAccesoProyecto($proyecto);

        $ccb = ComiteCambio::where('proyecto_id', $proyecto->id)->first();

        if (!$ccb) {
            if ($proyecto->esLider(Auth::id())) {
                return redirect()
                    ->route('proyectos.ccb.configurar', $proyecto)
                    ->with('info', 'Primero debes configurar el CCB');
            } else {
                return redirect()->route('proyectos.show', $proyecto);
            }
        }

        $esMiembro = $ccb->esMiembro(Auth::id());
        $esLider = $proyecto->esLider(Auth::id());

        if (!$esMiembro && !$esLider) {
            return view('gestionConfiguracion.ccb.sin-acceso', compact('proyecto', 'ccb'));
        }

        $votos = $ccb->votos()
            ->with(['usuario', 'solicitudCambio'])
            ->orderBy('votado_en', 'desc')
            ->paginate(50);

        return view('gestionConfiguracion.ccb.historial', compact('proyecto', 'ccb', 'votos'));
    }

    /**
     * Verificar acceso al proyecto
     */
    private function verificarAccesoProyecto(Proyecto $proyecto)
    {
        $esLider = $proyecto->esLider(Auth::id());
        $esMiembro = $proyecto->usuarios()->where('usuario_id', Auth::id())->exists();

        if (!$esLider && !$esMiembro) {
            abort(403, 'No tienes acceso a este proyecto');
        }
    }

    /**
     * Agregar miembro al CCB
     */
    public function agregarMiembro(Request $request, Proyecto $proyecto)
    {
        $this->verificarAccesoProyecto($proyecto);

        if (!$proyecto->esLider(Auth::id())) {
            abort(403, 'Solo el líder del proyecto puede gestionar miembros del CCB');
        }

        $ccb = ComiteCambio::where('proyecto_id', $proyecto->id)->first();
        if (!$ccb) {
            return back()->withErrors(['error' => 'Primero debes configurar el CCB']);
        }

        $validated = $request->validate([
            'usuario_id' => 'required|exists:usuarios,id',
            'rol_en_ccb' => 'nullable|string|max:100',
        ]);

        // Verificar que el usuario tenga acceso al proyecto
        $tieneAccesoProyecto = $proyecto->usuarios()->where('usuario_id', $validated['usuario_id'])->exists();

        if (!$tieneAccesoProyecto) {
            return back()->withErrors(['error' => 'El usuario debe tener acceso al proyecto para formar parte del CCB']);
        }

        // Verificar que no esté ya en el CCB
        if ($ccb->esMiembro($validated['usuario_id'])) {
            return back()->withErrors(['error' => 'Este usuario ya es miembro del CCB']);
        }

        $ccb->miembros()->attach($validated['usuario_id'], [
            'rol_en_ccb' => $validated['rol_en_ccb'] ?? 'Miembro'
        ]);

        $ccb->calcularQuorum();

        return back()->with('success', 'Miembro agregado al CCB exitosamente');
    }

    /**
     * Actualizar rol de miembro del CCB
     */
    public function actualizarRolMiembro(Request $request, Proyecto $proyecto, $usuarioId)
    {
        $this->verificarAccesoProyecto($proyecto);

        if (!$proyecto->esLider(Auth::id())) {
            abort(403, 'Solo el líder del proyecto puede gestionar miembros del CCB');
        }

        $ccb = ComiteCambio::where('proyecto_id', $proyecto->id)->first();
        if (!$ccb) {
            return back()->withErrors(['error' => 'Primero debes configurar el CCB']);
        }

        $validated = $request->validate([
            'rol_en_ccb' => 'nullable|string|max:100',
        ]);

        if (!$ccb->esMiembro($usuarioId)) {
            return back()->withErrors(['error' => 'Este usuario no es miembro del CCB']);
        }

        $ccb->miembros()->updateExistingPivot($usuarioId, [
            'rol_en_ccb' => $validated['rol_en_ccb'] ?? 'Miembro'
        ]);

        return back()->with('success', 'Rol del miembro actualizado exitosamente');
    }

    /**
     * Remover miembro del CCB
     */
    public function removerMiembro(Proyecto $proyecto, $usuarioId)
    {
        $this->verificarAccesoProyecto($proyecto);

        if (!$proyecto->esLider(Auth::id())) {
            abort(403, 'Solo el líder del proyecto puede gestionar miembros del CCB');
        }

        $ccb = ComiteCambio::where('proyecto_id', $proyecto->id)->first();
        if (!$ccb) {
            return back()->withErrors(['error' => 'Primero debes configurar el CCB']);
        }

        if (!$ccb->esMiembro($usuarioId)) {
            return back()->withErrors(['error' => 'Este usuario no es miembro del CCB']);
        }

        // No permitir que se quede sin miembros
        if ($ccb->miembros()->count() <= 1) {
            return back()->withErrors(['error' => 'El CCB debe tener al menos un miembro']);
        }

        $ccb->miembros()->detach($usuarioId);
        $ccb->calcularQuorum();

        return back()->with('success', 'Miembro removido del CCB exitosamente');
    }
}
