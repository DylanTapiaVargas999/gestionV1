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
        Schema::create('sprints', function (Blueprint $table) {
            $table->id('id_sprint');
            $table->char('id_proyecto', 36);
            $table->string('nombre', 100); // 'Sprint 1', 'Sprint 2', etc.
            $table->text('objetivo')->nullable(); // Objetivo del sprint
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->integer('velocidad_estimada')->nullable()->comment('Story points planeados');
            $table->integer('velocidad_real')->nullable()->comment('Story points completados');
            $table->enum('estado', ['planificado', 'activo', 'completado', 'cancelado'])->default('planificado');
            $table->text('observaciones')->nullable();
            $table->timestamp('creado_en')->useCurrent();
            $table->timestamp('actualizado_en')->useCurrent()->useCurrentOnUpdate();

            // Índices y foreign keys
            $table->foreign('id_proyecto')->references('id')->on('proyectos')->onDelete('cascade');
            $table->index(['id_proyecto', 'estado']);
            $table->index('fecha_inicio');
        });

        // Agregar FK de id_sprint en tareas_proyecto ahora que sprints existe
        Schema::table('tareas_proyecto', function (Blueprint $table) {
            $table->foreign('id_sprint')->references('id_sprint')->on('sprints')->onDelete('set null');
        });

        // Agregar FK de id_sprint en impedimentos ahora que sprints existe
        Schema::table('impedimentos', function (Blueprint $table) {
            $table->foreign('id_sprint')->references('id_sprint')->on('sprints')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sprints');
    }
};
