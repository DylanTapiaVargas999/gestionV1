<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Proyecto;
use App\Models\Sprint;

class SprintsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🏃 Creando Sprints para proyectos Scrum...');

        // Obtener proyectos Scrum
        $proyectosScrum = Proyecto::whereHas('metodologia', function ($query) {
            $query->where('nombre', 'Scrum');
        })->get();

        foreach ($proyectosScrum as $proyecto) {
            $this->command->info("  📁 Proyecto: {$proyecto->nombre}");

            // Crear 3 sprints por proyecto
            $fechaInicio = now()->subDays(60); // Empezar hace 60 días

            for ($i = 1; $i <= 3; $i++) {
                $sprint = Sprint::create([
                    'id_proyecto' => $proyecto->id,
                    'nombre' => "Sprint $i",
                    'objetivo' => $this->generarObjetivo($proyecto->nombre, $i),
                    'fecha_inicio' => $fechaInicio->copy(),
                    'fecha_fin' => $fechaInicio->copy()->addDays(14),
                    'velocidad_estimada' => 20 + ($i * 5), // 25, 30, 35 story points
                    'velocidad_real' => $i === 1 ? 22 : ($i === 2 ? 28 : null), // Solo sprints completados tienen velocidad real
                    'estado' => $i === 1 ? 'completado' : ($i === 2 ? 'completado' : 'activo'),
                    'observaciones' => $i === 3 ? 'Sprint actual en progreso' : "Sprint $i completado exitosamente",
                ]);

                $this->command->info("    ✅ {$sprint->nombre} creado ({$sprint->estado})");

                // Mover fecha para próximo sprint
                $fechaInicio->addDays(14);
            }

            $this->command->info('');
        }

        // Asociar tareas existentes al último sprint (actual)
        $this->command->info('🔗 Asociando tareas existentes a sprints...');

        foreach ($proyectosScrum as $proyecto) {
            $sprintActual = Sprint::where('id_proyecto', $proyecto->id)
                ->where('estado', 'activo')
                ->first();

            if ($sprintActual) {
                // Asociar tareas del Product Backlog al sprint actual
                $tareasActualizadas = DB::table('tareas_proyecto')
                    ->where('id_proyecto', $proyecto->id)
                    ->whereNull('id_sprint') // Solo las que no tienen sprint
                    ->limit(5) // Asignar solo 5 tareas al sprint actual
                    ->update(['id_sprint' => $sprintActual->id_sprint]);

                $this->command->info("  ✅ {$proyecto->nombre}: $tareasActualizadas tareas asignadas a {$sprintActual->nombre}");
            }
        }

        $this->command->info('');
        $this->command->info('✅ Sprints creados exitosamente');
    }

    /**
     * Generar objetivo para el sprint
     */
    private function generarObjetivo(string $nombreProyecto, int $numeroSprint): string
    {
        $objetivos = [
            1 => "Establecer la arquitectura base y funcionalidades core del proyecto",
            2 => "Implementar módulos principales y casos de uso prioritarios",
            3 => "Completar integraciones, optimización y preparación para release",
        ];

        return $objetivos[$numeroSprint] ?? "Continuar desarrollo de $nombreProyecto";
    }
}
