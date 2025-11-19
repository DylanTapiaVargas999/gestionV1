<?php

namespace App\Http\Controllers\GestionProyectos;

use App\Http\Controllers\Controller;
use App\Models\Proyecto;
use App\Models\Liberacion;
use App\Models\ItemLiberacion;
use App\Models\ElementoConfiguracion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class LiberacionesController extends Controller
{
    /**
     * Dashboard de liberaciones
     */
    public function index(Proyecto $proyecto)
    {
        $liberaciones = Liberacion::where('proyecto_id', $proyecto->id)
            ->with(['items.elementoConfiguracion', 'items.versionEc'])
            ->orderBy('creado_en', 'desc')
            ->get();

        // Elementos disponibles para liberar (aprobados o liberados)
        $elementosDisponibles = ElementoConfiguracion::where('proyecto_id', $proyecto->id)
            ->whereIn('estado', ['aprobado', 'liberado'])
            ->with('versiones')
            ->get();

        return view('liberaciones.index', compact('proyecto', 'liberaciones', 'elementosDisponibles'));
    }

    /**
     * Mostrar formulario de nueva liberación
     */
    public function create(Proyecto $proyecto)
    {
        $elementosDisponibles = ElementoConfiguracion::where('proyecto_id', $proyecto->id)
            ->whereIn('estado', ['aprobado', 'liberado'])
            ->with(['versiones' => function($query) {
                $query->orderBy('version', 'desc');
            }])
            ->get();

        return view('liberaciones.create', compact('proyecto', 'elementosDisponibles'));
    }

    /**
     * Guardar nueva liberación
     */
    public function store(Request $request, Proyecto $proyecto)
    {
        $request->validate([
            'etiqueta' => 'required|string|max:50',
            'nombre' => 'nullable|string|max:255',
            'descripcion' => 'nullable|string',
            'fecha_liberacion' => 'nullable|date',
            'elementos' => 'nullable|array',
            'elementos.*' => 'exists:elementos_configuracion,id',
        ]);

        // Crear liberación
        $liberacion = Liberacion::create([
            'id' => Str::uuid(),
            'proyecto_id' => $proyecto->id,
            'etiqueta' => $request->etiqueta,
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'fecha_liberacion' => $request->fecha_liberacion ?? Carbon::now(),
        ]);

        // Agregar elementos si se seleccionaron
        if ($request->has('elementos') && is_array($request->elementos)) {
            foreach ($request->elementos as $ecId) {
                // Obtener la última versión del elemento
                $elemento = ElementoConfiguracion::with(['versiones' => function($query) {
                    $query->orderBy('version', 'desc')->limit(1);
                }])->find($ecId);

                $versionId = $elemento && $elemento->versiones->isNotEmpty()
                    ? $elemento->versiones->first()->id
                    : null;

                ItemLiberacion::create([
                    'id' => Str::uuid(),
                    'liberacion_id' => $liberacion->id,
                    'ec_id' => $ecId,
                    'version_ec_id' => $versionId,
                ]);
            }
        }

        return redirect()
            ->route('proyectos.liberaciones.show', ['proyecto' => $proyecto, 'liberacion' => $liberacion])
            ->with('success', '✅ Liberación creada exitosamente');
    }

    /**
     * Ver detalles de una liberación
     */
    public function show(Proyecto $proyecto, Liberacion $liberacion)
    {
        $liberacion->load(['items.elementoConfiguracion', 'items.versionEc']);

        return view('liberaciones.show', compact('proyecto', 'liberacion'));
    }

    /**
     * Agregar elementos a una liberación existente
     */
    public function agregarElementos(Request $request, Proyecto $proyecto, Liberacion $liberacion)
    {
        $request->validate([
            'elementos' => 'required|array|min:1',
            'elementos.*' => 'exists:elementos_configuracion,id',
        ]);

        foreach ($request->elementos as $ecId) {
            // Verificar que no esté ya en la liberación
            $existe = ItemLiberacion::where('liberacion_id', $liberacion->id)
                ->where('ec_id', $ecId)
                ->exists();

            if ($existe) continue;

            // Obtener la última versión
            $elemento = ElementoConfiguracion::with(['versiones' => function($query) {
                $query->orderBy('version', 'desc')->limit(1);
            }])->find($ecId);

            $versionId = $elemento && $elemento->versiones->isNotEmpty()
                ? $elemento->versiones->first()->id
                : null;

            ItemLiberacion::create([
                'id' => Str::uuid(),
                'liberacion_id' => $liberacion->id,
                'ec_id' => $ecId,
                'version_ec_id' => $versionId,
            ]);
        }

        return redirect()->back()->with('success', '✅ Elementos agregados a la liberación');
    }

    /**
     * Eliminar un elemento de la liberación
     */
    public function quitarElemento(Proyecto $proyecto, Liberacion $liberacion, ItemLiberacion $item)
    {
        $item->delete();

        return redirect()->back()->with('success', '✅ Elemento removido de la liberación');
    }

    /**
     * Eliminar liberación completa
     */
    public function destroy(Proyecto $proyecto, Liberacion $liberacion)
    {
        $liberacion->delete();

        return redirect()
            ->route('proyectos.liberaciones.index', $proyecto)
            ->with('success', '✅ Liberación eliminada exitosamente');
    }
}
