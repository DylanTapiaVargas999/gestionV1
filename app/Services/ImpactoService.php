<?php

namespace App\Services;

use App\Models\ElementoConfiguracion;
use App\Models\RelacionEC;
use Illuminate\Support\Collection;

/**
 * Servicio para analizar el impacto de cambios en Elementos de Configuración
 * Utiliza las relaciones existentes para calcular dependencias en cadena
 */
class ImpactoService
{
    /**
     * Analizar el impacto de cambiar uno o más EC
     *
     * @param array $ecIds Array de IDs de elementos de configuración
     * @return array Estructura con EC afectados, nivel de impacto y recomendaciones
     */
    public function analizarImpacto(array $ecIds): array
    {
        $resultado = [
            'ec_originales' => [],
            'ec_afectados_directos' => [],
            'ec_afectados_indirectos' => [],
            'total_afectados' => 0,
            'nivel_impacto' => 'BAJO', // BAJO, MEDIO, ALTO, CRITICO
            'recomendaciones' => [],
            'dependencias_circulares' => [],
            'grafo_impacto' => [
                'nodes' => [],
                'edges' => [],
            ],
        ];

        if (empty($ecIds)) {
            return $resultado;
        }

        // Obtener EC originales
        $ecOriginales = ElementoConfiguracion::whereIn('id', $ecIds)
            ->with(['versionActual', 'proyecto'])
            ->get();

        $resultado['ec_originales'] = $ecOriginales->map(function ($ec) {
            return [
                'id' => $ec->id,
                'codigo' => $ec->codigo_ec,
                'titulo' => $ec->titulo,
                'tipo' => $ec->tipo,
                'estado' => $ec->estado,
                'version_actual' => $ec->versionActual?->version ?? 'Sin versión',
            ];
        })->toArray();

        // Analizar dependencias directas (nivel 1)
        $afectadosDirectos = $this->obtenerDependenciasDirectas($ecIds);

        $resultado['ec_afectados_directos'] = $afectadosDirectos->map(function ($ec) {
            return [
                'id' => $ec['ec']->id,
                'codigo' => $ec['ec']->codigo_ec,
                'titulo' => $ec['ec']->titulo,
                'tipo' => $ec['ec']->tipo,
                'estado' => $ec['ec']->estado,
                'tipo_relacion' => $ec['tipo_relacion'],
                'criticidad' => $this->calcularCriticidad($ec['ec']),
            ];
        })->toArray();

        // Analizar dependencias indirectas (niveles 2+)
        $idsDirectos = $afectadosDirectos->pluck('ec.id')->toArray();
        $afectadosIndirectos = $this->obtenerDependenciasIndirectas($idsDirectos, $ecIds);

        $resultado['ec_afectados_indirectos'] = $afectadosIndirectos->map(function ($ec) {
            return [
                'id' => $ec['ec']->id,
                'codigo' => $ec['ec']->codigo_ec,
                'titulo' => $ec['ec']->titulo,
                'tipo' => $ec['ec']->tipo,
                'estado' => $ec['ec']->estado,
                'nivel' => $ec['nivel'],
                'criticidad' => $this->calcularCriticidad($ec['ec']),
            ];
        })->toArray();

        // Calcular total de afectados
        $resultado['total_afectados'] = count($resultado['ec_afectados_directos']) +
                                       count($resultado['ec_afectados_indirectos']);

        // Determinar nivel de impacto
        $resultado['nivel_impacto'] = $this->determinarNivelImpacto(
            $ecOriginales,
            $afectadosDirectos,
            $afectadosIndirectos
        );

        // Generar recomendaciones
        $resultado['recomendaciones'] = $this->generarRecomendaciones(
            $ecOriginales,
            $afectadosDirectos,
            $afectadosIndirectos,
            $resultado['nivel_impacto']
        );

        // Detectar dependencias circulares
        $resultado['dependencias_circulares'] = $this->detectarDependenciasCirculares($ecIds);

        // Generar grafo de impacto para visualización
        $resultado['grafo_impacto'] = $this->generarGrafoImpacto(
            $ecOriginales,
            $afectadosDirectos,
            $afectadosIndirectos
        );

        return $resultado;
    }

    /**
     * Obtener EC que dependen directamente de los EC dados
     */
    private function obtenerDependenciasDirectas(array $ecIds): Collection
    {
        $relaciones = RelacionEC::whereIn('hacia_ec', $ecIds)
            ->with(['elementoDesde'])
            ->get();

        return $relaciones->map(function ($relacion) {
            return [
                'ec' => $relacion->elementoDesde,
                'tipo_relacion' => $relacion->tipo_relacion,
                'nota' => $relacion->nota,
            ];
        });
    }

