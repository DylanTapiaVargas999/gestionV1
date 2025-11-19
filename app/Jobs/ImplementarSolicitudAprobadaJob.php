<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\SolicitudCambio;
use App\Models\TareaProyecto;
use App\Models\FaseMetodologia;
use App\Models\VersionEC;
use App\Services\CronogramaInteligenteService;
use Illuminate\Support\Str;

class ImplementarSolicitudAprobadaJob implements ShouldQueue
{
    use Queueable;

    protected $solicitudCambio;

    /**
     * Create a new job instance.
     */
    public function __construct(SolicitudCambio $solicitudCambio)
    {
        $this->solicitudCambio = $solicitudCambio;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::beginTransaction();

        try {
            Log::info("🔧 Iniciando implementación automática de solicitud: {$this->solicitudCambio->titulo}");

            // 1. Crear nuevas versiones de EC (lo que ya existía en implementar())
            $this->crearVersionesEC();

            // 2. Crear tareas de implementación según metodología
            $this->crearTareasImplementacion();

            // 3. 🎯 NUEVO: Analizar impacto en cronograma y proponer ajustes
            $this->analizarImpactoCronograma();

            // 4. Marcar solicitud como implementada
            $this->solicitudCambio->update(['estado' => 'IMPLEMENTADA']);

            DB::commit();
            Log::info("✅ Solicitud implementada exitosamente");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("❌ Error implementando solicitud: " . $e->getMessage());
            throw $e; // Re-throw para que Laravel maneje el retry
        }
    }

    /**
     * Crear nuevas versiones de EC (lógica original)
     */
    private function crearVersionesEC()
    {
        foreach ($this->solicitudCambio->items as $item) {
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
            $version = new VersionEC();
            $version->id = Str::uuid()->toString();
            $version->ec_id = $ec->id;
            $version->version = $nuevaVersion;
            $version->estado = 'PENDIENTE'; // ✅ CORRECTO: Inicia pendiente, programador la desarrolla
            $version->registro_cambios = "Cambio aprobado por CCB: {$this->solicitudCambio->titulo}\n\n{$item->nota}";
            $version->creado_por = $this->solicitudCambio->aprobado_por;
            // ❌ NO tiene aprobado_por/aprobado_en aún - eso pasa después del desarrollo
            $version->save();

            // Actualizar EC
            $ec->update([
                'version_actual_id' => $version->id,
                'estado' => 'EN_REVISION', // ✅ EC en revisión para que programador trabaje
            ]);

            Log::info("📋 Versión {$nuevaVersion} PENDIENTE creada para EC: {$ec->codigo_ec} - Listo para desarrollo");
        }
    }

    /**
     * Crear tareas de implementación según metodología del proyecto
     */
    private function crearTareasImplementacion()
    {
        $proyecto = $this->solicitudCambio->proyecto;
        $metodologia = $proyecto->metodologia;

        Log::info("🎯 Creando tareas para metodología: {$metodologia->nombre}");

        if ($metodologia->nombre === 'Scrum') {
            $this->crearTareasScrum($proyecto);
        } elseif ($metodologia->nombre === 'Cascada') {
            $this->crearTareasCascada($proyecto);
        }
    }

    /**
     * Crear tareas para metodología Scrum
     */
    private function crearTareasScrum($proyecto)
    {
        Log::info("🔍 Buscando fase 'Product Backlog' para proyecto {$proyecto->nombre} (Metodología ID: {$proyecto->id_metodologia})");

        // Para Scrum: crear en Product Backlog
        $faseBacklog = FaseMetodologia::where('id_metodologia', $proyecto->id_metodologia)
            ->where('nombre_fase', 'Product Backlog')
            ->first();

        if (!$faseBacklog) {
            Log::error("❌ No se encontró fase 'Product Backlog' para Scrum - ID Metodología: {$proyecto->id_metodologia}");
            Log::error("Fases disponibles: " . FaseMetodologia::where('id_metodologia', $proyecto->id_metodologia)->pluck('nombre_fase')->implode(', '));
            throw new \Exception("No se encontró la fase 'Product Backlog' para crear las tareas");
        }

        Log::info("✅ Fase encontrada: {$faseBacklog->nombre_fase} (ID: {$faseBacklog->id_fase})");

        // Crear una historia de usuario por cada EC afectado
        foreach ($this->solicitudCambio->items as $item) {
            $ec = $item->elementoConfiguracion;

            Log::info("📝 Creando tarea Scrum para EC: {$ec->codigo_ec} - {$ec->titulo}");

            $tarea = TareaProyecto::create([
                'id_proyecto' => $proyecto->id,
                'id_fase' => $faseBacklog->id_fase,
                'id_ec' => $ec->id,
                'id_sprint' => null, // ✅ Explícitamente NULL - Se asignará durante Sprint Planning
                'nombre' => "Implementar cambio: {$ec->titulo}",
                'descripcion' => "Solicitud de cambio: {$this->solicitudCambio->titulo}\n\n{$item->nota}",
                // ✅ Normalizar estado a 'Pendiente' (con inicial mayúscula para consistencia)
                'estado' => 'Pendiente',
                'prioridad' => $this->convertirPrioridadScrum($this->solicitudCambio->prioridad),
                'story_points' => $this->estimarStoryPoints($this->solicitudCambio->prioridad),
                'criterios_aceptacion' => [
                    "El cambio debe ser implementado según la descripción de la solicitud",
                    "El EC {$ec->codigo_ec} debe tener la nueva versión aplicada",
                    "Debe pasar todas las pruebas de calidad",
                    "Debe vincularse el commit de GitHub con la URL correspondiente"
                ],
                'responsable' => $this->obtenerResponsablePorDefecto($proyecto), // Asignar responsable inteligente
                'creado_por' => $this->solicitudCambio->aprobado_por,
            ]);

            Log::info("✅ Tarea Scrum #{$tarea->id_tarea} creada exitosamente para EC: {$ec->codigo_ec}");
        }
    }

