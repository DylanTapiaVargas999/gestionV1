<?php

namespace App\Http\Controllers\GestionProyectos;

use App\Http\Controllers\Controller;
use App\Models\Proyecto;
use App\Models\AjusteCronograma;
use App\Services\CronogramaInteligenteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class CronogramaInteligenteController extends Controller
{
    protected $cronogramaService;

    public function __construct(CronogramaInteligenteService $cronogramaService)
    {
        $this->cronogramaService = $cronogramaService;
    }

    /**
     * Dashboard principal del cronograma inteligente
     */
    public function dashboard(Proyecto $proyecto)
    {
        // Analizar cronograma actual
        $analisis = $this->cronogramaService->analizarCronograma($proyecto);

        // Obtener ajustes pendientes y recientes
        $ajustesPendientes = AjusteCronograma::where('proyecto_id', $proyecto->id)
            ->where('estado', 'propuesto')
            ->with(['creador', 'historialTareas'])
            ->latest()
            ->get();

        $ajustesRecientes = AjusteCronograma::where('proyecto_id', $proyecto->id)
            ->whereIn('estado', ['aprobado', 'aplicado', 'rechazado'])
            ->with(['creador', 'aprobador', 'historialTareas'])
            ->latest()
            ->take(5)
            ->get();

        return view('cronograma.dashboard', compact(
            'proyecto',
            'analisis',
            'ajustesPendientes',
            'ajustesRecientes'
        ));
    }    /**
     * Ejecutar análisis automático
     */
    public function analizar(Proyecto $proyecto)
    {
        $analisis = $this->cronogramaService->analizarCronograma($proyecto);

        return response()->json([
            'success' => true,
            'analisis' => $analisis,
        ]);
    }

    /**
     * Generar ajuste automático
     */
    public function generar(Request $request, Proyecto $proyecto)
    {
        try {
            Log::info('Iniciando generación de ajuste automático', [
                'proyecto_id' => $proyecto->id,
                'proyecto_nombre' => $proyecto->nombre,
                'usuario_id' => Auth::id(),
            ]);

            $opciones = $request->only([
                'permitir_compresion',
                'permitir_paralelizacion',
                'permitir_reasignacion',
                'max_compresion_porcentaje',
                'max_aumento_horas',
            ]);
            
            if (!is_array($opciones)) {
                $opciones = [];
            }
            
            // Convertir strings a booleanos
            foreach (['permitir_compresion', 'permitir_paralelizacion', 'permitir_reasignacion'] as $key) {
                if (isset($opciones[$key])) {
                    $opciones[$key] = filter_var($opciones[$key], FILTER_VALIDATE_BOOLEAN);
                }
            }

            // Agregar estrategia a las opciones si existe
            if ($request->has('estrategia')) {
                $opciones['estrategia'] = $request->estrategia;
            }

            Log::info('Opciones de ajuste', ['opciones' => $opciones]);

            // Generar el ajuste
            $ajuste = $this->cronogramaService->generarAjuste($proyecto, $opciones);

            if (!$ajuste) {
                Log::warning('No se generó ajuste - sin problemas detectados', [
                    'proyecto_id' => $proyecto->id,
                ]);
                return redirect()->back()->with('warning', 'No se detectaron problemas que requieran ajuste automático. El cronograma está óptimo.');
            }

            Log::info('Ajuste generado exitosamente', [
                'ajuste_id' => $ajuste->id,
                'estrategia' => $ajuste->estrategia,
                'dias_recuperados' => $ajuste->dias_recuperados,
            ]);

            return redirect()
                ->route('proyectos.cronograma.ver-ajuste', ['proyecto' => $proyecto, 'ajuste' => $ajuste])
                ->with('success', '✨ Ajuste generado exitosamente. Revisa la propuesta.');
                
        } catch (\Exception $e) {
            Log::error('Error al generar ajuste', [
                'proyecto_id' => $proyecto->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return redirect()->back()->with('error', 'Error al generar el ajuste: ' . $e->getMessage());
        }
    }    /**
     * Simular ajuste (preview sin guardar)
     */
    public function simular(Proyecto $proyecto)
    {
        $simulacion = $this->cronogramaService->simularAjuste($proyecto);

        return response()->json([
            'success' => true,
            'simulacion' => $simulacion,
        ]);
    }

    /**
     * Ver detalle de un ajuste
     */
    public function verAjuste(Proyecto $proyecto, AjusteCronograma $ajuste)
    {
        $ajuste->load(['creador', 'aprobador', 'historialTareas.tarea.fase']);

        return view('cronograma.ver-ajuste', compact('proyecto', 'ajuste'));
    }

    /**
     * Aprobar ajuste propuesto
     */
    public function aprobar(Request $request, Proyecto $proyecto, AjusteCronograma $ajuste)
    {
        if ($ajuste->proyecto_id !== $proyecto->id) {
            abort(403, 'Este ajuste no pertenece al proyecto actual');
        }

        $success = $this->cronogramaService->aprobarAjuste(
            $ajuste,
            Auth::id()
        );

        if (!$success) {
            return redirect()->back()->with('error', 'No se pudo aprobar el ajuste. Verifica su estado.');
        }

        // Preguntar si desea aplicar inmediatamente
        if ($request->input('aplicar_inmediatamente')) {
            return $this->aplicar($request, $proyecto, $ajuste);
        }

        return redirect()
            ->route('proyectos.cronograma.ver-ajuste', ['proyecto' => $proyecto, 'ajuste' => $ajuste])
            ->with('success', '✅ Ajuste aprobado exitosamente.');
    }

    /**
     * Rechazar ajuste propuesto
     */
    public function rechazar(Request $request, Proyecto $proyecto, AjusteCronograma $ajuste)
    {
        if ($ajuste->proyecto_id !== $proyecto->id) {
            abort(403, 'Este ajuste no pertenece al proyecto actual');
        }

        $success = $this->cronogramaService->rechazarAjuste(
            $ajuste,
            $request->input('comentarios', 'Sin comentarios'),
            Auth::id()
        );

        if (!$success) {
            return redirect()->back()->with('error', 'No se pudo rechazar el ajuste.');
        }

        return redirect()
            ->route('proyectos.cronograma.dashboard', $proyecto)
            ->with('info', '❌ Ajuste rechazado.');
    }

    /**
     * Aplicar ajuste aprobado
     */
    public function aplicar(Request $request, Proyecto $proyecto, AjusteCronograma $ajuste)
    {
        if ($ajuste->proyecto_id !== $proyecto->id) {
            abort(403, 'Este ajuste no pertenece al proyecto actual');
        }

        $success = $this->cronogramaService->aplicarAjuste($ajuste);

        if (!$success) {
            return redirect()->back()->with('error', 'No se pudo aplicar el ajuste. Verifica que esté aprobado.');
        }

        return redirect()
            ->route('proyectos.cronograma.dashboard', $proyecto)
            ->with('success', '🚀 Ajuste aplicado exitosamente. El cronograma ha sido actualizado.');
    }

    /**
     * Revertir ajuste aplicado
     */
    public function revertir(Request $request, Proyecto $proyecto, AjusteCronograma $ajuste)
    {
        if ($ajuste->proyecto_id !== $proyecto->id) {
            abort(403, 'Este ajuste no pertenece al proyecto actual');
        }

        $success = $this->cronogramaService->revertirAjuste($ajuste);

        if (!$success) {
            return redirect()->back()->with('error', 'No se pudo revertir el ajuste.');
        }

        return redirect()
            ->route('proyectos.cronograma.dashboard', $proyecto)
            ->with('success', '↶ Ajuste revertido. El cronograma ha sido restaurado.');
    }

    /**
     * Historial de ajustes del proyecto
     */
    public function historial(Proyecto $proyecto, Request $request)
    {
        $query = AjusteCronograma::where('proyecto_id', $proyecto->id);

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('estrategia')) {
            $query->where('estrategia', $request->estrategia);
        }

        if ($request->filled('tipo')) {
            $query->where('tipo_ajuste', $request->tipo);
        }

        $ajustes = $query->with(['creador', 'aprobador', 'historialTareas'])
            ->latest()
            ->paginate(15);

        $estadisticas = [
            'propuestos' => AjusteCronograma::where('proyecto_id', $proyecto->id)->where('estado', 'propuesto')->count(),
            'aprobados' => AjusteCronograma::where('proyecto_id', $proyecto->id)->where('estado', 'aprobado')->count(),
            'aplicados' => AjusteCronograma::where('proyecto_id', $proyecto->id)->where('estado', 'aplicado')->count(),
            'rechazados' => AjusteCronograma::where('proyecto_id', $proyecto->id)->where('estado', 'rechazado')->count(),
        ];

        return view('cronograma.historial', compact('proyecto', 'ajustes', 'estadisticas'));
    }
}