    /**
     * Obtener EC afectados indirectamente (dependencias de dependencias)
     */
    private function obtenerDependenciasIndirectas(array $ecIds, array $ecOriginales, int $nivelActual = 2, int $nivelMaximo = 5): Collection
    {
        if ($nivelActual > $nivelMaximo || empty($ecIds)) {
            return collect([]);
        }

        $relaciones = RelacionEC::whereIn('hacia_ec', $ecIds)
            ->whereNotIn('desde_ec', $ecOriginales) // Evitar ciclos con originales
            ->with(['elementoDesde'])
            ->get();

        $afectados = $relaciones->map(function ($relacion) use ($nivelActual) {
            return [
                'ec' => $relacion->elementoDesde,
                'nivel' => $nivelActual,
                'tipo_relacion' => $relacion->tipo_relacion,
            ];
        });

        // Recursivamente buscar más niveles
        $nuevosIds = $afectados->pluck('ec.id')->unique()->toArray();
        if (!empty($nuevosIds)) {
            $masAfectados = $this->obtenerDependenciasIndirectas(
                $nuevosIds,
                array_merge($ecOriginales, $ecIds),
                $nivelActual + 1,
                $nivelMaximo
            );
            $afectados = $afectados->merge($masAfectados);
        }

        return $afectados;
    }

    /**
     * Calcular criticidad de un EC basado en sus características
     */
    private function calcularCriticidad(ElementoConfiguracion $ec): string
    {
        $puntos = 0;

        // Estado liberado = más crítico
        if ($ec->estado === 'LIBERADO') {
            $puntos += 3;
        } elseif ($ec->estado === 'APROBADO') {
            $puntos += 2;
        }

        // Tipo código o script BD = más crítico
        if (in_array($ec->tipo, ['CODIGO', 'SCRIPT_BD'])) {
            $puntos += 2;
        }

        // Contar cuántos EC dependen de este
        $dependientes = RelacionEC::where('hacia_ec', $ec->id)->count();
        if ($dependientes > 5) {
            $puntos += 3;
        } elseif ($dependientes > 2) {
            $puntos += 2;
        } elseif ($dependientes > 0) {
            $puntos += 1;
        }

        if ($puntos >= 6) return 'CRITICA';
        if ($puntos >= 4) return 'ALTA';
        if ($puntos >= 2) return 'MEDIA';
        return 'BAJA';
    }

    /**
     * Determinar el nivel global de impacto
     */
    private function determinarNivelImpacto(
        Collection $originales,
        Collection $afectadosDirectos,
        Collection $afectadosIndirectos
    ): string {
        $totalAfectados = $afectadosDirectos->count() + $afectadosIndirectos->count();

        // Verificar si hay EC liberados afectados
        $hayLiberados = $afectadosDirectos->concat($afectadosIndirectos)
            ->pluck('ec')
            ->contains(function ($ec) {
                return $ec->estado === 'LIBERADO';
            });

        // Verificar si hay muchos EC de código afectados
        $codigoAfectado = $afectadosDirectos->concat($afectadosIndirectos)
            ->pluck('ec')
            ->filter(function ($ec) {
                return in_array($ec->tipo, ['CODIGO', 'SCRIPT_BD']);
            })->count();

        // Lógica de determinación
        if ($hayLiberados || $totalAfectados > 10 || $codigoAfectado > 5) {
            return 'CRITICO';
        }

        if ($totalAfectados > 5 || $codigoAfectado > 2) {
            return 'ALTO';
        }

        if ($totalAfectados > 2 || $codigoAfectado > 0) {
            return 'MEDIO';
        }

        return 'BAJO';
    }

