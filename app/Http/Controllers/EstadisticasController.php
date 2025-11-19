<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use App\Models\TareaProyecto;
use App\Models\Liberacion;
use App\Models\ItemCambio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EstadisticasController extends Controller
{
    public function index()
    {
        $usuario = Auth::user();

        // Proyectos donde el usuario es creador o miembro de equipo
        $misProyectosIds = Proyecto::where('creado_por', $usuario->id)
            ->orWhereHas('equipos.miembros', function ($query) use ($usuario) {
                $query->where('usuario_id', $usuario->id);
            })
            ->pluck('id');

        // ============================================
        // 📊 ESTADÍSTICAS GENERALES
        // ============================================

        $totalProyectos = $misProyectosIds->count();

        // Contar proyectos sin usar columna 'estado' (no existe en BD)
        $proyectosActivos = $totalProyectos;
        $proyectosCompletados = 0;
        $proyectosPausados = 0;

        // ============================================
        // 📈 PROGRESO DE PROYECTOS (para gráfica de barras)
        // ============================================

        $proyectosConProgreso = Proyecto::whereIn('id', $misProyectosIds)
            ->select('id', 'codigo', 'nombre')
            ->withCount([
                'tareas as total_tareas',
                'tareas as tareas_completadas' => function ($query) {
                    $query->where('estado', 'Completada');
                }
            ])
            ->get()
            ->map(function ($proyecto) {
                $progreso = $proyecto->total_tareas > 0
                    ? round(($proyecto->tareas_completadas / $proyecto->total_tareas) * 100, 1)
                    : 0;

                return [
                    'codigo' => $proyecto->codigo,
                    'nombre' => $proyecto->nombre,
                    'progreso' => $progreso,
                    'tareas_completadas' => $proyecto->tareas_completadas,
                    'total_tareas' => $proyecto->total_tareas,
                ];
            });

        // ============================================
        // 🎯 TAREAS - MÉTRICAS DE PRODUCTIVIDAD
        // ============================================

        $totalTareas = TareaProyecto::whereIn('id_proyecto', $misProyectosIds)->count();

        $tareasCompletadas = TareaProyecto::whereIn('id_proyecto', $misProyectosIds)
            ->where('estado', 'Completada')
            ->count();

        $tareasPendientes = TareaProyecto::whereIn('id_proyecto', $misProyectosIds)
            ->where('estado', 'Pendiente')
            ->count();

        $tareasEnProgreso = TareaProyecto::whereIn('id_proyecto', $misProyectosIds)
            ->where('estado', 'En Progreso')
            ->count();

        // Distribución de tareas por estado (para gráfica de pastel)
        $distribucionTareas = [
            ['estado' => 'Completadas', 'cantidad' => $tareasCompletadas],
            ['estado' => 'En Progreso', 'cantidad' => $tareasEnProgreso],
            ['estado' => 'Pendientes', 'cantidad' => $tareasPendientes],
        ];

        // ============================================
        // 🚀 LIBERACIONES
        // ============================================

        $totalLiberaciones = Liberacion::whereIn('proyecto_id', $misProyectosIds)->count();

        // Liberaciones por mes (últimos 6 meses) - para gráfica de línea
        $liberacionesPorMes = DB::table('liberaciones')
            ->whereIn('proyecto_id', $misProyectosIds)
            ->select(
                DB::raw('MONTH(fecha_liberacion) as mes'),
                DB::raw('YEAR(fecha_liberacion) as anio'),
                DB::raw('COUNT(*) as cantidad')
            )
            ->where('fecha_liberacion', '>=', now()->subMonths(6))
            ->groupBy('anio', 'mes')
            ->orderBy('anio', 'asc')
            ->orderBy('mes', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'mes' => \Carbon\Carbon::create($item->anio, $item->mes)->format('M Y'),
                    'cantidad' => $item->cantidad,
                ];
            });

        // ============================================
        // 🔄 ÍTEMS DE CAMBIO (a través de solicitudes_cambio)
        // ============================================

        $totalCambios = DB::table('items_cambio')
            ->join('solicitudes_cambio', 'items_cambio.solicitud_cambio_id', '=', 'solicitudes_cambio.id')
            ->whereIn('solicitudes_cambio.proyecto_id', $misProyectosIds)
            ->count();

        $cambiosAprobados = DB::table('items_cambio')
            ->join('solicitudes_cambio', 'items_cambio.solicitud_cambio_id', '=', 'solicitudes_cambio.id')
            ->whereIn('solicitudes_cambio.proyecto_id', $misProyectosIds)
            ->where('solicitudes_cambio.estado', 'APROBADA')
            ->count();

        $cambiosPendientes = DB::table('items_cambio')
            ->join('solicitudes_cambio', 'items_cambio.solicitud_cambio_id', '=', 'solicitudes_cambio.id')
            ->whereIn('solicitudes_cambio.proyecto_id', $misProyectosIds)
            ->whereIn('solicitudes_cambio.estado', ['ABIERTA', 'EN_REVISION'])
            ->count();

        // ============================================
        // 📅 PROYECTOS POR METODOLOGÍA
        // ============================================

        $proyectosPorMetodologia = Proyecto::whereIn('id', $misProyectosIds)
            ->join('metodologias', 'proyectos.id_metodologia', '=', 'metodologias.id_metodologia')
            ->select('metodologias.nombre', DB::raw('COUNT(*) as cantidad'))
            ->groupBy('metodologias.nombre')
            ->get();

        return view('estadisticas.index', compact(
            'totalProyectos',
            'proyectosActivos',
            'proyectosCompletados',
            'proyectosPausados',
            'proyectosConProgreso',
            'totalTareas',
            'tareasCompletadas',
            'tareasPendientes',
            'tareasEnProgreso',
            'distribucionTareas',
            'totalLiberaciones',
            'liberacionesPorMes',
            'totalCambios',
            'cambiosAprobados',
            'cambiosPendientes',
            'proyectosPorMetodologia'
        ));
    }
}
