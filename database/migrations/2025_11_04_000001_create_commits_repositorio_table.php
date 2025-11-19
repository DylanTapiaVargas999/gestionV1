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
        Schema::create('commits_repositorio', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->text('url_repositorio'); // URL del repositorio (ej: https://github.com/user/repo)
            $table->string('hash_commit', 191); // SHA del commit (varchar so it can be indexed)
            $table->char('ec_id', 36)->nullable(); // Relación con elemento de configuración

            // Campos opcionales que se consultan dinámicamente desde GitHub API
            // Estos campos se pueden cachear temporalmente pero no son obligatorios
            $table->text('autor')->nullable();
            $table->text('mensaje')->nullable();
            $table->timestamp('fecha_commit')->nullable();

            // Timestamps para control
            $table->timestamps();

            // Foreign keys
            $table->foreign('ec_id')
                ->references('id')
                ->on('elementos_configuracion')
                ->onDelete('cascade');

            // Índices
            $table->index('ec_id');
            $table->index('hash_commit');
        });

        // Agregar FK de commit_id en tareas_proyecto ahora que commits_repositorio existe
        Schema::table('tareas_proyecto', function (Blueprint $table) {
            $table->foreign('commit_id')->references('id')->on('commits_repositorio')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commits_repositorio');
    }
};