    /**
     * Generar recomendaciones basadas en el análisis
     */
    private function generarRecomendaciones(
        Collection $originales,
        Collection $afectadosDirectos,
        Collection $afectadosIndirectos,
        string $nivelImpacto
    ): array {
        $recomendaciones = [];

        // Recomendaciones según nivel de impacto
        switch ($nivelImpacto) {
            case 'CRITICO':
                $recomendaciones[] = '🔴 IMPACTO CRÍTICO: Se recomienda revisión exhaustiva por todo el CCB';
                $recomendaciones[] = '📋 Crear plan de rollback antes de implementar';
                $recomendaciones[] = '⏰ Programar cambio en horario de bajo tráfico';
                break;
            case 'ALTO':
                $recomendaciones[] = '🟠 IMPACTO ALTO: Requiere aprobación de al menos 75% del CCB';
                $recomendaciones[] = '🧪 Realizar pruebas exhaustivas antes de liberar';
                break;
            case 'MEDIO':
                $recomendaciones[] = '🟡 IMPACTO MEDIO: Revisión estándar del CCB';
                $recomendaciones[] = '✅ Validar cambios con responsables de EC afectados';
                break;
            case 'BAJO':
                $recomendaciones[] = '🟢 IMPACTO BAJO: Cambio puede proceder con aprobación simple';
                break;
        }

        // Recomendaciones específicas por EC liberados
        $ecLiberados = $afectadosDirectos->concat($afectadosIndirectos)
            ->pluck('ec')
            ->filter(fn($ec) => $ec->estado === 'LIBERADO');

        if ($ecLiberados->isNotEmpty()) {
            $recomendaciones[] = '⚠️ Se afectarán ' . $ecLiberados->count() . ' elementos ya liberados';
            $recomendaciones[] = '📝 Actualizar documentación de versiones afectadas';
        }

        // Recomendaciones por código afectado
        $codigoAfectado = $afectadosDirectos->concat($afectadosIndirectos)
            ->pluck('ec')
            ->filter(fn($ec) => in_array($ec->tipo, ['CODIGO', 'SCRIPT_BD']));

        if ($codigoAfectado->isNotEmpty()) {
            $recomendaciones[] = '💻 ' . $codigoAfectado->count() . ' archivos de código/BD afectados - asegurar pruebas';
        }

        return $recomendaciones;
    }

    /**
     * Detectar dependencias circulares
     */
    private function detectarDependenciasCirculares(array $ecIds): array
    {
        $circulares = [];

        foreach ($ecIds as $ecId) {
            $visitados = [];
            $camino = [];

            if ($this->buscarCiclo($ecId, $visitados, $camino)) {
                $circulares[] = [
                    'ec_inicio' => $ecId,
                    'camino' => $camino,
                ];
            }
        }

        return $circulares;
    }

    /**
     * Búsqueda recursiva de ciclos (DFS)
     */
    private function buscarCiclo(string $ecId, array &$visitados, array &$camino): bool
    {
        if (in_array($ecId, $camino)) {
            return true; // Ciclo detectado
        }

        if (in_array($ecId, $visitados)) {
            return false; // Ya visitado sin ciclo
        }

        $visitados[] = $ecId;
        $camino[] = $ecId;

        $relaciones = RelacionEC::where('desde_ec', $ecId)->get();

        foreach ($relaciones as $relacion) {
            if ($this->buscarCiclo($relacion->hacia_ec, $visitados, $camino)) {
                return true;
            }
        }

        array_pop($camino);
        return false;
    }

    /**
     * Generar estructura de grafo para visualización
     */
    private function generarGrafoImpacto(
        Collection $originales,
        Collection $afectadosDirectos,
        Collection $afectadosIndirectos
    ): array {
        $nodes = [];
        $edges = [];

        // Nodos originales (rojos)
        foreach ($originales as $ec) {
            $nodes[] = [
                'id' => $ec->id,
                'label' => $ec->codigo_ec,
                'title' => $ec->titulo,
                'group' => 'original',
                'color' => '#EF4444', // Rojo
            ];
        }

        // Nodos afectados directos (naranjas)
        foreach ($afectadosDirectos as $item) {
            $ec = $item['ec'];
            $nodes[] = [
                'id' => $ec->id,
                'label' => $ec->codigo_ec,
                'title' => $ec->titulo,
                'group' => 'directo',
                'color' => '#F59E0B', // Naranja
            ];
        }

        // Nodos afectados indirectos (amarillos)
        foreach ($afectadosIndirectos as $item) {
            $ec = $item['ec'];
            $nodes[] = [
                'id' => $ec->id,
                'label' => $ec->codigo_ec,
                'title' => $ec->titulo,
                'group' => 'indirecto',
                'color' => '#EAB308', // Amarillo
            ];
        }

        // Obtener todas las relaciones relevantes
        $todosIds = $originales->pluck('id')
            ->merge($afectadosDirectos->pluck('ec.id'))
            ->merge($afectadosIndirectos->pluck('ec.id'))
            ->unique()
            ->toArray();

        $relaciones = RelacionEC::whereIn('desde_ec', $todosIds)
            ->whereIn('hacia_ec', $todosIds)
            ->get();

        foreach ($relaciones as $relacion) {
            $edges[] = [
                'from' => $relacion->desde_ec,
                'to' => $relacion->hacia_ec,
                'label' => $relacion->tipo_relacion,
                'arrows' => 'to',
            ];
        }

        return [
            'nodes' => $nodes,
            'edges' => $edges,
        ];
    }
}
