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
        Schema::create('impedimentos', function (Blueprint $table) {
            $table->id('id_impedimento');
            $table->char('id_proyecto', 36);
            $table->unsignedBigInteger('id_sprint')->nullable();
            $table->char('id_usuario_reporta', 36);
            $table->char('id_usuario_asignado', 36)->nullable();
            $table->string('titulo', 255);
            $table->text('descripcion');
            $table->enum('prioridad', ['baja', 'media', 'alta', 'critica'])->default('media');
            $table->enum('estado', ['abierto', 'en_progreso', 'resuelto', 'cerrado'])->default('abierto');
            $table->timestamp('fecha_reporte')->useCurrent();
            $table->timestamp('fecha_resolucion')->nullable();
            $table->text('solucion')->nullable();
            $table->timestamp('creado_en')->useCurrent();
            $table->timestamp('actualizado_en')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('id_proyecto')->references('id')->on('proyectos');
            // FK id_sprint se agregará después cuando exista tabla sprints
            $table->foreign('id_usuario_reporta')->references('id')->on('usuarios');
            $table->foreign('id_usuario_asignado')->references('id')->on('usuarios');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('impedimentos');
    }
};
