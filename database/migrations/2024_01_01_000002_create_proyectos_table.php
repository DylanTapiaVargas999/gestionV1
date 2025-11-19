<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tabla de metodologías
        Schema::create('metodologias', function (Blueprint $table) {
            $table->id('id_metodologia');
            $table->string('nombre', 100);
            $table->string('tipo', 50)->nullable();
            $table->string('descripcion', 255)->nullable();
        });

        // Tabla de fases de metodología
        Schema::create('fases_metodologia', function (Blueprint $table) {
            $table->id('id_fase');
            $table->unsignedBigInteger('id_metodologia');
            $table->string('nombre_fase', 100);
            $table->integer('orden')->nullable();
            $table->string('descripcion', 255)->nullable();
            $table->foreign('id_metodologia')->references('id_metodologia')->on('metodologias');
        });

        // Tabla de proyectos
        Schema::create('proyectos', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->string('codigo', 50)->unique();
            $table->string('nombre', 255);
            $table->text('descripcion')->nullable();
            $table->unsignedBigInteger('id_metodologia');
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->string('link_repositorio', 255)->nullable();
            $table->char('creado_por', 36);
            $table->timestamp('creado_en')->useCurrent();
            $table->timestamp('actualizado_en')->useCurrent()->useCurrentOnUpdate();
            $table->foreign('id_metodologia')->references('id_metodologia')->on('metodologias');
            $table->foreign('creado_por')->references('id')->on('usuarios');
        });

        // Agregar FKs pendientes ahora que proyectos existe
        Schema::table('usuarios_roles', function (Blueprint $table) {
            $table->foreign('proyecto_id')->references('id')->on('proyectos')->onDelete('cascade');
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->foreign('metodologia_id')->references('id_metodologia')->on('metodologias')->onDelete('cascade');
        });

        // Tabla de equipos
        Schema::create('equipos', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->char('proyecto_id', 36);
            $table->string('nombre', 255);
            $table->char('lider_id', 36);
            $table->foreign('proyecto_id')->references('id')->on('proyectos');
            $table->foreign('lider_id')->references('id')->on('usuarios');
        });

        // Tabla de miembros de equipo
        Schema::create('miembros_equipo', function (Blueprint $table) {
            $table->char('equipo_id', 36);
            $table->char('usuario_id', 36);
            $table->unsignedBigInteger('rol_id');
            $table->primary(['equipo_id', 'usuario_id', 'rol_id']);
            $table->foreign('equipo_id')->references('id')->on('equipos');
            $table->foreign('usuario_id')->references('id')->on('usuarios');
            $table->foreign('rol_id')->references('id')->on('roles');
        });

        // Tabla de tareas de proyecto
        Schema::create('tareas_proyecto', function (Blueprint $table) {
            $table->id('id_tarea');

            // Información básica
            $table->string('nombre', 255);
            $table->text('descripcion')->nullable();

            // Relaciones principales
            $table->char('id_proyecto', 36);
            $table->unsignedBigInteger('id_fase');
            $table->unsignedBigInteger('id_sprint')->nullable()->comment('FK a sprints - Solo para Scrum');
            $table->char('id_ec', 36)->nullable();
            $table->char('responsable', 36)->nullable();

            // Fechas
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->date('fecha_inicio_original')->nullable()->comment('Fecha original antes de ajustes automáticos');
            $table->date('fecha_fin_original')->nullable()->comment('Fecha original antes de ajustes automáticos');

            // Estado y prioridad
            $table->string('estado', 50)->nullable();
            $table->integer('prioridad')->default(3);

            // Campos ESPECÍFICOS por metodología (NULLABLES)
            // Para SCRUM:
            $table->integer('story_points')->nullable()->comment('Solo para Scrum');

            // Para CASCADA:
            $table->decimal('horas_estimadas', 8, 2)->nullable()->comment('Más usado en Cascada');
            $table->string('entregable', 255)->nullable()->comment('Específico de Cascada');

            // Campos para cronograma inteligente
            $table->integer('duracion_minima')->nullable()->comment('Duración mínima posible en días');
            $table->boolean('es_ruta_critica')->default(false)->comment('Indica si la tarea está en la ruta crítica');
            $table->integer('holgura_dias')->default(0)->comment('Días de holgura (slack) - 0 para ruta crítica');
            $table->boolean('puede_paralelizarse')->default(false)->comment('Indica si la tarea puede ejecutarse en paralelo con otras');
            $table->json('dependencias')->nullable()->comment('IDs de tareas de las que depende esta tarea');
            $table->decimal('progreso_real', 5, 2)->default(0)->comment('Porcentaje de progreso real (0-100)');

            // Campos COMUNES (ambas metodologías)
            $table->json('criterios_aceptacion')->nullable();
            $table->text('notas')->nullable();

            // Campo para almacenar la URL del commit cuando el desarrollador completa la tarea
            $table->text('commit_url')->nullable();

            // Campo para almacenar el ID del commit registrado (opcional)
            $table->char('commit_id', 36)->nullable();

            // Usuario que creó la tarea
            $table->char('creado_por', 36)->nullable();

            // Timestamps
            $table->timestamp('creado_en')->useCurrent();
            $table->timestamp('actualizado_en')->useCurrent()->useCurrentOnUpdate();

            // Foreign keys
            $table->foreign('id_proyecto')->references('id')->on('proyectos');
            $table->foreign('id_fase')->references('id_fase')->on('fases_metodologia');
            // FK id_sprint se agregará después cuando exista tabla sprints
            // FK a elementos_configuracion se añadirá después
            $table->foreign('responsable')->references('id')->on('usuarios');
            $table->foreign('creado_por')->references('id')->on('usuarios')->onDelete('set null');
            // FK commit_id se agregará después cuando exista tabla commits_repositorio

            // Índices para optimizar consultas
            $table->index('es_ruta_critica');
            $table->index(['fecha_inicio', 'fecha_fin']);
            $table->index('id_sprint');
        });

        // Tabla de plantillas EC (catálogo de EC base por metodología)
        Schema::create('plantillas_ec', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('metodologia_id');
            $table->string('nombre', 255);
            $table->enum('tipo', ['DOCUMENTO', 'CODIGO', 'SCRIPT_BD', 'CONFIGURACION', 'OTRO'])->default('DOCUMENTO');
            $table->text('descripcion')->nullable();
            $table->integer('orden')->default(1);
            $table->boolean('es_recomendado')->default(true);
            // Tarea base asociada
            $table->string('tarea_nombre', 255)->nullable();
            $table->text('tarea_descripcion')->nullable();
            // Distribución de fechas (porcentajes 0-100)
            $table->decimal('porcentaje_inicio', 5, 2)->default(0.00);
            $table->decimal('porcentaje_fin', 5, 2)->default(100.00);
            // Relaciones predefinidas (JSON: [{"id_plantilla_dependencia": 1, "tipo": "DEPENDE_DE"}, ...])
            $table->json('relaciones')->nullable();
            $table->timestamps();
            $table->foreign('metodologia_id')->references('id_metodologia')->on('metodologias')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plantillas_ec');
        Schema::dropIfExists('tareas_proyecto');
        Schema::dropIfExists('miembros_equipo');
        Schema::dropIfExists('equipos');

        Schema::table('usuarios_roles', function (Blueprint $table) {
            $table->dropForeign(['proyecto_id']);
        });

        Schema::dropIfExists('proyectos');
        Schema::dropIfExists('fases_metodologia');
        Schema::dropIfExists('metodologias');
    }
};
