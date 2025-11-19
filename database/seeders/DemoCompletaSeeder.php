<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Proyecto;
use App\Models\Usuario;
use App\Models\Equipo;
use App\Models\MiembroEquipo;
use App\Models\ElementoConfiguracion;
use App\Models\VersionEc;
use App\Models\RelacionEC;
use App\Models\TareaProyecto;
use App\Models\ComiteCambio;
use App\Models\MiembroCCB;
use Carbon\Carbon;

class DemoCompletaSeeder extends Seeder
{
    /**
     * Seeder completo para demostración profesional del SGCS
     * - 2 Proyectos completos: uno Scrum y uno Cascada
     * - Equipos con miembros y roles asignados
     * - Elementos de configuración con relaciones
     * - Tareas asignadas a cada fase
     * - Comités de Control de Cambios (CCB)
     */
    public function run(): void
    {
        // ============================================
        // 1. OBTENER DATOS BASE
        // ============================================

        $scrum = DB::table('metodologias')->where('nombre', 'Scrum')->first();
        $cascada = DB::table('metodologias')->where('nombre', 'Cascada')->first();

        if (!$scrum || !$cascada) {
            $this->command->error('❌ Ejecuta primero MetodologiasSeeder');
            return;
        }

        // Obtener usuarios
        $admin = Usuario::where('correo', 'admin@sgcs.com')->first();
        $scrumMaster = Usuario::where('correo', 'sm@sgcs.com')->first();
        $productOwner = Usuario::where('correo', 'po@sgcs.com')->first();
        $pmCascada = Usuario::where('correo', 'pm@sgcs.com')->first();
        $scmManager = Usuario::where('correo', 'scm@sgcs.com')->first();

        if (!$admin || !$scrumMaster || !$productOwner) {
            $this->command->error('❌ Ejecuta primero UsuarioSeeder');
            return;
        }

        // Obtener roles
        $roles = DB::table('roles')->get()->keyBy('nombre');

        // ============================================
        // 2. CREAR PROYECTO SCRUM COMPLETO
        // ============================================

        $this->command->info('🚀 Creando Proyecto SCRUM...');

        $proyectoScrum = Proyecto::firstOrCreate(['codigo' => 'ECOM-2024'], [
            'id' => Str::uuid()->toString(),
            'nombre' => 'E-Commerce Platform',
            'descripcion' => 'Plataforma de comercio electrónico con gestión de inventario, carrito de compras, pasarela de pagos y dashboard de analytics. Desarrollado con metodología ágil Scrum.',
            'id_metodologia' => $scrum->id_metodologia,
            'fecha_inicio' => Carbon::now()->subMonths(2),
            'fecha_fin' => Carbon::now()->addMonths(4),
            'link_repositorio' => 'https://github.com/sgcs-demo/ecommerce-platform',
            'creado_por' => $admin->id,  // ADMIN es creador
        ]);

        // Crear equipo Scrum con ADMIN como líder
        $equipoScrum = Equipo::firstOrCreate(
            ['proyecto_id' => $proyectoScrum->id, 'nombre' => 'E-Commerce Development Team'],
            [
                'id' => Str::uuid()->toString(),
                'lider_id' => $admin->id,  // ADMIN es líder del equipo
            ]
        );

        // Asignar miembros al equipo Scrum (incluyendo admin)
        $miembrosScrum = [
            ['correo' => 'admin@sgcs.com', 'rol' => 'Product Owner'],  // ADMIN en el equipo
            ['correo' => 'sm@sgcs.com', 'rol' => 'Scrum Master'],
            ['correo' => 'po@sgcs.com', 'rol' => 'Product Owner'],
            ['correo' => 'dev1@sgcs.com', 'rol' => 'Desarrollador Senior'],
            ['correo' => 'dev2@sgcs.com', 'rol' => 'Desarrollador'],
            ['correo' => 'dev3@sgcs.com', 'rol' => 'Desarrollador'],
            ['correo' => 'qa@sgcs.com', 'rol' => 'Analista QA'],
        ];

        foreach ($miembrosScrum as $miembro) {
            $usuario = Usuario::where('correo', $miembro['correo'])->first();
            $rol = $roles[$miembro['rol']] ?? null;

            if ($usuario && $rol) {
                MiembroEquipo::firstOrCreate([
                    'equipo_id' => $equipoScrum->id,
                    'usuario_id' => $usuario->id,
                    'rol_id' => $rol->id,
                ]);

                // También asignar en usuarios_roles
                DB::table('usuarios_roles')->insertOrIgnore([
                    'usuario_id' => $usuario->id,
                    'rol_id' => $rol->id,
                    'proyecto_id' => $proyectoScrum->id,
                ]);
            }
        }

        // Elementos de Configuración Scrum
        $this->crearElementosScrum($proyectoScrum, $productOwner);

        // Tareas Scrum
        $this->crearTareasScrum($proyectoScrum);

        // CCB Scrum
        $this->crearCCBScrum($proyectoScrum);

        // ============================================
        // 3. CREAR PROYECTO CASCADA COMPLETO
        // ============================================

        $this->command->info('🏗️  Creando Proyecto CASCADA...');

        $proyectoCascada = Proyecto::firstOrCreate(['codigo' => 'ERP-2024'], [
            'id' => Str::uuid()->toString(),
            'nombre' => 'Sistema ERP Corporativo',
            'descripcion' => 'Sistema integral de planificación de recursos empresariales (ERP) con módulos de contabilidad, RRHH, inventario, compras y ventas. Implementado con metodología tradicional en cascada.',
            'id_metodologia' => $cascada->id_metodologia,
            'fecha_inicio' => Carbon::now()->subMonths(3),
            'fecha_fin' => Carbon::now()->addMonths(9),
            'link_repositorio' => 'https://github.com/sgcs-demo/erp-corporativo',
            'creado_por' => $admin->id,  // ADMIN es creador
        ]);

        // Crear equipo Cascada con ADMIN como líder
        $equipoCascada = Equipo::firstOrCreate(
            ['proyecto_id' => $proyectoCascada->id, 'nombre' => 'ERP Implementation Team'],
            [
                'id' => Str::uuid()->toString(),
                'lider_id' => $admin->id,  // ADMIN es líder del equipo
            ]
        );

        // Asignar miembros al equipo Cascada (incluyendo admin)
        $miembrosCascada = [
            ['correo' => 'admin@sgcs.com', 'rol' => 'Líder de Proyecto'],  // ADMIN en el equipo
            ['correo' => 'pm@sgcs.com', 'rol' => 'Líder de Proyecto'],
            ['correo' => 'arch@sgcs.com', 'rol' => 'Arquitecto de Software'],
            ['correo' => 'analyst@sgcs.com', 'rol' => 'Product Owner'],
            ['correo' => 'dev1@sgcs.com', 'rol' => 'Desarrollador Senior'],
            ['correo' => 'dev2@sgcs.com', 'rol' => 'Desarrollador'],
            ['correo' => 'dev3@sgcs.com', 'rol' => 'Desarrollador'],
            ['correo' => 'qa@sgcs.com', 'rol' => 'Analista QA'],
            ['correo' => 'tester@sgcs.com', 'rol' => 'Tester'],
        ];

        foreach ($miembrosCascada as $miembro) {
            $usuario = Usuario::where('correo', $miembro['correo'])->first();
            $rol = $roles[$miembro['rol']] ?? null;

            if ($usuario && $rol) {
                MiembroEquipo::firstOrCreate([
                    'equipo_id' => $equipoCascada->id,
                    'usuario_id' => $usuario->id,
                    'rol_id' => $rol->id,
                ]);

                DB::table('usuarios_roles')->insertOrIgnore([
                    'usuario_id' => $usuario->id,
                    'rol_id' => $rol->id,
                    'proyecto_id' => $proyectoCascada->id,
                ]);
            }
        }

        // Elementos de Configuración Cascada
        $this->crearElementosCascada($proyectoCascada, $pmCascada);

        // Tareas Cascada
        $this->crearTareasCascada($proyectoCascada);

        // CCB Cascada
        $this->crearCCBCascada($proyectoCascada);

        // ============================================
        // 4. PROYECTOS ADICIONALES (para llenar)
        // ============================================

        $this->command->info('📦 Creando proyectos adicionales...');

        $proyectosAdicionales = [
            [
                'codigo' => 'MOB-2024',
                'nombre' => 'App Móvil Bancaria',
                'descripcion' => 'Aplicación móvil para banca digital',
                'metodologia' => $scrum->id_metodologia,
                'creado_por' => $scrumMaster->id,
            ],
            [
                'codigo' => 'WEB-2024',
                'nombre' => 'Portal Institucional',
                'descripcion' => 'Sitio web corporativo institucional',
                'metodologia' => $cascada->id_metodologia,
                'creado_por' => $pmCascada->id,
            ],
            [
                'codigo' => 'API-2024',
                'nombre' => 'API Gateway Empresarial',
                'descripcion' => 'Gateway de APIs para integración de servicios',
                'metodologia' => $scrum->id_metodologia,
                'creado_por' => $productOwner->id,
            ],
        ];

        foreach ($proyectosAdicionales as $proj) {
            Proyecto::firstOrCreate(['codigo' => $proj['codigo']], [
                'id' => Str::uuid()->toString(),
                'nombre' => $proj['nombre'],
                'descripcion' => $proj['descripcion'],
                'id_metodologia' => $proj['metodologia'],
                'fecha_inicio' => Carbon::now()->subMonths(rand(1, 3)),
                'fecha_fin' => Carbon::now()->addMonths(rand(3, 6)),
                'link_repositorio' => 'https://github.com/sgcs-demo/' . strtolower($proj['codigo']),
                'creado_por' => $proj['creado_por'],
            ]);
        }

        $this->command->info('✅ Demostración completa creada exitosamente!');
    }

