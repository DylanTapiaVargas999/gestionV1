<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CCBRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'nombre' => 'Líder del Proyecto (Gestor de Configuración)',
                'descripcion' => 'Presidente del CCB: convoca reuniones, valida decisiones y coordina acciones.',
                'metodologia_id' => null,
                'es_para_ccb' => true,
            ],
            [
                'nombre' => 'Arquitecto de Software',
                'descripcion' => 'Evaluador técnico: analiza impacto en la arquitectura y dependencias.',
                'metodologia_id' => null,
                'es_para_ccb' => true,
            ],
            [
                'nombre' => 'Desarrollador Senior / Líder Técnico',
                'descripcion' => 'Responsable de implementación: estima esfuerzo y valida la viabilidad técnica.',
                'metodologia_id' => null,
                'es_para_ccb' => true,
            ],
            [
                'nombre' => 'Tester / QA',
                'descripcion' => 'Asegurador de calidad: evalúa impacto en pruebas y validez tras implementación.',
                'metodologia_id' => null,
                'es_para_ccb' => true,
            ],
            [
                'nombre' => 'Documentador / Analista funcional',
                'descripcion' => 'Encargado de trazabilidad y documentación del cambio.',
                'metodologia_id' => null,
                'es_para_ccb' => true,
            ],
            [
                'nombre' => 'Auditor',
                'descripcion' => 'Control y seguimiento: verifica cumplimiento de procesos y políticas.',
                'metodologia_id' => null,
                'es_para_ccb' => true,
            ],
        ];

        foreach ($roles as $rol) {
            DB::table('roles')->updateOrInsert(
                ['nombre' => $rol['nombre']],
                $rol
            );
        }
    }
}
