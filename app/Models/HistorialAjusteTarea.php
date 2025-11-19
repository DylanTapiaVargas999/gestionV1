<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistorialAjusteTarea extends Model
{
    protected $table = 'historial_ajustes_tareas';

    protected $fillable = [
        'ajuste_id',
        'tarea_id',
        'fecha_inicio_anterior',
        'fecha_fin_anterior',
        'duracion_anterior',
        'responsable_anterior',
        'horas_estimadas_anterior',
        'fecha_inicio_nueva',
        'fecha_fin_nueva',
        'duracion_nueva',
        'responsable_nuevo',
        'horas_estimadas_nueva',
        'tipo_cambio',
        'impacto_estimado',
        'aplicado',
    ];

    protected $casts = [
        'fecha_inicio_anterior' => 'date',
        'fecha_fin_anterior' => 'date',
        'duracion_anterior' => 'integer',
        'horas_estimadas_anterior' => 'decimal:2',
        'fecha_inicio_nueva' => 'date',
        'fecha_fin_nueva' => 'date',
        'duracion_nueva' => 'integer',
        'horas_estimadas_nueva' => 'decimal:2',
        'aplicado' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación con AjusteCronograma
     */
    public function ajuste(): BelongsTo
    {
        return $this->belongsTo(AjusteCronograma::class, 'ajuste_id');
    }

    /**
     * Relación con TareaProyecto
     */
    public function tarea(): BelongsTo
    {
        return $this->belongsTo(TareaProyecto::class, 'tarea_id', 'id_tarea');
    }

    /**
     * Relación con Usuario (responsable anterior)
     */
    public function responsableAnteriorUsuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'responsable_anterior', 'id');
    }

    /**
     * Relación con Usuario (responsable nuevo)
     */
    public function responsableNuevoUsuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'responsable_nuevo', 'id');
    }
}