    /**
     * Crear elementos de configuración para proyecto Scrum
     */
    private function crearElementosScrum($proyecto, $creador)
    {
        $this->command->info('  📄 Creando elementos de configuración Scrum...');

        $elementos = [];

        // 1. Product Backlog
        $productBacklog = ElementoConfiguracion::firstOrCreate(
            ['codigo_ec' => 'ECOM-DOC-001'],
            [
                'id' => Str::uuid()->toString(),
                'titulo' => 'Product Backlog',
                'descripcion' => 'Repositorio central de historias de usuario y features del producto',
                'proyecto_id' => $proyecto->id,
                'tipo' => 'DOCUMENTO',
                'estado' => 'APROBADO',
                'creado_por' => $creador->id,
            ]
        );
        $elementos[] = $productBacklog;

        // Versión del Product Backlog
        VersionEc::firstOrCreate(
            ['ec_id' => $productBacklog->id, 'version' => '1.0.0'],
            [
                'id' => Str::uuid()->toString(),
                'registro_cambios' => 'Versión inicial con 45 historias de usuario priorizadas',
                'estado' => 'APROBADO',
                'creado_por' => $creador->id,
                'aprobado_por' => $creador->id,
                'aprobado_en' => now(),
            ]
        );

        // 2. Sprint Backlog
        $sprintBacklog = ElementoConfiguracion::firstOrCreate(
            ['codigo_ec' => 'ECOM-DOC-002'],
            [
                'id' => Str::uuid()->toString(),
                'titulo' => 'Sprint Backlog - Sprint 1',
                'descripcion' => 'Planificación del primer sprint con 8 historias de usuario',
                'proyecto_id' => $proyecto->id,
                'tipo' => 'DOCUMENTO',
                'estado' => 'EN_REVISION',
                'creado_por' => $creador->id,
            ]
        );
        $elementos[] = $sprintBacklog;

        // 3. Repositorio de Código
        $repoCode = ElementoConfiguracion::firstOrCreate(
            ['codigo_ec' => 'ECOM-CODE-001'],
            [
                'id' => Str::uuid()->toString(),
                'titulo' => 'Repositorio Git - E-Commerce',
                'descripcion' => 'Código fuente del proyecto con branches main, develop y features',
                'proyecto_id' => $proyecto->id,
                'tipo' => 'CODIGO',
                'estado' => 'APROBADO',
                'creado_por' => $creador->id,
            ]
        );
        $elementos[] = $repoCode;

        VersionEc::firstOrCreate(
            ['ec_id' => $repoCode->id, 'version' => '0.1.0'],
            [
                'id' => Str::uuid()->toString(),
                'registro_cambios' => 'Configuración inicial del proyecto: Laravel 10, Vue 3, Tailwind CSS',
                'estado' => 'APROBADO',
                'creado_por' => $creador->id,
                'aprobado_por' => $creador->id,
                'aprobado_en' => now(),
            ]
        );

        // 4. Base de Datos
        $dbSchema = ElementoConfiguracion::firstOrCreate(
            ['codigo_ec' => 'ECOM-DB-001'],
            [
                'id' => Str::uuid()->toString(),
                'titulo' => 'Esquema de Base de Datos',
                'descripcion' => 'Scripts de migración y modelo de datos del e-commerce',
                'proyecto_id' => $proyecto->id,
                'tipo' => 'SCRIPT_BD',
                'estado' => 'APROBADO',
                'creado_por' => $creador->id,
            ]
        );
        $elementos[] = $dbSchema;

        // 5. API REST
        $apiDoc = ElementoConfiguracion::firstOrCreate(
            ['codigo_ec' => 'ECOM-DOC-003'],
            [
                'id' => Str::uuid()->toString(),
                'titulo' => 'Documentación API REST',
                'descripcion' => 'Especificación OpenAPI 3.0 de endpoints del backend',
                'proyecto_id' => $proyecto->id,
                'tipo' => 'DOCUMENTO',
                'estado' => 'EN_REVISION',
                'creado_por' => $creador->id,
            ]
        );
        $elementos[] = $apiDoc;

        // 6. Módulo de Autenticación
        $authModule = ElementoConfiguracion::firstOrCreate(
            ['codigo_ec' => 'ECOM-CODE-002'],
            [
                'id' => Str::uuid()->toString(),
                'titulo' => 'Módulo de Autenticación JWT',
                'descripcion' => 'Sistema de autenticación con tokens JWT y refresh tokens',
                'proyecto_id' => $proyecto->id,
                'tipo' => 'CODIGO',
                'estado' => 'LIBERADO',
                'creado_por' => $creador->id,
            ]
        );
        $elementos[] = $authModule;

        // 7. Módulo de Productos
        $productsModule = ElementoConfiguracion::firstOrCreate(
            ['codigo_ec' => 'ECOM-CODE-003'],
            [
                'id' => Str::uuid()->toString(),
                'titulo' => 'Módulo de Gestión de Productos',
                'descripcion' => 'CRUD completo de productos, categorías y variantes',
                'proyecto_id' => $proyecto->id,
                'tipo' => 'CODIGO',
                'estado' => 'APROBADO',
                'creado_por' => $creador->id,
            ]
        );
        $elementos[] = $productsModule;

        // 8. Módulo de Carrito
        $cartModule = ElementoConfiguracion::firstOrCreate(
            ['codigo_ec' => 'ECOM-CODE-004'],
            [
                'id' => Str::uuid()->toString(),
                'titulo' => 'Módulo de Carrito de Compras',
                'descripcion' => 'Sistema de carrito con persistencia y cálculo de totales',
                'proyecto_id' => $proyecto->id,
                'tipo' => 'CODIGO',
                'estado' => 'EN_REVISION',
                'creado_por' => $creador->id,
            ]
        );
        $elementos[] = $cartModule;

        // 9. Módulo de Pagos
        $paymentModule = ElementoConfiguracion::firstOrCreate(
            ['codigo_ec' => 'ECOM-CODE-005'],
            [
                'id' => Str::uuid()->toString(),
                'titulo' => 'Integración Pasarela de Pagos',
                'descripcion' => 'Integración con Stripe y PayPal para procesamiento de pagos',
                'proyecto_id' => $proyecto->id,
                'tipo' => 'CODIGO',
                'estado' => 'PENDIENTE',
                'creado_por' => $creador->id,
            ]
        );
        $elementos[] = $paymentModule;

        // 10. Tests Automatizados
        $tests = ElementoConfiguracion::firstOrCreate(
            ['codigo_ec' => 'ECOM-CODE-006'],
            [
                'id' => Str::uuid()->toString(),
                'titulo' => 'Suite de Tests Automatizados',
                'descripcion' => 'Tests unitarios y de integración con PHPUnit y Jest',
                'proyecto_id' => $proyecto->id,
                'tipo' => 'CODIGO',
                'estado' => 'EN_REVISION',
                'creado_por' => $creador->id,
            ]
        );
        $elementos[] = $tests;

        // 11. Configuración CI/CD
        $cicd = ElementoConfiguracion::firstOrCreate(
            ['codigo_ec' => 'ECOM-CONFIG-001'],
            [
                'id' => Str::uuid()->toString(),
                'titulo' => 'Pipeline CI/CD',
                'descripcion' => 'Configuración de GitHub Actions para integración y despliegue continuo',
                'proyecto_id' => $proyecto->id,
                'tipo' => 'CONFIGURACION',
                'estado' => 'APROBADO',
                'creado_por' => $creador->id,
            ]
        );
        $elementos[] = $cicd;

        // 12. Definition of Done
        $dod = ElementoConfiguracion::firstOrCreate(
            ['codigo_ec' => 'ECOM-DOC-004'],
            [
                'id' => Str::uuid()->toString(),
                'titulo' => 'Definition of Done (DoD)',
                'descripcion' => 'Criterios de aceptación y estándares de calidad del equipo',
                'proyecto_id' => $proyecto->id,
                'tipo' => 'DOCUMENTO',
                'estado' => 'APROBADO',
                'creado_por' => $creador->id,
            ]
        );
        $elementos[] = $dod;

        // Crear relaciones entre elementos
        $this->crearRelacionesScrum($elementos);
    }

