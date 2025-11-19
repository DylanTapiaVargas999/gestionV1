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
        Schema::create('historial_ajustes_tareas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ajuste_id');
            $table->unsignedBigInteger('tarea_id');

            // Valores anteriores
            $table->date('fecha_inicio_anterior')->nullable();
            $table->date('fecha_fin_anterior')->nullable();
            $table->integer('duracion_anterior')->nullable();
            $table->char('responsable_anterior', 36)->nullable();
            $table->decimal('horas_estimadas_anterior', 8, 2)->nullable();

            // Nuevos valores
            $table->date('fecha_inicio_nueva')->nullable();
            $table->date('fecha_fin_nueva')->nullable();
            $table->integer('duracion_nueva')->nullable();
            $table->char('responsable_nuevo', 36)->nullable();
            $table->decimal('horas_estimadas_nueva', 8, 2)->nullable();

            // Metadatos
            $table->string('tipo_cambio', 50); // 'compresion', 'reasignacion', 'fechas', 'paralelizacion'
            $table->text('impacto_estimado')->nullable();
            $table->boolean('aplicado')->default(false);

            $table->timestamps();

            // Foreign keys
            $table->foreign('ajuste_id')->references('id')->on('ajustes_cronograma')->onDelete('cascade');
            $table->foreign('tarea_id')->references('id_tarea')->on('tareas_proyecto')->onDelete('cascade');
            $table->foreign('responsable_anterior')->references('id')->on('usuarios')->onDelete('set null');
            $table->foreign('responsable_nuevo')->references('id')->on('usuarios')->onDelete('set null');

            $table->index('ajuste_id');
            $table->index('tarea_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historial_ajustes_tareas');
    }
};
