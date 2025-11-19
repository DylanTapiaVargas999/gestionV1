<?php

namespace App\Http\Controllers;

use App\Models\Liberacion;
use App\Models\Proyecto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LiberacionesGlobalController extends Controller
{
    /**
     * Mostrar todas las liberaciones de proyectos donde soy líder o participo
     */
    public function index(Request $request)
    {
        // Obtener IDs de proyectos donde el usuario es líder O miembro del equipo
        $proyectosLider = Proyecto::where('creado_por', Auth::id())->pluck('id');

        // Proyectos donde soy miembro de equipo
        $proyectosMiembro = DB::table('equipos')
            ->join('miembros_equipo', 'equipos.id', '=', 'miembros_equipo.equipo_id')
            ->where('miembros_equipo.usuario_id', Auth::id())
            ->pluck('equipos.proyecto_id');

        // Combinar ambos
        $misProyectos = $proyectosLider->merge($proyectosMiembro)->unique();

        // Determinar vista
        $vista = $request->get('vista', 'agrupada'); // 'agrupada' o 'lista'

        if ($vista === 'agrupada') {
            // Vista agrupada por proyectos
            $proyectosConLiberaciones = Proyecto::whereIn('id', $misProyectos)
                ->with(['liberaciones' => function($query) use ($request) {
                    if ($request->filled('buscar')) {
                        $buscar = $request->buscar;
                        $query->where(function($q) use ($buscar) {
                            $q->where('nombre', 'like', "%{$buscar}%")
                              ->orWhere('etiqueta', 'like', "%{$buscar}%")
                              ->orWhere('descripcion', 'like', "%{$buscar}%");
                        });
                    }
                    $query->with('items')->orderBy('fecha_liberacion', 'desc');
                }])
                ->withCount('liberaciones')
                ->having('liberaciones_count', '>', 0)
                ->orderBy('nombre')
                ->get();

            // Estadísticas
            $estadisticas = [
                'total_liberaciones' => Liberacion::whereIn('proyecto_id', $misProyectos)->count(),
                'total_proyectos' => $proyectosConLiberaciones->count(),
                'liberaciones_mes' => Liberacion::whereIn('proyecto_id', $misProyectos)
                    ->whereMonth('fecha_liberacion', now()->month)
                    ->whereYear('fecha_liberacion', now()->year)
                    ->count(),
                'elementos_liberados' => DB::table('items_liberacion')
                    ->whereIn('liberacion_id', function($query) use ($misProyectos) {
                        $query->select('id')
                            ->from('liberaciones')
                            ->whereIn('proyecto_id', $misProyectos);
                    })
                    ->count(),
            ];

            return view('liberaciones.global-agrupada', compact(
                'proyectosConLiberaciones',
                'estadisticas'
            ));
        }

        // Vista de lista (original)
        $query = Liberacion::whereIn('proyecto_id', $misProyectos)
            ->with(['proyecto', 'items']);

        // Filtros
        if ($request->filled('proyecto_id')) {
            $query->where('proyecto_id', $request->proyecto_id);
        }

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                  ->orWhere('etiqueta', 'like', "%{$buscar}%")
                  ->orWhere('descripcion', 'like', "%{$buscar}%");
            });
        }

        // Ordenar - agrupar por proyecto por defecto
        $ordenar = $request->get('ordenar', 'proyecto');
        switch ($ordenar) {
            case 'nombre':
                $query->orderBy('nombre', 'asc');
                break;
            case 'proyecto':
                $query->orderBy('proyecto_id', 'asc')->orderBy('fecha_liberacion', 'desc');
                break;
            case 'antiguas':
                $query->orderBy('fecha_liberacion', 'asc');
                break;
            default: // recientes
                $query->orderBy('fecha_liberacion', 'desc');
                break;
        }

        $liberaciones = $query->get(); // Sin paginar para agrupar

        // Agrupar liberaciones por proyecto
        $liberacionesAgrupadas = $liberaciones->groupBy('proyecto_id');

        // Proyectos para el filtro (donde soy líder o miembro)
        $proyectos = Proyecto::whereIn('id', $misProyectos)
            ->orderBy('nombre')
            ->get();

        // Estadísticas
        $estadisticas = [
            'total_liberaciones' => $liberaciones->count(),
            'total_proyectos' => $misProyectos->count(),
            'liberaciones_mes' => Liberacion::whereIn('proyecto_id', $misProyectos)
                ->whereMonth('fecha_liberacion', now()->month)
                ->whereYear('fecha_liberacion', now()->year)
                ->count(),
            'elementos_liberados' => DB::table('items_liberacion')
                ->whereIn('liberacion_id', function($query) use ($misProyectos) {
                    $query->select('id')
                        ->from('liberaciones')
                        ->whereIn('proyecto_id', $misProyectos);
                })
                ->count(),
        ];

        return view('liberaciones.global-index', compact(
            'liberaciones',
            'liberacionesAgrupadas',
            'proyectos',
            'estadisticas'
        ));
    }
}