    /**
     * Crear tareas para metodología Cascada
     */
    private function crearTareasCascada($proyecto)
    {
        Log::info("🔍 Buscando fase 'Implementación' para proyecto {$proyecto->nombre} (Metodología ID: {$proyecto->id_metodologia})");

        // Para Cascada: crear en fase Implementación
        $faseImplementacion = FaseMetodologia::where('id_metodologia', $proyecto->id_metodologia)
            ->where('nombre_fase', 'Implementación')
            ->first();

        if (!$faseImplementacion) {
            Log::error("❌ No se encontró fase 'Implementación' para Cascada - ID Metodología: {$proyecto->id_metodologia}");
            Log::error("Fases disponibles: " . FaseMetodologia::where('id_metodologia', $proyecto->id_metodologia)->pluck('nombre_fase')->implode(', '));
            throw new \Exception("No se encontró la fase 'Implementación' para crear las tareas");
        }

        Log::info("✅ Fase encontrada: {$faseImplementacion->nombre_fase} (ID: {$faseImplementacion->id_fase})");

        // Crear una tarea por cada EC afectado
        foreach ($this->solicitudCambio->items as $item) {
            $ec = $item->elementoConfiguracion;

            Log::info("📝 Creando tarea Cascada para EC: {$ec->codigo_ec} - {$ec->titulo}");

            $tarea = TareaProyecto::create([
                'id_proyecto' => $proyecto->id,
                'id_fase' => $faseImplementacion->id_fase,
                'id_ec' => $ec->id,
                'nombre' => "Implementar cambio: {$ec->titulo}",
                'descripcion' => "Solicitud de cambio: {$this->solicitudCambio->titulo}\n\n{$item->nota}",
                // ✅ Normalizar estado a 'Pendiente' (con inicial mayúscula para consistencia)
                'estado' => 'Pendiente',
                'prioridad' => $this->convertirPrioridadCascada($this->solicitudCambio->prioridad),
                'horas_estimadas' => $this->estimarHoras($this->solicitudCambio->prioridad),
                'fecha_inicio' => now()->addDay(), // Comenzar mañana
                'fecha_fin' => now()->addDays($this->calcularDuracionDias($this->solicitudCambio->prioridad)),
                'entregable' => "EC {$ec->codigo_ec} actualizado con los cambios solicitados",
                'responsable' => $this->obtenerResponsablePorDefecto($proyecto), // Asignar responsable inteligente
                'creado_por' => $this->solicitudCambio->aprobado_por,
            ]);

            Log::info("✅ Tarea Cascada #{$tarea->id_tarea} creada exitosamente para EC: {$ec->codigo_ec}");
        }
    }

    /**
     * Convertir prioridad de solicitud a prioridad Scrum
     */
    private function convertirPrioridadScrum($prioridad)
    {
        return match($prioridad) {
            'CRITICA' => 5,
            'ALTA' => 4,
            'MEDIA' => 3,
            'BAJA' => 2,
            default => 3,
        };
    }

    /**
     * Convertir prioridad de solicitud a prioridad Cascada
     */
    private function convertirPrioridadCascada($prioridad)
    {
        return match($prioridad) {
            'CRITICA' => 1,
            'ALTA' => 2,
            'MEDIA' => 3,
            'BAJA' => 4,
            default => 3,
        };
    }

    /**
     * Estimar story points según prioridad
     */
    private function estimarStoryPoints($prioridad)
    {
        return match($prioridad) {
            'CRITICA' => 8,
            'ALTA' => 5,
            'MEDIA' => 3,
            'BAJA' => 1,
            default => 3,
        };
    }

    /**
     * Estimar horas según prioridad
     */
    private function estimarHoras($prioridad)
    {
        return match($prioridad) {
            'CRITICA' => 40.0,
            'ALTA' => 24.0,
            'MEDIA' => 16.0,
            'BAJA' => 8.0,
            default => 16.0,
        };
    }

