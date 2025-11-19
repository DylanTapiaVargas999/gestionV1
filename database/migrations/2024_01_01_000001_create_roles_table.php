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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100)->unique();
            $table->string('descripcion', 255)->nullable();
            $table->boolean('es_para_ccb')->default(false)->comment('Indica si el rol puede ser miembro del CCB');
            $table->unsignedBigInteger('metodologia_id')->nullable()->comment('Metodología específica del rol (null = rol genérico)');
            // FK a metodologias se agregará después
            $table->timestamps();
        });

        Schema::create('usuarios_roles', function (Blueprint $table) {
            $table->id();
            $table->char('usuario_id', 36);
            $table->unsignedBigInteger('rol_id');
            $table->char('proyecto_id', 36);
            $table->foreign('usuario_id')->references('id')->on('usuarios')->onDelete('cascade');
            $table->foreign('rol_id')->references('id')->on('roles')->onDelete('restrict');
            // FK a proyectos se agregará después cuando exista la tabla
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios_roles');
        Schema::dropIfExists('roles');
    }
};