    /**
     * Crear relaciones entre elementos de configuración Scrum
     */
    private function crearRelacionesScrum($elementos)
    {
        // Sprint Backlog depende de Product Backlog
        if (isset($elementos[0]) && isset($elementos[1])) {
            RelacionEC::firstOrCreate([
                'desde_ec' => $elementos[1]->id,
                'hacia_ec' => $elementos[0]->id,
                'tipo_relacion' => 'DEPENDE_DE',
            ], [
                'id' => Str::uuid()->toString(),
                'nota' => 'El Sprint Backlog se deriva del Product Backlog',
            ]);
        }

        // Módulo de Autenticación depende del Repositorio y BD
        if (isset($elementos[2]) && isset($elementos[3]) && isset($elementos[5])) {
            RelacionEC::firstOrCreate([
                'desde_ec' => $elementos[5]->id,
                'hacia_ec' => $elementos[2]->id,
                'tipo_relacion' => 'DEPENDE_DE',
            ], [
                'id' => Str::uuid()->toString(),
                'nota' => 'El módulo de autenticación requiere el repositorio de código',
            ]);

            RelacionEC::firstOrCreate([
                'desde_ec' => $elementos[5]->id,
                'hacia_ec' => $elementos[3]->id,
                'tipo_relacion' => 'DEPENDE_DE',
            ], [
                'id' => Str::uuid()->toString(),
                'nota' => 'El módulo de autenticación requiere la base de datos',
            ]);
        }

        // Módulo de Productos depende de Autenticación
        if (isset($elementos[5]) && isset($elementos[6])) {
            RelacionEC::firstOrCreate([
                'desde_ec' => $elementos[6]->id,
                'hacia_ec' => $elementos[5]->id,
                'tipo_relacion' => 'DEPENDE_DE',
            ], [
                'id' => Str::uuid()->toString(),
                'nota' => 'Gestión de productos requiere autenticación de usuarios',
            ]);
        }

        // Módulo de Carrito depende de Productos
        if (isset($elementos[6]) && isset($elementos[7])) {
            RelacionEC::firstOrCreate([
                'desde_ec' => $elementos[7]->id,
                'hacia_ec' => $elementos[6]->id,
                'tipo_relacion' => 'DEPENDE_DE',
            ], [
                'id' => Str::uuid()->toString(),
                'nota' => 'El carrito necesita el módulo de productos',
            ]);
        }

        // Módulo de Pagos depende de Carrito
        if (isset($elementos[7]) && isset($elementos[8])) {
            RelacionEC::firstOrCreate([
                'desde_ec' => $elementos[8]->id,
                'hacia_ec' => $elementos[7]->id,
                'tipo_relacion' => 'DEPENDE_DE',
            ], [
                'id' => Str::uuid()->toString(),
                'nota' => 'Los pagos procesan items del carrito',
            ]);
        }

        // Tests referencian a la API
        if (isset($elementos[4]) && isset($elementos[9])) {
            RelacionEC::firstOrCreate([
                'desde_ec' => $elementos[9]->id,
                'hacia_ec' => $elementos[4]->id,
                'tipo_relacion' => 'REFERENCIA',
            ], [
                'id' => Str::uuid()->toString(),
                'nota' => 'Los tests validan la API documentada',
            ]);
        }
    }

