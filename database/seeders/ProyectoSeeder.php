<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Proyecto;
use App\Models\Usuario;

class ProyectoSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Usuario::where('correo', 'admin@demo.com')->first();
        $lider = Usuario::where('correo', 'lider@demo.com')->first();
        if (!$admin || !$lider) return;

        // Obtener metodologías
        $scrum = DB::table('metodologias')->where('nombre', 'Scrum')->first();
        $cascada = DB::table('metodologias')->where('nombre', 'Cascada')->first();
        if (!$scrum || !$cascada) return;

        // Proyecto Scrum
        Proyecto::firstOrCreate(['codigo' => 'SCRUM-DEMO'], [
            'id' => Str::uuid()->toString(),
            'codigo' => 'SCRUM-DEMO',
            'nombre' => 'Sistema de Gestión Ágil',
            'descripcion' => 'Proyecto demo con metodología Scrum para gestión de pedidos',
            'id_metodologia' => $scrum->id_metodologia,
            'link_repositorio' => 'https://github.com/demo/scrum-project',
            'creado_por' => $admin->id,
        ]);

        // Proyecto Cascada
        Proyecto::firstOrCreate(['codigo' => 'CASCADA-DEMO'], [
            'id' => Str::uuid()->toString(),
            'codigo' => 'CASCADA-DEMO',
            'nombre' => 'Sistema ERP Empresarial',
            'descripcion' => 'Proyecto demo con metodología Cascada para sistema empresarial',
            'id_metodologia' => $cascada->id_metodologia,
            'link_repositorio' => 'https://github.com/demo/cascada-project',
            'creado_por' => $lider->id,
        ]);
    }
}
