

<?php

use App\Http\Controllers\PerfilController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EstadisticasController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\GestionProyectos\ProyectoController;
use App\Http\Controllers\GestionProyectos\ElementoConfiguracionController;
use App\Http\Controllers\GestionProyectos\RelacionECController;
use Illuminate\Support\Facades\Route;

// Rutas para verificación 2FA en login
Route::get('/2fa', [\App\Http\Controllers\Auth\TwoFactorController::class, 'showForm'])->name('auth.2fa');
Route::post('/2fa', [\App\Http\Controllers\Auth\TwoFactorController::class, 'verify'])->name('auth.2fa.verify');

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/estadisticas', [EstadisticasController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('estadisticas');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [PerfilController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [PerfilController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [PerfilController::class, 'destroy'])->name('profile.destroy');

    // Rutas de Notificaciones
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('/{id}/mark-read', [NotificationController::class, 'markAsRead'])->name('mark-read');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllRead'])->name('mark-all-read');
        Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy');
    });

    // Ruta global de Liberaciones (fuera de proyectos individuales)
    Route::get('/liberaciones', [\App\Http\Controllers\LiberacionesGlobalController::class, 'index'])->name('liberaciones.index');

    // Rutas de proyectos
    Route::post('/perfil/activar-2fa', [PerfilController::class, 'activar2fa'])->name('perfil.activar2fa');
    Route::post('/perfil/confirmar-2fa', [PerfilController::class, 'confirmar2fa'])->name('perfil.confirmar2fa');
    Route::post('/perfil/desactivar-2fa', [PerfilController::class, 'desactivar2fa'])->name('perfil.desactivar2fa');

    // Rutas de Gestión de Proyectos
    Route::prefix('proyectos')->name('proyectos.')->group(function () {
        // Lista de proyectos
        Route::get('/', [ProyectoController::class, 'index'])->name('index');

        // Paso 1: Datos del Proyecto
        Route::get('/crear', [ProyectoController::class, 'create'])->name('create');
        Route::post('/crear/paso-1', [ProyectoController::class, 'storeStep1'])->name('store-step1');

        // Paso 2: Seleccionar Plantillas EC
        Route::get('/crear/paso-2', [ProyectoController::class, 'createStep2'])->name('create-step2');
        Route::post('/crear/paso-2', [ProyectoController::class, 'storeStep2'])->name('store-step2');

        // Paso 3: Configurar Equipo
        Route::get('/crear/paso-3', [ProyectoController::class, 'createTeams'])->name('create-step3');
        Route::post('/crear/paso-3', [ProyectoController::class, 'storeStep3'])->name('store-step3');

        // Paso 4: Revisión Final
        Route::get('/crear/paso-4', [ProyectoController::class, 'createStep4'])->name('create-step4');

        // Guardar proyecto completo
        Route::post('/guardar', [ProyectoController::class, 'store'])->name('store');

        // Cancelar proceso
        Route::get('/cancelar', [ProyectoController::class, 'cancel'])->name('cancel');

        // Ver proyecto específico (dashboard interno) - DEBE IR AL FINAL
        Route::get('/{proyecto}', [ProyectoController::class, 'show'])->name('show');

        // Rutas de Elementos de Configuración (anidadas bajo proyecto)
        Route::prefix('/{proyecto}/elementos')->name('elementos.')->group(function () {
            Route::get('/', [ElementoConfiguracionController::class, 'index'])->name('index');
            Route::get('/grafo', [ElementoConfiguracionController::class, 'grafo'])->name('grafo');
            Route::get('/ver-grafo', [ElementoConfiguracionController::class, 'verGrafo'])->name('verGrafo');
            Route::get('/crear', [ElementoConfiguracionController::class, 'create'])->name('create');
            Route::post('/', [ElementoConfiguracionController::class, 'store'])->name('store');
            Route::get('/{elemento}/editar', [ElementoConfiguracionController::class, 'edit'])->name('edit');
            Route::put('/{elemento}', [ElementoConfiguracionController::class, 'update'])->name('update');
            Route::delete('/{elemento}', [ElementoConfiguracionController::class, 'destroy'])->name('destroy');

            // Rutas para revisión y aprobación de EC
            Route::get('/{elemento}/revisar', [ElementoConfiguracionController::class, 'review'])->name('review');
            Route::post('/{elemento}/aprobar', [ElementoConfiguracionController::class, 'approve'])->name('approve');

            // Rutas de Relaciones (anidadas bajo elemento)
            Route::prefix('/{elemento}/relaciones')->name('relaciones.')->group(function () {
                Route::get('/', [RelacionECController::class, 'index'])->name('index');
                Route::get('/crear', [RelacionECController::class, 'create'])->name('create');
                Route::post('/', [RelacionECController::class, 'store'])->name('store');
                Route::delete('/{relacion}', [RelacionECController::class, 'destroy'])->name('destroy');
            });
        });

        // Rutas de Tareas/Cronograma (anidadas bajo proyecto)
        Route::prefix('/{proyecto}/tareas')->name('tareas.')->group(function () {
            Route::get('/', [\App\Http\Controllers\GestionProyectos\TareaProyectoController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\GestionProyectos\TareaProyectoController::class, 'store'])->name('store');
            Route::get('/{tarea}', [\App\Http\Controllers\GestionProyectos\TareaProyectoController::class, 'show'])->name('show');
            Route::get('/{tarea}/editar', [\App\Http\Controllers\GestionProyectos\TareaProyectoController::class, 'edit'])->name('edit');
            Route::put('/{tarea}', [\App\Http\Controllers\GestionProyectos\TareaProyectoController::class, 'update'])->name('update');
            Route::delete('/{tarea}', [\App\Http\Controllers\GestionProyectos\TareaProyectoController::class, 'destroy'])->name('destroy');
            Route::post('/{tarea}/cambiar-fase', [\App\Http\Controllers\GestionProyectos\TareaProyectoController::class, 'cambiarFase'])->name('cambiar-fase');
            Route::post('/{tarea}/actualizar-estado', [\App\Http\Controllers\GestionProyectos\TareaProyectoController::class, 'actualizarEstado'])->name('actualizarEstado');
        });

        // Ruta de trazabilidad general del proyecto
        Route::get('/{proyecto}/trazabilidad', [ProyectoController::class, 'trazabilidad'])->name('trazabilidad');

        // API para consultar información de commits (AJAX)
        Route::get('/commits/{commit}/detalles', [\App\Http\Controllers\GestionProyectos\ElementoConfiguracionController::class, 'obtenerDetallesCommit'])->name('commits.detalles');

        // Rutas de Solicitudes de Cambio (CCB)
        Route::prefix('/{proyecto}/solicitudes')->name('solicitudes.')->group(function () {
            Route::get('/', [\App\Http\Controllers\GestionConfiguracion\SolicitudCambioController::class, 'index'])->name('index');
            Route::get('/crear', [\App\Http\Controllers\GestionConfiguracion\SolicitudCambioController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\GestionConfiguracion\SolicitudCambioController::class, 'store'])->name('store');
            Route::get('/{solicitud}', [\App\Http\Controllers\GestionConfiguracion\SolicitudCambioController::class, 'show'])->name('show');
            Route::get('/{solicitud}/evaluar-impacto', [\App\Http\Controllers\GestionConfiguracion\SolicitudCambioController::class, 'evaluarImpacto'])->name('evaluar-impacto');
            Route::post('/{solicitud}/enviar-ccb', [\App\Http\Controllers\GestionConfiguracion\SolicitudCambioController::class, 'enviarACCB'])->name('enviar-ccb');
            Route::get('/{solicitud}/votar', [\App\Http\Controllers\GestionConfiguracion\SolicitudCambioController::class, 'mostrarFormularioVoto'])->name('votar-form');
            Route::post('/{solicitud}/votar', [\App\Http\Controllers\GestionConfiguracion\SolicitudCambioController::class, 'votar'])->name('votar');
            Route::post('/{solicitud}/implementar', [\App\Http\Controllers\GestionConfiguracion\SolicitudCambioController::class, 'implementar'])->name('implementar');
            Route::post('/{solicitud}/cerrar', [\App\Http\Controllers\GestionConfiguracion\SolicitudCambioController::class, 'cerrar'])->name('cerrar');
        });

        // Rutas del Comité de Control de Cambios (CCB)
        Route::prefix('/{proyecto}/ccb')->name('ccb.')->group(function () {
            Route::get('/dashboard', [\App\Http\Controllers\GestionConfiguracion\ComiteCambiosController::class, 'dashboard'])->name('dashboard');
            Route::get('/configurar', [\App\Http\Controllers\GestionConfiguracion\ComiteCambiosController::class, 'configurar'])->name('configurar');
            Route::post('/configurar', [\App\Http\Controllers\GestionConfiguracion\ComiteCambiosController::class, 'guardarConfiguracion'])->name('guardar-configuracion');
            Route::get('/miembros', [\App\Http\Controllers\GestionConfiguracion\ComiteCambiosController::class, 'verMiembros'])->name('miembros');
            Route::post('/miembros/agregar', [\App\Http\Controllers\GestionConfiguracion\ComiteCambiosController::class, 'agregarMiembro'])->name('miembros.agregar');
            Route::put('/miembros/{usuarioId}/rol', [\App\Http\Controllers\GestionConfiguracion\ComiteCambiosController::class, 'actualizarRolMiembro'])->name('miembros.actualizar-rol');
            Route::delete('/miembros/{usuarioId}', [\App\Http\Controllers\GestionConfiguracion\ComiteCambiosController::class, 'removerMiembro'])->name('miembros.remover');
            Route::get('/historial-votos', [\App\Http\Controllers\GestionConfiguracion\ComiteCambiosController::class, 'historialVotos'])->name('historial-votos');
        });

        // Rutas de Gestión de Equipos
        Route::prefix('/{proyecto}/equipos')->name('equipos.')->group(function () {
            Route::get('/', [\App\Http\Controllers\GestionProyectos\ProyectoController::class, 'gestionarEquipos'])->name('index');
            Route::get('/crear', [\App\Http\Controllers\GestionProyectos\ProyectoController::class, 'crearEquipo'])->name('create');
            Route::post('/', [\App\Http\Controllers\GestionProyectos\ProyectoController::class, 'guardarEquipo'])->name('store');
            Route::get('/{equipo}/editar', [\App\Http\Controllers\GestionProyectos\ProyectoController::class, 'editarEquipo'])->name('edit');
            Route::put('/{equipo}', [\App\Http\Controllers\GestionProyectos\ProyectoController::class, 'actualizarEquipo'])->name('update');
            Route::delete('/{equipo}', [\App\Http\Controllers\GestionProyectos\ProyectoController::class, 'eliminarEquipo'])->name('destroy');
            Route::post('/{equipo}/miembros', [\App\Http\Controllers\GestionProyectos\ProyectoController::class, 'agregarMiembroEquipo'])->name('miembros.store');
            Route::delete('/{equipo}/miembros/{usuarioId}', [\App\Http\Controllers\GestionProyectos\ProyectoController::class, 'removerMiembroEquipo'])->name('miembros.destroy');
        });

        // Rutas de Gestión de Miembros del Proyecto
        Route::prefix('/{proyecto}/miembros')->name('miembros.')->group(function () {
            Route::get('/', [\App\Http\Controllers\GestionProyectos\ProyectoController::class, 'gestionarMiembrosProyecto'])->name('index');
            Route::post('/agregar', [\App\Http\Controllers\GestionProyectos\ProyectoController::class, 'agregarMiembroProyecto'])->name('store');
            Route::put('/{usuarioId}/rol', [\App\Http\Controllers\GestionProyectos\ProyectoController::class, 'actualizarRolMiembroProyecto'])->name('update');
            Route::delete('/{usuarioId}', [\App\Http\Controllers\GestionProyectos\ProyectoController::class, 'removerMiembroProyecto'])->name('destroy');
        });

        // Rutas de Cronograma Inteligente
        Route::prefix('/{proyecto}/cronograma-inteligente')->name('cronograma.')->group(function () {
            Route::get('/', [\App\Http\Controllers\GestionProyectos\CronogramaInteligenteController::class, 'dashboard'])->name('dashboard');
            Route::post('/analizar', [\App\Http\Controllers\GestionProyectos\CronogramaInteligenteController::class, 'analizar'])->name('analizar');
            Route::post('/generar', [\App\Http\Controllers\GestionProyectos\CronogramaInteligenteController::class, 'generar'])->name('generar');
            Route::get('/simular', [\App\Http\Controllers\GestionProyectos\CronogramaInteligenteController::class, 'simular'])->name('simular');
            Route::get('/ajustes/{ajuste}', [\App\Http\Controllers\GestionProyectos\CronogramaInteligenteController::class, 'verAjuste'])->name('ver-ajuste');
            Route::post('/ajustes/{ajuste}/aprobar', [\App\Http\Controllers\GestionProyectos\CronogramaInteligenteController::class, 'aprobar'])->name('aprobar');
            Route::post('/ajustes/{ajuste}/rechazar', [\App\Http\Controllers\GestionProyectos\CronogramaInteligenteController::class, 'rechazar'])->name('rechazar');
            Route::post('/ajustes/{ajuste}/aplicar', [\App\Http\Controllers\GestionProyectos\CronogramaInteligenteController::class, 'aplicar'])->name('aplicar');
            Route::post('/ajustes/{ajuste}/revertir', [\App\Http\Controllers\GestionProyectos\CronogramaInteligenteController::class, 'revertir'])->name('revertir');
            Route::get('/historial', [\App\Http\Controllers\GestionProyectos\CronogramaInteligenteController::class, 'historial'])->name('historial');
        });

        // Rutas de Informes
        Route::prefix('/{proyecto}/informes')->name('informes.')->group(function () {
            Route::get('/', [\App\Http\Controllers\GestionProyectos\InformesController::class, 'dashboard'])->name('dashboard');
        });

        // Rutas de Liberaciones
        Route::prefix('/{proyecto}/liberaciones')->name('liberaciones.')->group(function () {
            Route::get('/', [\App\Http\Controllers\GestionProyectos\LiberacionesController::class, 'index'])->name('index');
            Route::get('/crear', [\App\Http\Controllers\GestionProyectos\LiberacionesController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\GestionProyectos\LiberacionesController::class, 'store'])->name('store');
            Route::get('/{liberacion}', [\App\Http\Controllers\GestionProyectos\LiberacionesController::class, 'show'])->name('show');
            Route::post('/{liberacion}/elementos', [\App\Http\Controllers\GestionProyectos\LiberacionesController::class, 'agregarElementos'])->name('agregar-elementos');
            Route::delete('/{liberacion}/elementos/{item}', [\App\Http\Controllers\GestionProyectos\LiberacionesController::class, 'quitarElemento'])->name('quitar-elemento');
            Route::delete('/{liberacion}', [\App\Http\Controllers\GestionProyectos\LiberacionesController::class, 'destroy'])->name('destroy');
        });
    });

    // Rutas específicas para metodología Scrum
    Route::prefix('proyectos/{proyecto}/scrum')->name('scrum.')->group(function () {
        // Vistas
        Route::get('/dashboard', [\App\Http\Controllers\GestionProyectos\ScrumController::class, 'dashboard'])->name('dashboard');
        Route::get('/sprint-planning', [\App\Http\Controllers\GestionProyectos\ScrumController::class, 'sprintPlanning'])->name('sprint-planning');
        Route::get('/daily-scrum', [\App\Http\Controllers\GestionProyectos\ScrumController::class, 'dailyScrum'])->name('daily-scrum');
        Route::get('/sprint-review', [\App\Http\Controllers\GestionProyectos\ScrumController::class, 'sprintReview'])->name('sprint-review');
        Route::get('/sprint-retrospective', [\App\Http\Controllers\GestionProyectos\ScrumController::class, 'sprintRetrospective'])->name('sprint-retrospective');

        // Acciones POST
        Route::post('/sprints', [\App\Http\Controllers\GestionProyectos\ScrumController::class, 'storeSprint'])->name('sprints.store');
        Route::post('/sprints/{sprint}/iniciar', [\App\Http\Controllers\GestionProyectos\ScrumController::class, 'iniciarSprint'])->name('sprints.iniciar');
        Route::post('/sprints/{sprint}/completar', [\App\Http\Controllers\GestionProyectos\ScrumController::class, 'completarSprint'])->name('sprints.completar');
        Route::post('/sprints/{sprint}/asignar-user-stories', [\App\Http\Controllers\GestionProyectos\ScrumController::class, 'asignarUserStories'])->name('sprints.asignar-user-stories');
        Route::get('/sprints/{sprint}/user-stories', [\App\Http\Controllers\GestionProyectos\ScrumController::class, 'obtenerUserStoriesSprint'])->name('sprints.user-stories');
        Route::post('/user-stories', [\App\Http\Controllers\GestionProyectos\ScrumController::class, 'storeUserStory'])->name('user-stories.store');
        Route::patch('/user-stories/{tarea}', [\App\Http\Controllers\GestionProyectos\ScrumController::class, 'updateUserStory'])->name('user-stories.update');
        Route::post('/user-stories/{tarea}/remover-sprint', [\App\Http\Controllers\GestionProyectos\ScrumController::class, 'removerUserStorySprint'])->name('user-stories.remover-sprint');
        Route::post('/daily-scrums', [\App\Http\Controllers\GestionProyectos\ScrumController::class, 'storeDailyScrum'])->name('daily-scrums.store');
    });

    // Rutas específicas para metodología Cascada
    Route::prefix('proyectos/{proyecto}/cascada')->name('cascada.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\GestionProyectos\CascadaController::class, 'dashboard'])->name('dashboard');
        Route::get('/cronograma-maestro', [\App\Http\Controllers\GestionProyectos\CascadaController::class, 'cronogramaMaestro'])->name('cronograma-maestro');
        Route::get('/hitos', [\App\Http\Controllers\GestionProyectos\CascadaController::class, 'hitos'])->name('hitos');
        Route::get('/fase/{fase}', [\App\Http\Controllers\GestionProyectos\CascadaController::class, 'verFase'])->name('ver-fase');
    });

    // Ruta temporal para debug CCB
    Route::get('/debug-ccb', function() {
        $proyecto = \App\Models\Proyecto::first();
        $ccb = $proyecto?->hasOne(\App\Models\ComiteCambio::class, 'proyecto_id')->first();

        if (!$ccb) {
            return 'No hay CCB configurado';
        }

        // Agregar usuario actual como miembro si no lo es
        $usuarioActual = \Illuminate\Support\Facades\Auth::user();
        if ($usuarioActual && !$ccb->esMiembro($usuarioActual->id)) {
            $ccb->miembros()->attach($usuarioActual->id, ['rol_en_ccb' => 'Miembro']);
            return 'Usuario agregado como miembro del CCB. Recarga la página.';
        }

        return 'Usuario ya es miembro del CCB';
    })->middleware('auth');
});

require __DIR__.'/auth.php';
