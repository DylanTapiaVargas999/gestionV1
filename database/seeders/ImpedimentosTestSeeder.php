<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ImpedimentosTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $proyecto = \App\Models\Proyecto::first();
        $usuario = \App\Models\Usuario::first();

        if ($proyecto && $usuario) {
            \App\Models\Impedimento::create([
                'id_proyecto' => $proyecto->id,
                'id_usuario_reporta' => $usuario->id,
                'titulo' => 'Problema con validación de email',
                'descripcion' => 'La validación de email no funciona correctamente en el formulario de registro',
                'prioridad' => 'alta',
                'estado' => 'resuelto',
                'fecha_reporte' => now()->subDays(5),
                'fecha_resolucion' => now()->subDays(2),
                'solucion' => 'Se implementó una nueva función de validación usando expresiones regulares más robustas'
            ]);

            \App\Models\Impedimento::create([
                'id_proyecto' => $proyecto->id,
                'id_usuario_reporta' => $usuario->id,
                'titulo' => 'Error 500 en API de pedidos',
                'descripcion' => 'La API de pedidos devuelve error 500 cuando se intenta crear un pedido sin items',
                'prioridad' => 'critica',
                'estado' => 'resuelto',
                'fecha_reporte' => now()->subDays(3),
                'fecha_resolucion' => now()->subDay(),
                'solucion' => 'Se agregó validación en el controlador para verificar que el pedido tenga al menos un item antes de procesarlo'
            ]);

            \App\Models\Impedimento::create([
                'id_proyecto' => $proyecto->id,
                'id_usuario_reporta' => $usuario->id,
                'titulo' => 'Problema de rendimiento en dashboard',
                'descripcion' => 'El dashboard carga muy lento cuando hay muchos datos',
                'prioridad' => 'media',
                'estado' => 'cerrado',
                'fecha_reporte' => now()->subDays(7),
                'fecha_resolucion' => now()->subDays(1),
                'solucion' => 'Se implementó paginación y optimización de consultas SQL'
            ]);
        }
    }
}
