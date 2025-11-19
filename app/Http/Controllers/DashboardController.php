<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use App\Models\Usuario;
use App\Models\Equipo;
use App\Models\MiembroEquipo;
use App\Models\Metodologia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $usuarioId = Auth::user()->id;

        // 📊 INDICADORES GENERALES

        // 1. TODOS mis proyectos (creados + asignados sin duplicar)
        $misProyectosIds = Proyecto::where('creado_por', $usuarioId)
            ->orWhereHas('equipos.miembros', function ($query) use ($usuarioId) {
                $query->where('usuario_id', $usuarioId);
            })
            ->pluck('id');

        // Contador único: Total de proyectos donde pertenezco
        $totalMisProyectos = $misProyectosIds->count();

        // 2. Total de liberaciones en mis proyectos
        $totalLiberaciones = DB::table('liberaciones')
            ->whereIn('proyecto_id', $misProyectosIds)
            ->count();

        // 5. Liberaciones este mes
        $liberacionesMes = DB::table('liberaciones')
            ->whereIn('proyecto_id', $misProyectosIds)
            ->whereMonth('fecha_liberacion', now()->month)
            ->whereYear('fecha_liberacion', now()->year)
            ->count();

        return view('dashboard', compact(
            'totalMisProyectos',
            'totalLiberaciones',
            'liberacionesMes'
        ));
    }
}