    /**
     * Crear tareas para proyecto Scrum
     */
    private function crearTareasScrum($proyecto)
    {
        $this->command->info('  ✅ Creando tareas Scrum...');

        $fases = DB::table('fases_metodologia')
            ->join('metodologias', 'fases_metodologia.id_metodologia', '=', 'metodologias.id_metodologia')
            ->where('metodologias.nombre', 'Scrum')
            ->get();

        $devSenior = Usuario::where('correo', 'dev.senior.scrum@sgcs.com')->first();
        $dev1 = Usuario::where('correo', 'dev1.scrum@sgcs.com')->first();
        $dev2 = Usuario::where('correo', 'dev2.scrum@sgcs.com')->first();
        $qa = Usuario::where('correo', 'qa.scrum@sgcs.com')->first();

        $tareasData = [
            ['fase' => 'Product Backlog', 'titulo' => 'US-001: Registro de usuarios', 'responsable' => $dev1, 'estado' => 'Done'],
            ['fase' => 'Product Backlog', 'titulo' => 'US-002: Login con JWT', 'responsable' => $devSenior, 'estado' => 'Done'],
            ['fase' => 'Sprint Planning', 'titulo' => 'US-003: CRUD de productos', 'responsable' => $dev2, 'estado' => 'Done'],
            ['fase' => 'In Progress', 'titulo' => 'US-004: Carrito de compras', 'responsable' => $dev1, 'estado' => 'In Progress'],
            ['fase' => 'In Progress', 'titulo' => 'US-005: Cálculo de totales', 'responsable' => $dev2, 'estado' => 'In Progress'],
            ['fase' => 'In Review', 'titulo' => 'US-006: Integración con Stripe', 'responsable' => $devSenior, 'estado' => 'In Review'],
            ['fase' => 'Sprint Planning', 'titulo' => 'US-007: Dashboard de analytics', 'responsable' => null, 'estado' => 'To Do'],
            ['fase' => 'Sprint Planning', 'titulo' => 'US-008: Gestión de órdenes', 'responsable' => null, 'estado' => 'To Do'],
        ];

        foreach ($tareasData as $tareaInfo) {
            $fase = $fases->firstWhere('nombre_fase', $tareaInfo['fase']);
            if (!$fase) continue;

            $fechaInicio = Carbon::now()->subDays(rand(10, 30));
            $fechaFin = $fechaInicio->copy()->addDays(rand(5, 15));

            TareaProyecto::firstOrCreate([
                'id_proyecto' => $proyecto->id,
                'id_fase' => $fase->id_fase,
                'responsable' => $tareaInfo['responsable']?->id,
            ], [
                'nombre' => $tareaInfo['titulo'],
                'descripcion' => 'Tarea: ' . $tareaInfo['titulo'],
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaFin,
                'estado' => $tareaInfo['estado'],
            ]);
        }
    }

