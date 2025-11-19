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
        Schema::create('daily_scrums', function (Blueprint $table) {
            $table->id('id_daily');
            $table->unsignedBigInteger('id_sprint');
            $table->char('id_usuario', 36);
            $table->date('fecha');
            $table->json('que_hice_ayer')->nullable()->comment('Array de tareas completadas ayer');
            $table->json('que_hare_hoy')->nullable()->comment('Array de tareas planificadas para hoy');
            $table->json('impedimentos')->nullable()->comment('Array de impedimentos reportados');
            $table->text('notas_adicionales')->nullable();
            $table->timestamp('creado_en')->useCurrent();
            $table->timestamp('actualizado_en')->useCurrent()->useCurrentOnUpdate();

            // Foreign keys
            $table->foreign('id_sprint')->references('id_sprint')->on('sprints')->onDelete('cascade');
            $table->foreign('id_usuario')->references('id')->on('usuarios')->onDelete('cascade');

            // Índices
            $table->unique(['id_sprint', 'id_usuario', 'fecha'], 'daily_scrum_unique');
            $table->index('fecha');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_scrums');
    }
};
