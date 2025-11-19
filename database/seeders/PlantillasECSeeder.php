<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlantillasECSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener IDs de metodologías
        $scrum = DB::table('metodologias')->where('nombre', 'Scrum')->first();
        $cascada = DB::table('metodologias')->where('nombre', 'Cascada')->first();

        // ============================================
        // PLANTILLAS PARA SCRUM
        // ============================================
        if ($scrum) {
            $plantillasScrum = [
                [
                    'metodologia_id' => $scrum->id_metodologia,
                    'nombre' => 'Product Backlog',
                    'tipo' => 'DOCUMENTO',
                    'descripcion' => 'Repositorio central de historias de usuario y requisitos del producto',
                    'orden' => 1,
                    'es_recomendado' => true,
                    'tarea_nombre' => 'Crear historias de usuario iniciales',
                    'tarea_descripcion' => 'Definir las primeras historias de usuario del proyecto basadas en los requisitos del Product Owner',
                    'porcentaje_inicio' => 0.00,
                    'porcentaje_fin' => 20.00,
                    'relaciones' => null, // Base del proyecto, no depende de nada
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'metodologia_id' => $scrum->id_metodologia,
                    'nombre' => 'Sprint Backlog',
                    'tipo' => 'DOCUMENTO',
                    'descripcion' => 'Planificación y seguimiento de sprints del proyecto',
                    'orden' => 2,
                    'es_recomendado' => true,
                    'tarea_nombre' => 'Planificar primer sprint',
                    'tarea_descripcion' => 'Seleccionar historias de usuario del Product Backlog y planificar el primer sprint',
                    'porcentaje_inicio' => 20.00,
                    'porcentaje_fin' => 40.00,
                    'relaciones' => json_encode([['nombre' => 'Product Backlog', 'tipo' => 'DEPENDE_DE']]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'metodologia_id' => $scrum->id_metodologia,
                    'nombre' => 'Repositorio de Código',
                    'tipo' => 'CODIGO',
                    'descripcion' => 'Control de versiones del código fuente del proyecto',
                    'orden' => 3,
                    'es_recomendado' => true,
                    'tarea_nombre' => 'Configurar repositorio Git',
                    'tarea_descripcion' => 'Inicializar repositorio Git, configurar branches (main, develop) y establecer reglas de commit',
                    'porcentaje_inicio' => 0.00,
                    'porcentaje_fin' => 10.00,
                    'relaciones' => null, // Base técnica, no depende
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'metodologia_id' => $scrum->id_metodologia,
                    'nombre' => 'Documentación Técnica',
                    'tipo' => 'DOCUMENTO',
                    'descripcion' => 'Documentación del sistema, APIs y guías de desarrollo',
                    'orden' => 4,
                    'es_recomendado' => true,
                    'tarea_nombre' => 'Crear README y documentación inicial',
                    'tarea_descripcion' => 'Documentar configuración del proyecto, estructura de carpetas y guías para desarrolladores',
                    'porcentaje_inicio' => 40.00,
                    'porcentaje_fin' => 60.00,
                    'relaciones' => json_encode([['nombre' => 'Repositorio de Código', 'tipo' => 'REFERENCIA']]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'metodologia_id' => $scrum->id_metodologia,
                    'nombre' => 'Definition of Done (DoD)',
                    'tipo' => 'DOCUMENTO',
                    'descripcion' => 'Criterios de aceptación y definición de "terminado" para el equipo',
                    'orden' => 5,
                    'es_recomendado' => false, // Opcional
                    'tarea_nombre' => 'Definir criterios de DoD del equipo',
                    'tarea_descripcion' => 'Establecer los criterios que debe cumplir una historia de usuario para considerarse terminada',
                    'porcentaje_inicio' => 0.00,
                    'porcentaje_fin' => 15.00,
                    'relaciones' => json_encode([['nombre' => 'Product Backlog', 'tipo' => 'REFERENCIA']]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'metodologia_id' => $scrum->id_metodologia,
                    'nombre' => 'Retrospectivas',
                    'tipo' => 'DOCUMENTO',
                    'descripcion' => 'Registro de retrospectivas y acciones de mejora del equipo',
                    'orden' => 6,
                    'es_recomendado' => false, // Opcional
                    'tarea_nombre' => 'Preparar template de retrospectivas',
                    'tarea_descripcion' => 'Crear formato estándar para documentar retrospectivas de cada sprint',
                    'porcentaje_inicio' => 60.00,
                    'porcentaje_fin' => 80.00,
                    'relaciones' => json_encode([['nombre' => 'Sprint Backlog', 'tipo' => 'REFERENCIA']]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ];

            foreach ($plantillasScrum as $plantilla) {
                DB::table('plantillas_ec')->insertOrIgnore($plantilla);
            }
        }

        // ============================================
        // PLANTILLAS PARA CASCADA
        // ============================================
        if ($cascada) {
            $plantillasCascada = [
                [
                    'metodologia_id' => $cascada->id_metodologia,
                    'nombre' => 'Documento de Requisitos (SRS)',
                    'tipo' => 'DOCUMENTO',
                    'descripcion' => 'Especificación de requisitos del sistema (Software Requirements Specification)',
                    'orden' => 1,
                    'es_recomendado' => true,
                    'tarea_nombre' => 'Recopilar y documentar requisitos',
                    'tarea_descripcion' => 'Realizar entrevistas con stakeholders y documentar todos los requisitos funcionales y no funcionales',
                    'porcentaje_inicio' => 0.00,
                    'porcentaje_fin' => 15.00,
                    'relaciones' => null, // Base del proyecto
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'metodologia_id' => $cascada->id_metodologia,
                    'nombre' => 'Diseño de Arquitectura',
                    'tipo' => 'DOCUMENTO',
                    'descripcion' => 'Diseño técnico y arquitectónico del sistema',
                    'orden' => 2,
                    'es_recomendado' => true,
                    'tarea_nombre' => 'Diseñar arquitectura del sistema',
                    'tarea_descripcion' => 'Crear diagramas UML, arquitectura de componentes, diseño de base de datos y especificaciones técnicas',
                    'porcentaje_inicio' => 15.00,
                    'porcentaje_fin' => 30.00,
                    'relaciones' => json_encode([['nombre' => 'Documento de Requisitos (SRS)', 'tipo' => 'DEPENDE_DE']]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'metodologia_id' => $cascada->id_metodologia,
                    'nombre' => 'Código Fuente',
                    'tipo' => 'CODIGO',
                    'descripcion' => 'Implementación del código fuente del sistema',
                    'orden' => 3,
                    'es_recomendado' => true,
                    'tarea_nombre' => 'Configurar estructura del proyecto',
                    'tarea_descripcion' => 'Inicializar proyecto, configurar dependencias y establecer estructura de carpetas según diseño',
                    'porcentaje_inicio' => 30.00,
                    'porcentaje_fin' => 60.00,
                    'relaciones' => json_encode([['nombre' => 'Diseño de Arquitectura', 'tipo' => 'DEPENDE_DE']]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'metodologia_id' => $cascada->id_metodologia,
                    'nombre' => 'Plan de Pruebas',
                    'tipo' => 'DOCUMENTO',
                    'descripcion' => 'Estrategia de testing, casos de prueba y matriz de trazabilidad',
                    'orden' => 4,
                    'es_recomendado' => true,
                    'tarea_nombre' => 'Elaborar plan de pruebas',
                    'tarea_descripcion' => 'Definir estrategia de testing, diseñar casos de prueba y crear matriz de trazabilidad de requisitos',
                    'porcentaje_inicio' => 50.00,
                    'porcentaje_fin' => 70.00,
                    'relaciones' => json_encode([
                        ['nombre' => 'Documento de Requisitos (SRS)', 'tipo' => 'REFERENCIA'],
                        ['nombre' => 'Código Fuente', 'tipo' => 'DEPENDE_DE']
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'metodologia_id' => $cascada->id_metodologia,
                    'nombre' => 'Manual de Usuario',
                    'tipo' => 'DOCUMENTO',
                    'descripcion' => 'Documentación para usuarios finales del sistema',
                    'orden' => 5,
                    'es_recomendado' => true,
                    'tarea_nombre' => 'Redactar manual de usuario',
                    'tarea_descripcion' => 'Crear guías de uso, tutoriales y documentación para usuarios finales del sistema',
                    'porcentaje_inicio' => 70.00,
                    'porcentaje_fin' => 85.00,
                    'relaciones' => json_encode([['nombre' => 'Código Fuente', 'tipo' => 'REFERENCIA']]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'metodologia_id' => $cascada->id_metodologia,
                    'nombre' => 'Scripts de Base de Datos',
                    'tipo' => 'SCRIPT_BD',
                    'descripcion' => 'Scripts SQL de creación y migración de base de datos',
                    'orden' => 6,
                    'es_recomendado' => true,
                    'tarea_nombre' => 'Crear scripts de base de datos',
                    'tarea_descripcion' => 'Desarrollar scripts DDL/DML para creación de tablas, procedimientos y carga inicial de datos',
                    'porcentaje_inicio' => 25.00,
                    'porcentaje_fin' => 35.00,
                    'relaciones' => json_encode([['nombre' => 'Diseño de Arquitectura', 'tipo' => 'DERIVADO_DE']]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'metodologia_id' => $cascada->id_metodologia,
                    'nombre' => 'Plan de Despliegue',
                    'tipo' => 'DOCUMENTO',
                    'descripcion' => 'Estrategia y procedimientos para despliegue en producción',
                    'orden' => 7,
                    'es_recomendado' => false, // Opcional
                    'tarea_nombre' => 'Elaborar plan de despliegue',
                    'tarea_descripcion' => 'Documentar procedimientos de instalación, configuración y despliegue en ambientes productivos',
                    'porcentaje_inicio' => 85.00,
                    'porcentaje_fin' => 95.00,
                    'relaciones' => json_encode([
                        ['nombre' => 'Código Fuente', 'tipo' => 'REFERENCIA'],
                        ['nombre' => 'Scripts de Base de Datos', 'tipo' => 'REFERENCIA']
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'metodologia_id' => $cascada->id_metodologia,
                    'nombre' => 'Acta de Aceptación',
                    'tipo' => 'DOCUMENTO',
                    'descripcion' => 'Documento formal de aceptación del proyecto por el cliente',
                    'orden' => 8,
                    'es_recomendado' => false, // Opcional
                    'tarea_nombre' => 'Preparar acta de aceptación',
                    'tarea_descripcion' => 'Preparar documento formal para firma de aceptación del cliente',
                    'porcentaje_inicio' => 95.00,
                    'porcentaje_fin' => 100.00,
                    'relaciones' => json_encode([
                        ['nombre' => 'Plan de Pruebas', 'tipo' => 'REQUERIDO_POR'],
                        ['nombre' => 'Manual de Usuario', 'tipo' => 'REQUERIDO_POR']
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ];

            foreach ($plantillasCascada as $plantilla) {
                DB::table('plantillas_ec')->insertOrIgnore($plantilla);
            }
        }
    }
}