    /**
     * Crear elementos de configuración para proyecto Cascada
     */
    private function crearElementosCascada($proyecto, $creador)
    {
        $this->command->info('  📄 Creando elementos de configuración Cascada...');

        $elementos = [];

        // 1. Documento de Requisitos (SRS)
        $srs = ElementoConfiguracion::firstOrCreate(
            ['codigo_ec' => 'ERP-DOC-001'],
            [
                'id' => Str::uuid()->toString(),
                'titulo' => 'Especificación de Requisitos del Sistema (SRS)',
                'descripcion' => 'Documento IEEE 830 con requisitos funcionales y no funcionales del ERP',
                'proyecto_id' => $proyecto->id,
                'tipo' => 'DOCUMENTO',
                'estado' => 'APROBADO',
                'creado_por' => $creador->id,
            ]
        );
        $elementos[] = $srs;

        VersionEc::firstOrCreate(
            ['ec_id' => $srs->id, 'version' => '2.1.0'],
            [
                'id' => Str::uuid()->toString(),
                'registro_cambios' => 'Revisión aprobada con 125 requisitos funcionales y 38 no funcionales',
                'estado' => 'APROBADO',
                'creado_por' => $creador->id,
                'aprobado_por' => $creador->id,
                'aprobado_en' => now()->subMonths(2),
            ]
        );

        // 2. Plan de Proyecto
        $planProyecto = ElementoConfiguracion::firstOrCreate(
            ['codigo_ec' => 'ERP-DOC-002'],
            [
                'id' => Str::uuid()->toString(),
                'titulo' => 'Plan de Gestión del Proyecto',
                'descripcion' => 'Plan maestro según PMBOK con cronograma, presupuesto y recursos',
                'proyecto_id' => $proyecto->id,
                'tipo' => 'DOCUMENTO',
                'estado' => 'APROBADO',
                'creado_por' => $creador->id,
            ]
        );
        $elementos[] = $planProyecto;

        // 3. Diseño de Arquitectura
        $arquitectura = ElementoConfiguracion::firstOrCreate(
            ['codigo_ec' => 'ERP-DOC-003'],
            [
                'id' => Str::uuid()->toString(),
                'titulo' => 'Documento de Arquitectura de Software (SAD)',
                'descripcion' => 'Arquitectura en capas con patrones MVC, Repository y Factory',
                'proyecto_id' => $proyecto->id,
                'tipo' => 'DOCUMENTO',
                'estado' => 'APROBADO',
                'creado_por' => $creador->id,
            ]
        );
        $elementos[] = $arquitectura;

        // 4. Modelo de Base de Datos
        $modeloBD = ElementoConfiguracion::firstOrCreate(
            ['codigo_ec' => 'ERP-DOC-004'],
            [
                'id' => Str::uuid()->toString(),
                'titulo' => 'Modelo Entidad-Relación',
                'descripcion' => 'Diagrama ER normalizado con 85 tablas y relaciones',
                'proyecto_id' => $proyecto->id,
                'tipo' => 'DOCUMENTO',
                'estado' => 'APROBADO',
                'creado_por' => $creador->id,
            ]
        );
        $elementos[] = $modeloBD;

        // 5. Scripts de Base de Datos
        $scriptsBD = ElementoConfiguracion::firstOrCreate(
            ['codigo_ec' => 'ERP-DB-001'],
            [
                'id' => Str::uuid()->toString(),
                'titulo' => 'Scripts DDL y Migraciones',
                'descripcion' => 'Scripts SQL para creación de esquema y datos iniciales',
                'proyecto_id' => $proyecto->id,
                'tipo' => 'SCRIPT_BD',
                'estado' => 'APROBADO',
                'creado_por' => $creador->id,
            ]
        );
        $elementos[] = $scriptsBD;

        // 6. Repositorio de Código
        $repoCode = ElementoConfiguracion::firstOrCreate(
            ['codigo_ec' => 'ERP-CODE-001'],
            [
                'id' => Str::uuid()->toString(),
                'titulo' => 'Repositorio Git - ERP',
                'descripcion' => 'Código fuente con estructura modular por subsistemas',
                'proyecto_id' => $proyecto->id,
                'tipo' => 'CODIGO',
                'estado' => 'APROBADO',
                'creado_por' => $creador->id,
            ]
        );
        $elementos[] = $repoCode;

        // 7. Módulo de Contabilidad
        $modContabilidad = ElementoConfiguracion::firstOrCreate(
            ['codigo_ec' => 'ERP-CODE-002'],
            [
                'id' => Str::uuid()->toString(),
                'titulo' => 'Módulo de Contabilidad',
                'descripcion' => 'Sistema contable con libro mayor, balance y estados financieros',
                'proyecto_id' => $proyecto->id,
                'tipo' => 'CODIGO',
                'estado' => 'EN_REVISION',
                'creado_por' => $creador->id,
            ]
        );
        $elementos[] = $modContabilidad;

        // 8. Módulo de RRHH
        $modRRHH = ElementoConfiguracion::firstOrCreate(
            ['codigo_ec' => 'ERP-CODE-003'],
            [
                'id' => Str::uuid()->toString(),
                'titulo' => 'Módulo de Recursos Humanos',
                'descripcion' => 'Gestión de empleados, nómina y evaluaciones de desempeño',
                'proyecto_id' => $proyecto->id,
                'tipo' => 'CODIGO',
                'estado' => 'APROBADO',
                'creado_por' => $creador->id,
            ]
        );
        $elementos[] = $modRRHH;

        // 9. Módulo de Inventario
        $modInventario = ElementoConfiguracion::firstOrCreate(
            ['codigo_ec' => 'ERP-CODE-004'],
            [
                'id' => Str::uuid()->toString(),
                'titulo' => 'Módulo de Inventario',
                'descripcion' => 'Control de stock, kardex y valoración de inventarios',
                'proyecto_id' => $proyecto->id,
                'tipo' => 'CODIGO',
                'estado' => 'LIBERADO',
                'creado_por' => $creador->id,
            ]
        );
        $elementos[] = $modInventario;

        // 10. Módulo de Compras
        $modCompras = ElementoConfiguracion::firstOrCreate(
            ['codigo_ec' => 'ERP-CODE-005'],
            [
                'id' => Str::uuid()->toString(),
                'titulo' => 'Módulo de Compras',
                'descripcion' => 'Gestión de proveedores, órdenes de compra y recepciones',
                'proyecto_id' => $proyecto->id,
                'tipo' => 'CODIGO',
                'estado' => 'APROBADO',
                'creado_por' => $creador->id,
            ]
        );
        $elementos[] = $modCompras;

        // 11. Módulo de Ventas
        $modVentas = ElementoConfiguracion::firstOrCreate(
            ['codigo_ec' => 'ERP-CODE-006'],
            [
                'id' => Str::uuid()->toString(),
                'titulo' => 'Módulo de Ventas',
                'descripcion' => 'Gestión de clientes, cotizaciones, pedidos y facturación',
                'proyecto_id' => $proyecto->id,
                'tipo' => 'CODIGO',
                'estado' => 'EN_REVISION',
                'creado_por' => $creador->id,
            ]
        );
        $elementos[] = $modVentas;

        // 12. Plan de Pruebas
        $planPruebas = ElementoConfiguracion::firstOrCreate(
            ['codigo_ec' => 'ERP-DOC-005'],
            [
                'id' => Str::uuid()->toString(),
                'titulo' => 'Plan Maestro de Pruebas',
                'descripcion' => 'Estrategia de testing con casos de prueba funcionales y de integración',
                'proyecto_id' => $proyecto->id,
                'tipo' => 'DOCUMENTO',
                'estado' => 'APROBADO',
                'creado_por' => $creador->id,
            ]
        );
        $elementos[] = $planPruebas;

        // 13. Casos de Prueba
        $casosPrueba = ElementoConfiguracion::firstOrCreate(
            ['codigo_ec' => 'ERP-DOC-006'],
            [
                'id' => Str::uuid()->toString(),
                'titulo' => 'Suite de Casos de Prueba',
                'descripcion' => '350+ casos de prueba categorizados por módulo y prioridad',
                'proyecto_id' => $proyecto->id,
                'tipo' => 'DOCUMENTO',
                'estado' => 'EN_REVISION',
                'creado_por' => $creador->id,
            ]
        );
        $elementos[] = $casosPrueba;

        // 14. Manual de Usuario
        $manualUsuario = ElementoConfiguracion::firstOrCreate(
            ['codigo_ec' => 'ERP-DOC-007'],
            [
                'id' => Str::uuid()->toString(),
                'titulo' => 'Manual de Usuario Final',
                'descripcion' => 'Guía completa de uso del sistema con capturas de pantalla',
                'proyecto_id' => $proyecto->id,
                'tipo' => 'DOCUMENTO',
                'estado' => 'PENDIENTE',
                'creado_por' => $creador->id,
            ]
        );
        $elementos[] = $manualUsuario;

        // 15. Plan de Despliegue
        $planDespliegue = ElementoConfiguracion::firstOrCreate(
            ['codigo_ec' => 'ERP-DOC-008'],
            [
                'id' => Str::uuid()->toString(),
                'titulo' => 'Plan de Despliegue a Producción',
                'descripcion' => 'Procedimientos de instalación, configuración y rollback',
                'proyecto_id' => $proyecto->id,
                'tipo' => 'DOCUMENTO',
                'estado' => 'BORRADOR',
                'creado_por' => $creador->id,
            ]
        );
        $elementos[] = $planDespliegue;

        // Crear relaciones entre elementos
        $this->crearRelacionesCascada($elementos);
    }