    /**
     * Calcular duración en días según prioridad
     */
    private function calcularDuracionDias($prioridad)
    {
        return match($prioridad) {
            'CRITICA' => 5, // 1 semana
            'ALTA' => 3,    // 3 días
            'MEDIA' => 2,   // 2 días
            'BAJA' => 1,    // 1 día
            default => 2,
        };
    }

    /**
     * Analizar impacto en cronograma y proponer ajustes si es necesario
     */
    private function analizarImpactoCronograma()
    {
        $proyecto = $this->solicitudCambio->proyecto;

        try {
            $cronogramaService = new CronogramaInteligenteService();

            Log::info("📊 Analizando impacto en cronograma del proyecto: {$proyecto->nombre}");

            // Analizar cronograma después de agregar nuevas tareas
            $analisis = $cronogramaService->analizarCronograma($proyecto);

            // Si hay problemas detectados, proponer ajuste automático
            if (!empty($analisis['desviaciones']) || !empty($analisis['sobrecarga']) || $analisis['salud'] < 70) {

                Log::warning("⚠️ Problemas detectados en cronograma. Salud: {$analisis['salud']}%");

                // Generar ajuste automático
                $ajuste = $cronogramaService->generarAjuste($proyecto, [
                    'motivo' => "Nuevas tareas por solicitud de cambio: {$this->solicitudCambio->titulo}",
                    'nivel_urgencia' => $this->solicitudCambio->prioridad,
                    'auto_aprobar' => $this->solicitudCambio->prioridad === 'CRITICA', // Auto-aprobar si es crítico
                ]);

                if ($ajuste) {
                    Log::info("🔄 Ajuste de cronograma propuesto: {$ajuste->id}");

                    // Si es crítico, aplicar automáticamente
                    if ($this->solicitudCambio->prioridad === 'CRITICA') {
                        $aprobado = $cronogramaService->aprobarAjuste($ajuste, $this->solicitudCambio->aprobado_por);
                        if ($aprobado) {
                            $cronogramaService->aplicarAjuste($ajuste);
                            Log::info("✅ Ajuste crítico aplicado automáticamente");
                        }
                    } else {
                        Log::info("📋 Ajuste propuesto. Requiere aprobación manual.");
                    }
                }

            } else {
                Log::info("✅ No se detectaron problemas significativos en el cronograma");
            }

        } catch (\Exception $e) {
            // No fallar todo el job por problemas de cronograma
            Log::warning("⚠️ Error al analizar cronograma: " . $e->getMessage());
        }
    }

    /**
     * Obtener responsable por defecto para tareas creadas automáticamente.
     * Preferimos asignar al creador del proyecto (líder) si está disponible.
     * 
     * PRIORIDAD DE ASIGNACIÓN:
     * 1. Creador del proyecto (Project Manager)
     * 2. Líder del equipo del proyecto
     * 3. Primer miembro del equipo con rol de desarrollador
     * 4. Quien aprobó la solicitud de cambio (CCB member)
     * 5. NULL (se debe asignar manualmente después)
     */
    private function obtenerResponsablePorDefecto($proyecto)
    {
        try {
            // 1️⃣ Intentar asignar al creador del proyecto (PM)
            if (isset($proyecto->creado_por) && $proyecto->creado_por) {
                Log::info("✅ Responsable asignado: Creador del proyecto (ID: {$proyecto->creado_por})");
                return $proyecto->creado_por;
            }

            // 2️⃣ Intentar obtener líder del primer equipo
            $equipo = $proyecto->equipos()->with('lider')->first();
            if ($equipo && isset($equipo->lider_id) && $equipo->lider_id) {
                Log::info("✅ Responsable asignado: Líder del equipo (ID: {$equipo->lider_id})");
                return $equipo->lider_id;
            }

            // 3️⃣ Intentar obtener primer desarrollador del equipo
            if ($equipo) {
                $miembroDesarrollador = $equipo->miembros()
                    ->where('rol', 'Desarrollador')
                    ->first();
                
                if ($miembroDesarrollador && isset($miembroDesarrollador->id_usuario)) {
                    Log::info("✅ Responsable asignado: Desarrollador del equipo (ID: {$miembroDesarrollador->id_usuario})");
                    return $miembroDesarrollador->id_usuario;
                }
            }

            // 4️⃣ Asignar a quien aprobó la solicitud (último recurso antes de NULL)
            if ($this->solicitudCambio->aprobado_por) {
                Log::info("✅ Responsable asignado: Aprobador de la solicitud (ID: {$this->solicitudCambio->aprobado_por})");
                return $this->solicitudCambio->aprobado_por;
            }

        } catch (\Exception $e) {
            Log::warning('⚠️ Error al determinar responsable por defecto: ' . $e->getMessage());
        }

        // 5️⃣ No se pudo asignar automáticamente - el PM debe hacerlo manualmente
        Log::warning('⚠️ No se pudo asignar responsable automáticamente. La tarea debe ser asignada manualmente desde el tablero.');
        return null;
    }
}
