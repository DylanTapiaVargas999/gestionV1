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
        Schema::create('ajustes_cronograma', function (Blueprint $table) {
            $table->id();
            $table->string('proyecto_id');
            $table->enum('tipo_ajuste', ['manual', 'automatico', 'solicitud_cambio'])->default('automatico');
            $table->enum('estado', ['propuesto', 'aprobado', 'aplicado', 'rechazado', 'revertido'])->default('propuesto');

            // Datos del análisis
            $table->json('desviaciones_detectadas')->nullable();
            $table->json('ruta_critica')->nullable();
            $table->json('recursos_sobrecargados')->nullable();

            // Solución seleccionada
            $table->string('estrategia', 50)->nullable(); // 'compresion', 'paralelizacion', 'reasignacion', 'mixta'
            $table->json('ajustes_propuestos')->nullable();
            $table->json('ajustes_aplicados')->nullable();

            // Métricas
            $table->integer('dias_recuperados')->default(0);
            $table->integer('recursos_afectados')->default(0);
            $table->decimal('score_solucion', 5, 2)->nullable();
            $table->decimal('costo_adicional_estimado', 8, 2)->nullable();

            // Aprobación
            $table->char('aprobado_por', 36)->nullable();
            $table->timestamp('aprobado_en')->nullable();
            $table->text('motivo_ajuste')->nullable();
            $table->text('notas_rechazo')->nullable();

            // Auditoría
            $table->char('creado_por', 36)->nullable();
            $table->timestamps();

            // Índices y foreign keys
            $table->foreign('proyecto_id')->references('id')->on('proyectos')->onDelete('cascade');
            $table->foreign('aprobado_por')->references('id')->on('usuarios')->onDelete('set null');
            $table->foreign('creado_por')->references('id')->on('usuarios')->onDelete('set null');

            $table->index(['proyecto_id', 'estado']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ajustes_cronograma');
    }
};