    /**
     * Crear relaciones entre elementos de configuración Cascada
     */
    private function crearRelacionesCascada($elementos)
    {
        // Arquitectura depende de SRS
        if (isset($elementos[0]) && isset($elementos[2])) {
            RelacionEC::firstOrCreate([
                'desde_ec' => $elementos[2]->id,
                'hacia_ec' => $elementos[0]->id,
                'tipo_relacion' => 'DERIVADO_DE',
            ], [
                'id' => Str::uuid()->toString(),
                'nota' => 'La arquitectura se deriva de los requisitos del SRS',
            ]);
        }

        // Modelo BD depende de Arquitectura
        if (isset($elementos[2]) && isset($elementos[3])) {
            RelacionEC::firstOrCreate([
                'desde_ec' => $elementos[3]->id,
                'hacia_ec' => $elementos[2]->id,
                'tipo_relacion' => 'DERIVADO_DE',
            ], [
                'id' => Str::uuid()->toString(),
                'nota' => 'El modelo de datos se deriva del diseño arquitectónico',
            ]);
        }

        // Scripts BD dependen del Modelo BD
        if (isset($elementos[3]) && isset($elementos[4])) {
            RelacionEC::firstOrCreate([
                'desde_ec' => $elementos[4]->id,
                'hacia_ec' => $elementos[3]->id,
                'tipo_relacion' => 'DERIVADO_DE',
            ], [
                'id' => Str::uuid()->toString(),
                'nota' => 'Los scripts implementan el modelo de datos',
            ]);
        }

        // Todos los módulos dependen del Repositorio y BD
        for ($i = 6; $i <= 10; $i++) {
            if (isset($elementos[$i]) && isset($elementos[5])) {
                RelacionEC::firstOrCreate([
                    'desde_ec' => $elementos[$i]->id,
                    'hacia_ec' => $elementos[5]->id,
                    'tipo_relacion' => 'DEPENDE_DE',
                ], [
                    'id' => Str::uuid()->toString(),
                    'nota' => 'Módulo requiere el repositorio de código base',
                ]);
            }

            if (isset($elementos[$i]) && isset($elementos[4])) {
                RelacionEC::firstOrCreate([
                    'desde_ec' => $elementos[$i]->id,
                    'hacia_ec' => $elementos[4]->id,
                    'tipo_relacion' => 'DEPENDE_DE',
                ], [
                    'id' => Str::uuid()->toString(),
                    'nota' => 'Módulo requiere la base de datos',
                ]);
            }
        }

        // Módulo de Ventas requiere Inventario
        if (isset($elementos[8]) && isset($elementos[10])) {
            RelacionEC::firstOrCreate([
                'desde_ec' => $elementos[10]->id,
                'hacia_ec' => $elementos[8]->id,
                'tipo_relacion' => 'REQUERIDO_POR',
            ], [
                'id' => Str::uuid()->toString(),
                'nota' => 'Las ventas requieren consultar el inventario disponible',
            ]);
        }

        // Casos de Prueba se derivan del Plan de Pruebas
        if (isset($elementos[11]) && isset($elementos[12])) {
            RelacionEC::firstOrCreate([
                'desde_ec' => $elementos[12]->id,
                'hacia_ec' => $elementos[11]->id,
                'tipo_relacion' => 'DERIVADO_DE',
            ], [
                'id' => Str::uuid()->toString(),
                'nota' => 'Los casos de prueba implementan el plan maestro de pruebas',
            ]);
        }
    }

