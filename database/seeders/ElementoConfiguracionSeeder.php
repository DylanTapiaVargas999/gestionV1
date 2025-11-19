<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ElementoConfiguracion;
use App\Models\Proyecto;
use App\Models\RelacionEC;

class ElementoConfiguracionSeeder extends Seeder
{
    public function run(): void
    {
        $scrumProject = Proyecto::where('codigo', 'SCRUM-DEMO')->first();
        $cascadaProject = Proyecto::where('codigo', 'CASCADA-DEMO')->first();
        if (!$scrumProject || !$cascadaProject) return;

        // --- PROYECTO SCRUM: ECs ---
        $scrumECs = [
            ['codigo' => 'SCRUM-EC-001', 'titulo' => 'Product Backlog', 'descripcion' => 'Lista priorizada de historias de usuario', 'tipo' => 'DOCUMENTO'],
            ['codigo' => 'SCRUM-EC-002', 'titulo' => 'User Story - Login', 'descripcion' => 'Historia: Como usuario quiero iniciar sesión', 'tipo' => 'DOCUMENTO'],
            ['codigo' => 'SCRUM-EC-003', 'titulo' => 'User Story - CRUD Pedidos', 'descripcion' => 'Historia: Como usuario quiero gestionar pedidos', 'tipo' => 'DOCUMENTO'],
            ['codigo' => 'SCRUM-EC-004', 'titulo' => 'API Auth Module', 'descripcion' => 'Módulo de autenticación con JWT', 'tipo' => 'CODIGO'],
            ['codigo' => 'SCRUM-EC-005', 'titulo' => 'Frontend Login Component', 'descripcion' => 'Componente React para login', 'tipo' => 'CODIGO'],
            ['codigo' => 'SCRUM-EC-006', 'titulo' => 'Pedidos Controller', 'descripcion' => 'Controlador para gestión de pedidos', 'tipo' => 'CODIGO'],
            ['codigo' => 'SCRUM-EC-007', 'titulo' => 'Test Suite - Auth', 'descripcion' => 'Pruebas unitarias para autenticación', 'tipo' => 'CODIGO'],
            ['codigo' => 'SCRUM-EC-008', 'titulo' => 'Sprint 1 - Docs', 'descripcion' => 'Documentación del Sprint 1', 'tipo' => 'DOCUMENTO'],
        ];
        $scrumIds = [];
        foreach ($scrumECs as $e) {
            $ec = ElementoConfiguracion::firstOrCreate(
                ['codigo_ec' => $e['codigo']],
                [
                    'codigo_ec' => $e['codigo'],
                    'titulo' => $e['titulo'],
                    'descripcion' => $e['descripcion'],
                    'tipo' => $e['tipo'],
                    'proyecto_id' => $scrumProject->id,
                ]
            );
            $scrumIds[$e['titulo']] = $ec->id;
        }

        // Relaciones Scrum
        RelacionEC::firstOrCreate([
            'desde_ec' => $scrumIds['User Story - Login'],
            'hacia_ec' => $scrumIds['Product Backlog'],
        ], [
            'tipo_relacion' => 'DERIVADO_DE',
            'nota' => 'User Story deriva del Product Backlog',
        ]);
        RelacionEC::firstOrCreate([
            'desde_ec' => $scrumIds['API Auth Module'],
            'hacia_ec' => $scrumIds['User Story - Login'],
        ], [
            'tipo_relacion' => 'DEPENDE_DE',
            'nota' => 'API Auth implementa la User Story de Login',
        ]);
        RelacionEC::firstOrCreate([
            'desde_ec' => $scrumIds['Frontend Login Component'],
            'hacia_ec' => $scrumIds['API Auth Module'],
        ], [
            'tipo_relacion' => 'DEPENDE_DE',
            'nota' => 'Frontend depende de la API Auth',
        ]);

        // --- PROYECTO CASCADA: ECs ---
        $cascadaECs = [
            ['codigo' => 'CASC-EC-001', 'titulo' => 'Documento de Requisitos', 'descripcion' => 'SRS - Especificación de Requisitos de Software', 'tipo' => 'DOCUMENTO'],
            ['codigo' => 'CASC-EC-002', 'titulo' => 'Diseño Arquitectónico', 'descripcion' => 'SAD - Especificación de Arquitectura de Software', 'tipo' => 'DOCUMENTO'],
            ['codigo' => 'CASC-EC-003', 'titulo' => 'Diagrama ER', 'descripcion' => 'Diagrama Entidad-Relación de la base de datos', 'tipo' => 'DOCUMENTO'],
            ['codigo' => 'CASC-EC-004', 'titulo' => 'Manual de Usuario', 'descripcion' => 'Guía completa para el usuario final', 'tipo' => 'DOCUMENTO'],
            ['codigo' => 'CASC-EC-005', 'titulo' => 'Módulo Core ERP', 'descripcion' => 'Módulo principal del sistema ERP', 'tipo' => 'CODIGO'],
            ['codigo' => 'CASC-EC-006', 'titulo' => 'Módulo Inventario', 'descripcion' => 'Gestión de inventario y almacén', 'tipo' => 'CODIGO'],
            ['codigo' => 'CASC-EC-007', 'titulo' => 'Script BD Inicial', 'descripcion' => 'Script de creación de base de datos', 'tipo' => 'SCRIPT_BD'],
            ['codigo' => 'CASC-EC-008', 'titulo' => 'Plan de Pruebas', 'descripcion' => 'Documento de estrategia de testing', 'tipo' => 'DOCUMENTO'],
        ];
        $cascadaIds = [];
        foreach ($cascadaECs as $e) {
            $ec = ElementoConfiguracion::firstOrCreate(
                ['codigo_ec' => $e['codigo']],
                [
                    'codigo_ec' => $e['codigo'],
                    'titulo' => $e['titulo'],
                    'descripcion' => $e['descripcion'],
                    'tipo' => $e['tipo'],
                    'proyecto_id' => $cascadaProject->id,
                ]
            );
            $cascadaIds[$e['titulo']] = $ec->id;
        }

        // Relaciones Cascada
        RelacionEC::firstOrCreate([
            'desde_ec' => $cascadaIds['Diseño Arquitectónico'],
            'hacia_ec' => $cascadaIds['Documento de Requisitos'],
        ], [
            'tipo_relacion' => 'DERIVADO_DE',
            'nota' => 'El diseño deriva de los requisitos',
        ]);
        RelacionEC::firstOrCreate([
            'desde_ec' => $cascadaIds['Diagrama ER'],
            'hacia_ec' => $cascadaIds['Diseño Arquitectónico'],
        ], [
            'tipo_relacion' => 'DERIVADO_DE',
            'nota' => 'El diagrama ER deriva del diseño arquitectónico',
        ]);
        RelacionEC::firstOrCreate([
            'desde_ec' => $cascadaIds['Módulo Core ERP'],
            'hacia_ec' => $cascadaIds['Diseño Arquitectónico'],
        ], [
            'tipo_relacion' => 'DEPENDE_DE',
            'nota' => 'El módulo Core depende del diseño',
        ]);
        RelacionEC::firstOrCreate([
            'desde_ec' => $cascadaIds['Script BD Inicial'],
            'hacia_ec' => $cascadaIds['Diagrama ER'],
        ], [
            'tipo_relacion' => 'DERIVADO_DE',
            'nota' => 'El script SQL deriva del diagrama ER',
        ]);
    }
}
