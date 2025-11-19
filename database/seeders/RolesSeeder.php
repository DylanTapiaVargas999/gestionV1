<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener IDs de metodologías
        $scrumId = DB::table('metodologias')->where('nombre', 'Scrum')->value('id_metodologia');
        $cascadaId = DB::table('metodologias')->where('nombre', 'Cascada')->value('id_metodologia');

        // Roles GENÉRICOS (sin metodología específica)
        $rolesGenericos = [
            [
                'nombre' => 'Gestor de Configuración',
                'descripcion' => 'Responsable de la gestión de configuración del software (SCM Manager)',
                'metodologia_id' => null
            ],
            [
                'nombre' => 'Administrador CCB',
                'descripcion' => 'Administrador del Comité de Control de Cambios (CCB Administrator)',
                'metodologia_id' => null
            ],
            [
                'nombre' => 'Auditor de Configuración',
                'descripcion' => 'Auditor de cumplimiento de procesos de gestión de configuración',
                'metodologia_id' => null
            ],
            [
                'nombre' => 'Release Manager',
                'descripcion' => 'Gestor de liberaciones y despliegues a producción',
                'metodologia_id' => null
            ]
        ];

        // Roles específicos de SCRUM
        $rolesScrum = [
            [
                'nombre' => 'Product Owner',
                'descripcion' => 'Dueño del producto, define prioridades y requisitos (PO)',
                'metodologia_id' => $scrumId
            ],
            [
                'nombre' => 'Scrum Master',
                'descripcion' => 'Facilitador del proceso Scrum y eliminador de impedimentos',
                'metodologia_id' => $scrumId
            ],
            [
                'nombre' => 'Desarrollador Scrum',
                'descripcion' => 'Miembro del equipo de desarrollo en Scrum',
                'metodologia_id' => $scrumId
            ],
            [
                'nombre' => 'Tester Scrum',
                'descripcion' => 'Responsable de pruebas en equipo Scrum',
                'metodologia_id' => $scrumId
            ]
        ];

        // Roles específicos de CASCADA
        $rolesCascada = [
            [
                'nombre' => 'Líder de Proyecto',
                'descripcion' => 'Líder técnico y gestor del proyecto en metodología Cascada',
                'metodologia_id' => $cascadaId
            ],
            [
                'nombre' => 'Arquitecto de Software',
                'descripcion' => 'Diseñador de arquitectura y decisiones técnicas estratégicas',
                'metodologia_id' => $cascadaId
            ],
            [
                'nombre' => 'Analista de Sistemas',
                'descripcion' => 'Analista de requisitos y especificaciones',
                'metodologia_id' => $cascadaId
            ],
            [
                'nombre' => 'Desarrollador Senior',
                'descripcion' => 'Desarrollador con experiencia y capacidad de revisión de código',
                'metodologia_id' => $cascadaId
            ],
            [
                'nombre' => 'Desarrollador',
                'descripcion' => 'Desarrollador de software con acceso a elementos de configuración',
                'metodologia_id' => $cascadaId
            ],
            [
                'nombre' => 'Analista QA',
                'descripcion' => 'Analista de aseguramiento de calidad (Quality Assurance)',
                'metodologia_id' => $cascadaId
            ],
            [
                'nombre' => 'Tester',
                'descripcion' => 'Responsable de pruebas y validación de software',
                'metodologia_id' => $cascadaId
            ]
        ];

        // Insertar todos los roles
        $todosLosRoles = array_merge($rolesGenericos, $rolesScrum, $rolesCascada);

        foreach ($todosLosRoles as $rol) {
            DB::table('roles')->updateOrInsert(
                ['nombre' => $rol['nombre']],
                $rol
            );
        }
    }
}