    /**
     * Crear tareas para proyecto Cascada
     */
    private function crearTareasCascada($proyecto)
    {
        $this->command->info('  ✅ Creando tareas Cascada...');

        $fases = DB::table('fases_metodologia')
            ->join('metodologias', 'fases_metodologia.id_metodologia', '=', 'metodologias.id_metodologia')
            ->where('metodologias.nombre', 'Cascada')
            ->get();

        $architect = Usuario::where('correo', 'architect.cascada@sgcs.com')->first();
        $analyst = Usuario::where('correo', 'analyst.cascada@sgcs.com')->first();
        $devSenior = Usuario::where('correo', 'dev.senior.cascada@sgcs.com')->first();
        $dev1 = Usuario::where('correo', 'dev1.cascada@sgcs.com')->first();
        $dev2 = Usuario::where('correo', 'dev2.cascada@sgcs.com')->first();
        $qa = Usuario::where('correo', 'qa.cascada@sgcs.com')->first();
        $tester = Usuario::where('correo', 'tester.cascada@sgcs.com')->first();

        $tareasData = [
            ['fase' => 'Requisitos', 'titulo' => 'Recopilación de requisitos funcionales', 'responsable' => $analyst, 'estado' => 'Completada'],
            ['fase' => 'Requisitos', 'titulo' => 'Documentación de requisitos no funcionales', 'responsable' => $analyst, 'estado' => 'Completada'],
            ['fase' => 'Análisis', 'titulo' => 'Análisis de factibilidad técnica', 'responsable' => $architect, 'estado' => 'Completada'],
            ['fase' => 'Análisis', 'titulo' => 'Definición de casos de uso', 'responsable' => $analyst, 'estado' => 'Completada'],
            ['fase' => 'Diseño', 'titulo' => 'Diseño de arquitectura del sistema', 'responsable' => $architect, 'estado' => 'Completada'],
            ['fase' => 'Diseño', 'titulo' => 'Diseño de base de datos', 'responsable' => $architect, 'estado' => 'Completada'],
            ['fase' => 'Diseño', 'titulo' => 'Diseño de interfaces de usuario', 'responsable' => $devSenior, 'estado' => 'Completada'],
            ['fase' => 'Implementación', 'titulo' => 'Desarrollo módulo de Contabilidad', 'responsable' => $devSenior, 'estado' => 'En Progreso'],
            ['fase' => 'Implementación', 'titulo' => 'Desarrollo módulo de RRHH', 'responsable' => $dev1, 'estado' => 'En Progreso'],
            ['fase' => 'Implementación', 'titulo' => 'Desarrollo módulo de Inventario', 'responsable' => $dev2, 'estado' => 'Completada'],
            ['fase' => 'Implementación', 'titulo' => 'Desarrollo módulo de Compras', 'responsable' => $dev1, 'estado' => 'En Progreso'],
            ['fase' => 'Implementación', 'titulo' => 'Desarrollo módulo de Ventas', 'responsable' => $devSenior, 'estado' => 'Pendiente'],
            ['fase' => 'Pruebas', 'titulo' => 'Elaboración de plan de pruebas', 'responsable' => $qa, 'estado' => 'Completada'],
            ['fase' => 'Pruebas', 'titulo' => 'Ejecución de pruebas unitarias', 'responsable' => $tester, 'estado' => 'En Progreso'],
            ['fase' => 'Pruebas', 'titulo' => 'Pruebas de integración', 'responsable' => $qa, 'estado' => 'Pendiente'],
            ['fase' => 'Despliegue', 'titulo' => 'Preparación de ambiente de producción', 'responsable' => $devSenior, 'estado' => 'Pendiente'],
            ['fase' => 'Mantenimiento', 'titulo' => 'Planificación de soporte post-lanzamiento', 'responsable' => null, 'estado' => 'Pendiente'],
        ];

        foreach ($tareasData as $tareaInfo) {
            $fase = $fases->firstWhere('nombre_fase', $tareaInfo['fase']);
            if (!$fase) continue;

            $estadoTarea = match($tareaInfo['estado']) {
                'Completada' => 'completada',
                'En Progreso' => 'en_progreso',
                'Pendiente' => 'pendiente',
                default => 'pendiente',
            };

            $fechaInicio = Carbon::now()->subMonths(2)->addDays(rand(0, 60));
            $fechaFin = $fechaInicio->copy()->addDays(rand(10, 30));

            TareaProyecto::firstOrCreate([
                'id_proyecto' => $proyecto->id,
                'id_fase' => $fase->id_fase,
                'responsable' => $tareaInfo['responsable']?->id,
            ], [
                'nombre' => $tareaInfo['titulo'],
                'descripcion' => 'Tarea: ' . $tareaInfo['titulo'],
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaFin,
                'estado' => $estadoTarea,
            ]);
        }
    }

