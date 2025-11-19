<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Proyecto;
use App\Models\TareaProyecto;
use App\Models\Sprint;
use App\Models\FaseMetodologia;
use App\Models\ElementoConfiguracion;
use App\Models\Usuario;
use Carbon\Carbon;

class TareasScrumCompletasSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('📝 Creando tareas Scrum completas...');

        $proyecto = Proyecto::where('nombre', 'E-Commerce Platform')->first();

        if (!$proyecto) {
            $this->command->error('❌ Proyecto E-Commerce Platform no encontrado');
            return;
        }

        // Obtener fases (del tablero, NO ceremonias)
        $faseInProgress = FaseMetodologia::where('nombre_fase', 'In Progress')->first();
        $faseInReview = FaseMetodologia::where('nombre_fase', 'In Review')->first();
        $faseDone = FaseMetodologia::where('nombre_fase', 'Done')->first();
        $faseProductBacklog = FaseMetodologia::where('nombre_fase', 'Product Backlog')->first();

        // Obtener sprints
        $sprint1 = Sprint::where('id_proyecto', $proyecto->id)->where('nombre', 'Sprint 1')->first();
        $sprint2 = Sprint::where('id_proyecto', $proyecto->id)->where('nombre', 'Sprint 2')->first();
        $sprint3 = Sprint::where('id_proyecto', $proyecto->id)->where('nombre', 'Sprint 3')->first();

        // Obtener usuarios
        $admin = Usuario::where('correo', 'admin@sgcs.com')->first();
        $dev1 = Usuario::where('correo', 'dev1@sgcs.com')->first();
        $dev2 = Usuario::where('correo', 'dev2@sgcs.com')->first();
        $dev3 = Usuario::where('correo', 'dev3@sgcs.com')->first();

        // Eliminar tareas existentes del proyecto
        TareaProyecto::where('id_proyecto', $proyecto->id)->delete();
        $this->command->info('  🗑️  Tareas anteriores eliminadas');

        // CREAR ELEMENTOS DE CONFIGURACIÓN
        $ecs = [];

        $ecs['auth'] = ElementoConfiguracion::firstOrCreate(
            ['codigo_ec' => 'ECOM-AUTH-001'],
            [
                'titulo' => 'Módulo de Autenticación',
                'descripcion' => 'Sistema de registro, login y JWT para autenticación de usuarios',
                'proyecto_id' => $proyecto->id,
                'tipo' => 'CODIGO',
                'estado' => 'APROBADO',
                'creado_por' => $admin->id,
            ]
        );

        $ecs['products'] = ElementoConfiguracion::firstOrCreate(
            ['codigo_ec' => 'ECOM-PROD-001'],
            [
                'titulo' => 'Módulo de Productos',
                'descripcion' => 'CRUD completo de productos con categorías e inventario',
                'proyecto_id' => $proyecto->id,
                'tipo' => 'CODIGO',
                'estado' => 'APROBADO',
                'creado_por' => $admin->id,
            ]
        );

        $ecs['cart'] = ElementoConfiguracion::firstOrCreate(
            ['codigo_ec' => 'ECOM-CART-001'],
            [
                'titulo' => 'Módulo de Carrito',
                'descripcion' => 'Carrito de compras con cálculo de totales y sesión persistente',
                'proyecto_id' => $proyecto->id,
                'tipo' => 'CODIGO',
                'estado' => 'BORRADOR',
                'creado_por' => $admin->id,
            ]
        );

        $ecs['payment'] = ElementoConfiguracion::firstOrCreate(
            ['codigo_ec' => 'ECOM-PAY-001'],
            [
                'titulo' => 'Integración de Pagos',
                'descripcion' => 'Integración con Stripe para procesamiento de pagos',
                'proyecto_id' => $proyecto->id,
                'tipo' => 'CODIGO',
                'estado' => 'EN_REVISION',
                'creado_por' => $admin->id,
            ]
        );

        $ecs['orders'] = ElementoConfiguracion::firstOrCreate(
            ['codigo_ec' => 'ECOM-ORD-001'],
            [
                'titulo' => 'Módulo de Órdenes',
                'descripcion' => 'Gestión completa de órdenes de compra y estados',
                'proyecto_id' => $proyecto->id,
                'tipo' => 'CODIGO',
                'estado' => 'PENDIENTE',
                'creado_por' => $admin->id,
            ]
        );

        $ecs['analytics'] = ElementoConfiguracion::firstOrCreate(
            ['codigo_ec' => 'ECOM-ANALYTICS-001'],
            [
                'titulo' => 'Dashboard de Analytics',
                'descripcion' => 'Reportes y métricas de ventas, productos y usuarios',
                'proyecto_id' => $proyecto->id,
                'tipo' => 'CODIGO',
                'estado' => 'PENDIENTE',
                'creado_por' => $admin->id,
            ]
        );

        $this->command->info('  ✅ ' . count($ecs) . ' Elementos de Configuración creados');

        // CREAR TAREAS DEL SPRINT 1 (Completado)
        $tareasSprin1 = [
            [
                'nombre' => 'US-001: Implementar registro de usuarios',
                'descripcion' => 'Como usuario nuevo, quiero registrarme en la plataforma para poder crear una cuenta.',
                'fase' => $faseDone,
                'sprint' => $sprint1,
                'ec' => $ecs['auth'],
                'responsable' => $dev1,
                'story_points' => 5,
                'prioridad' => 9,
                'estado' => 'Done',
            ],
            [
                'nombre' => 'US-002: Implementar login con JWT',
                'descripcion' => 'Como usuario registrado, quiero iniciar sesión con mi email y contraseña.',
                'fase' => $faseDone,
                'sprint' => $sprint1,
                'ec' => $ecs['auth'],
                'responsable' => $dev2,
                'story_points' => 8,
                'prioridad' => 9,
                'estado' => 'Done',
            ],
            [
                'nombre' => 'US-003: Crear CRUD de productos',
                'descripcion' => 'Como administrador, quiero gestionar productos (crear, leer, actualizar, eliminar).',
                'fase' => $faseDone,
                'sprint' => $sprint1,
                'ec' => $ecs['products'],
                'responsable' => $dev3,
                'story_points' => 13,
                'prioridad' => 8,
                'estado' => 'Done',
            ],
        ];

        foreach ($tareasSprin1 as $tareaData) {
            TareaProyecto::create([
                'id_proyecto' => $proyecto->id,
                'id_fase' => $tareaData['fase']->id_fase,
                'id_sprint' => $tareaData['sprint']->id_sprint,
                'id_ec' => $tareaData['ec']->id,
                'responsable' => $tareaData['responsable']->id,
                'nombre' => $tareaData['nombre'],
                'descripcion' => $tareaData['descripcion'],
                'story_points' => $tareaData['story_points'],
                'prioridad' => $tareaData['prioridad'],
                'estado' => $tareaData['estado'],
                'fecha_inicio' => Carbon::now()->subDays(50),
                'fecha_fin' => Carbon::now()->subDays(37),
                'creado_por' => $admin->id,
            ]);
        }

        $this->command->info('  ✅ Sprint 1: ' . count($tareasSprin1) . ' tareas completadas');

        // CREAR TAREAS DEL SPRINT 2 (Completado)
        $tareasSprint2 = [
            [
                'nombre' => 'US-004: Implementar carrito de compras',
                'descripcion' => 'Como cliente, quiero agregar productos a mi carrito para poder comprarlos.',
                'fase' => $faseDone,
                'sprint' => $sprint2,
                'ec' => $ecs['cart'],
                'responsable' => $dev1,
                'story_points' => 13,
                'prioridad' => 8,
                'estado' => 'Done',
            ],
            [
                'nombre' => 'US-005: Calcular totales del carrito',
                'descripcion' => 'Como cliente, quiero ver el total de mi carrito incluyendo impuestos y envío.',
                'fase' => $faseDone,
                'sprint' => $sprint2,
                'ec' => $ecs['cart'],
                'responsable' => $dev2,
                'story_points' => 5,
                'prioridad' => 7,
                'estado' => 'Done',
            ],
        ];

        foreach ($tareasSprint2 as $tareaData) {
            TareaProyecto::create([
                'id_proyecto' => $proyecto->id,
                'id_fase' => $tareaData['fase']->id_fase,
                'id_sprint' => $tareaData['sprint']->id_sprint,
                'id_ec' => $tareaData['ec']->id,
                'responsable' => $tareaData['responsable']->id,
                'nombre' => $tareaData['nombre'],
                'descripcion' => $tareaData['descripcion'],
                'story_points' => $tareaData['story_points'],
                'prioridad' => $tareaData['prioridad'],
                'estado' => $tareaData['estado'],
                'fecha_inicio' => Carbon::now()->subDays(36),
                'fecha_fin' => Carbon::now()->subDays(23),
                'creado_por' => $admin->id,
            ]);
        }

        $this->command->info('  ✅ Sprint 2: ' . count($tareasSprint2) . ' tareas completadas');

        // CREAR TAREAS DEL SPRINT 3 (Activo)
        $tareasSprint3 = [
            [
                'nombre' => 'US-006: Integrar pasarela de pagos Stripe',
                'descripcion' => 'Como cliente, quiero pagar con tarjeta de crédito usando Stripe.',
                'fase' => $faseInReview,
                'sprint' => $sprint3,
                'ec' => $ecs['payment'],
                'responsable' => $dev3,
                'story_points' => 13,
                'prioridad' => 9,
                'estado' => 'In Review',
            ],
            [
                'nombre' => 'US-007: Crear dashboard de analytics',
                'descripcion' => 'Como administrador, quiero ver reportes de ventas y métricas del negocio.',
                'fase' => $faseInProgress,
                'sprint' => $sprint3,
                'ec' => $ecs['analytics'],
                'responsable' => $dev1,
                'story_points' => 8,
                'prioridad' => 6,
                'estado' => 'In Progress',
            ],
            [
                'nombre' => 'US-008: Implementar gestión de órdenes',
                'descripcion' => 'Como administrador, quiero ver y gestionar todas las órdenes de compra.',
                'fase' => $faseInProgress,
                'sprint' => $sprint3,
                'ec' => $ecs['orders'],
                'responsable' => $dev2,
                'story_points' => 13,
                'prioridad' => 7,
                'estado' => 'In Progress',
            ],
            [
                'nombre' => 'US-009: Implementar filtros de productos',
                'descripcion' => 'Como cliente, quiero filtrar productos por categoría, precio y disponibilidad.',
                'fase' => $faseInProgress,
                'sprint' => $sprint3,
                'ec' => $ecs['products'],
                'responsable' => $dev3,
                'story_points' => 5,
                'prioridad' => 5,
                'estado' => 'In Progress',
            ],
        ];

        foreach ($tareasSprint3 as $tareaData) {
            TareaProyecto::create([
                'id_proyecto' => $proyecto->id,
                'id_fase' => $tareaData['fase']->id_fase,
                'id_sprint' => $tareaData['sprint']->id_sprint,
                'id_ec' => $tareaData['ec']->id,
                'responsable' => $tareaData['responsable']->id,
                'nombre' => $tareaData['nombre'],
                'descripcion' => $tareaData['descripcion'],
                'story_points' => $tareaData['story_points'],
                'prioridad' => $tareaData['prioridad'],
                'estado' => $tareaData['estado'],
                'fecha_inicio' => Carbon::now()->subDays(10),
                'fecha_fin' => Carbon::now()->addDays(4),
                'creado_por' => $admin->id,
            ]);
        }

        $this->command->info('  ✅ Sprint 3: ' . count($tareasSprint3) . ' tareas en progreso');

        // CREAR TAREAS EN PRODUCT BACKLOG (sin sprint asignado)
        $tareasBacklog = [
            [
                'nombre' => 'US-010: Implementar wishlist',
                'descripcion' => 'Como cliente, quiero guardar productos favoritos para comprarlos después.',
                'fase' => $faseProductBacklog,
                'sprint' => null,
                'ec' => null,
                'responsable' => null,
                'story_points' => 8,
                'prioridad' => 4,
                'estado' => 'To Do',
            ],
            [
                'nombre' => 'US-011: Sistema de reviews y ratings',
                'descripcion' => 'Como cliente, quiero calificar y comentar productos comprados.',
                'fase' => $faseProductBacklog,
                'sprint' => null,
                'ec' => null,
                'responsable' => null,
                'story_points' => 13,
                'prioridad' => 5,
                'estado' => 'To Do',
            ],
            [
                'nombre' => 'US-012: Notificaciones por email',
                'descripcion' => 'Como cliente, quiero recibir emails de confirmación de pedido y envío.',
                'fase' => $faseProductBacklog,
                'sprint' => null,
                'ec' => null,
                'responsable' => null,
                'story_points' => 5,
                'prioridad' => 6,
                'estado' => 'To Do',
            ],
        ];

        foreach ($tareasBacklog as $tareaData) {
            TareaProyecto::create([
                'id_proyecto' => $proyecto->id,
                'id_fase' => $tareaData['fase']->id_fase,
                'id_sprint' => null,
                'id_ec' => null,
                'responsable' => null,
                'nombre' => $tareaData['nombre'],
                'descripcion' => $tareaData['descripcion'],
                'story_points' => $tareaData['story_points'],
                'prioridad' => $tareaData['prioridad'],
                'estado' => $tareaData['estado'],
                'creado_por' => $admin->id,
            ]);
        }

        $this->command->info('  ✅ Product Backlog: ' . count($tareasBacklog) . ' user stories pendientes');

        // Actualizar velocidades de los sprints
        $sprint1->update(['velocidad_estimada' => array_sum(array_column($tareasSprin1, 'story_points'))]);
        $sprint2->update(['velocidad_estimada' => array_sum(array_column($tareasSprint2, 'story_points'))]);
        $sprint3->update(['velocidad_estimada' => array_sum(array_column($tareasSprint3, 'story_points'))]);

        $totalTareas = count($tareasSprin1) + count($tareasSprint2) + count($tareasSprint3) + count($tareasBacklog);
        $this->command->info("\n✅ Total: {$totalTareas} tareas Scrum creadas correctamente");
        $this->command->info('   - Sprint 1: ' . array_sum(array_column($tareasSprin1, 'story_points')) . ' story points (DONE)');
        $this->command->info('   - Sprint 2: ' . array_sum(array_column($tareasSprint2, 'story_points')) . ' story points (DONE)');
        $this->command->info('   - Sprint 3: ' . array_sum(array_column($tareasSprint3, 'story_points')) . ' story points (ACTIVO)');
        $this->command->info('   - Backlog: ' . array_sum(array_column($tareasBacklog, 'story_points')) . ' story points');
    }
}
