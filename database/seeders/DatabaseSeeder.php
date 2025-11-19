<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🚀 Iniciando población de base de datos del SGCS...');
        $this->command->info('');

        $this->call([
            MetodologiasSeeder::class,          // 1. Metodologías (Scrum y Cascada) y sus Fases
            PlantillasECSeeder::class,          // 2. Plantillas de EC por metodología
            RolesSeeder::class,                 // 3. Roles profesionales del SGCS
            CCBRolesSeeder::class,              // 3b. Roles específicos para CCB (si faltan)
            UsuarioSeeder::class,               // 4. Usuarios profesionales para demo
            DemoCompletaSeeder::class,          // 5. DEMO COMPLETA: Proyectos, Equipos, ECs, Tareas y CCBs
            SprintsSeeder::class,               // 6. Sprints para proyectos Scrum
            TareasScrumCompletasSeeder::class,  // 7. Tareas Scrum completas con story points y ECs
        ]);

        $this->command->info('');
        $this->command->info('✅ ¡Base de datos poblada exitosamente!');
        $this->command->info('');
        $this->command->info('📊 RESUMEN DE LA DEMOSTRACIÓN:');
        $this->command->info('   • 2 Metodologías: Scrum y Cascada');
        $this->command->info('   • 2 Proyectos principales completos:');
        $this->command->info('     - E-Commerce Platform (Scrum)');
        $this->command->info('     - Sistema ERP Corporativo (Cascada)');
        $this->command->info('   • 3 Proyectos adicionales');
        $this->command->info('   • 19 Usuarios profesionales');
        $this->command->info('   • 12 Roles del SGCS');
        $this->command->info('   • Elementos de Configuración con relaciones');
        $this->command->info('   • Tareas asignadas por fase');
        $this->command->info('   • Sprints configurados para proyectos Scrum');
        $this->command->info('   • Comités de Control de Cambios (CCB)');
        $this->command->info('');
    }
}