    /**
     * Crear Comité de Control de Cambios para Scrum
     */
    private function crearCCBScrum($proyecto)
    {
        $this->command->info('  🔒 Creando CCB Scrum...');

        $ccb = ComiteCambio::firstOrCreate(
            ['proyecto_id' => $proyecto->id],
            [
                'id' => Str::uuid()->toString(),
                'nombre' => 'Comité de Control de Cambios E-Commerce',
                'quorum' => 3,
            ]
        );

        $miembrosCCB = [
            ['correo' => 'scm.manager@sgcs.com', 'rol' => 'Presidente'],
            ['correo' => 'po.scrum@sgcs.com', 'rol' => 'Product Owner'],
            ['correo' => 'sm.scrum@sgcs.com', 'rol' => 'Scrum Master'],
            ['correo' => 'dev.senior.scrum@sgcs.com', 'rol' => 'Líder Técnico'],
            ['correo' => 'qa.scrum@sgcs.com', 'rol' => 'QA Lead'],
        ];

        foreach ($miembrosCCB as $miembro) {
            $usuario = Usuario::where('correo', $miembro['correo'])->first();
            if ($usuario) {
                MiembroCCB::firstOrCreate([
                    'ccb_id' => $ccb->id,
                    'usuario_id' => $usuario->id,
                ], [
                    'rol_en_ccb' => $miembro['rol'],
                ]);
            }
        }
    }

    /**
     * Crear Comité de Control de Cambios para Cascada
     */
    private function crearCCBCascada($proyecto)
    {
        $this->command->info('  🔒 Creando CCB Cascada...');

        $ccb = ComiteCambio::firstOrCreate(
            ['proyecto_id' => $proyecto->id],
            [
                'id' => Str::uuid()->toString(),
                'nombre' => 'Comité de Control de Cambios ERP',
                'quorum' => 4,
            ]
        );

        $miembrosCCB = [
            ['correo' => 'ccb.admin@sgcs.com', 'rol' => 'Presidente CCB'],
            ['correo' => 'pm.cascada@sgcs.com', 'rol' => 'Líder de Proyecto'],
            ['correo' => 'architect.cascada@sgcs.com', 'rol' => 'Arquitecto de Software'],
            ['correo' => 'dev.senior.cascada@sgcs.com', 'rol' => 'Líder Técnico'],
            ['correo' => 'qa.cascada@sgcs.com', 'rol' => 'QA Manager'],
            ['correo' => 'auditor@sgcs.com', 'rol' => 'Auditor de Configuración'],
        ];

        foreach ($miembrosCCB as $miembro) {
            $usuario = Usuario::where('correo', $miembro['correo'])->first();
            if ($usuario) {
                MiembroCCB::firstOrCreate([
                    'ccb_id' => $ccb->id,
                    'usuario_id' => $usuario->id,
                ], [
                    'rol_en_ccb' => $miembro['rol'],
                ]);
            }
        }
    }
}
