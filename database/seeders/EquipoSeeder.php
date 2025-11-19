<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Proyecto;
use App\Models\Equipo;
use App\Models\Usuario;

class EquipoSeeder extends Seeder
{
    public function run(): void
    {
        $scrumProject = Proyecto::where('codigo', 'SCRUM-DEMO')->first();
        $cascadaProject = Proyecto::where('codigo', 'CASCADA-DEMO')->first();
        $admin = Usuario::where('correo', 'admin@demo.com')->first();
        $lider = Usuario::where('correo', 'lider@demo.com')->first();
        $dev1 = Usuario::where('correo', 'dev1@demo.com')->first();
        $dev2 = Usuario::where('correo', 'dev2@demo.com')->first();
        $tester = Usuario::where('correo', 'tester@demo.com')->first();

        if (!$scrumProject || !$cascadaProject) return;

        $rolLider = DB::table('roles')->where('nombre', 'lider')->first();
        $rolDev = DB::table('roles')->where('nombre', 'desarrollador')->first();
        $rolTester = DB::table('roles')->where('nombre', 'tester')->first();

        // Equipo Scrum
        $equipoScrum = Equipo::firstOrCreate(
            ['nombre' => 'Equipo Scrum', 'proyecto_id' => $scrumProject->id],
            [
                'id' => Str::uuid()->toString(),
                'lider_id' => $admin->id,
            ]
        );
        if ($lider && $rolDev) {
            DB::table('miembros_equipo')->insertOrIgnore([
                'equipo_id' => $equipoScrum->id,
                'usuario_id' => $lider->id,
                'rol_id' => $rolDev->id,
            ]);
        }
        if ($dev1 && $rolDev) {
            DB::table('miembros_equipo')->insertOrIgnore([
                'equipo_id' => $equipoScrum->id,
                'usuario_id' => $dev1->id,
                'rol_id' => $rolDev->id,
            ]);
        }
        if ($tester && $rolTester) {
            DB::table('miembros_equipo')->insertOrIgnore([
                'equipo_id' => $equipoScrum->id,
                'usuario_id' => $tester->id,
                'rol_id' => $rolTester->id,
            ]);
        }

        // Equipo Cascada
        $equipoCascada = Equipo::firstOrCreate(
            ['nombre' => 'Equipo Cascada', 'proyecto_id' => $cascadaProject->id],
            [
                'id' => Str::uuid()->toString(),
                'lider_id' => $lider->id,
            ]
        );
        if ($admin && $rolLider) {
            DB::table('miembros_equipo')->insertOrIgnore([
                'equipo_id' => $equipoCascada->id,
                'usuario_id' => $admin->id,
                'rol_id' => $rolLider->id,
            ]);
        }
        if ($dev2 && $rolDev) {
            DB::table('miembros_equipo')->insertOrIgnore([
                'equipo_id' => $equipoCascada->id,
                'usuario_id' => $dev2->id,
                'rol_id' => $rolDev->id,
            ]);
        }
        if ($tester && $rolTester) {
            DB::table('miembros_equipo')->insertOrIgnore([
                'equipo_id' => $equipoCascada->id,
                'usuario_id' => $tester->id,
                'rol_id' => $rolTester->id,
            ]);
        }
    }
}
