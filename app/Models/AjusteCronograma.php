<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AjusteCronograma extends Model
{
    protected $table = 'ajustes_cronograma';

    protected $fillable = [
        'proyecto_id',
        'tipo_ajuste',
        'estado',
        'desviaciones_detectadas',
        'ruta_critica',
        'recursos_sobrecargados',
        'estrategia',
        'ajustes_propuestos',
        'ajustes_aplicados',
        'dias_recuperados',
        'recursos_afectados',
        'score_solucion',
        'costo_adicional_estimado',
        'aprobado_por',
        'aprobado_en',
        'motivo_ajuste',
        'notas_rechazo',
        'creado_por',
    ];

    protected $casts = [
        'desviaciones_detectadas' => 'array',
        'ruta_critica' => 'array',
        'recursos_sobrecargados' => 'array',
        'ajustes_propuestos' => 'array',
        'ajustes_aplicados' => 'array',
        'dias_recuperados' => 'integer',
        'recursos_afectados' => 'integer',
        'score_solucion' => 'decimal:2',
        'costo_adicional_estimado' => 'decimal:2',
        'aprobado_en' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación con Proyecto
     */
    public function proyecto(): BelongsTo
    {
        return $this->belongsTo(Proyecto::class, 'proyecto_id', 'id');
    }

    /**
     * Relación con Usuario (aprobador)
     */
    public function aprobador(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'aprobado_por', 'id');
    }

    /**
     * Relación con Usuario (creador)
     */
    public function creador(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'creado_por', 'id');
    }

    /**
     * Relación con historial de ajustes de tareas
     */
    public function historialTareas(): HasMany
    {
        return $this->hasMany(HistorialAjusteTarea::class, 'ajuste_id');
    }

    /**
     * Scope para ajustes pendientes
     */
    public function scopePendientes($query)
    {
        return $query->where('estado', 'propuesto');
    }

    /**
     * Scope para ajustes aprobados
     */
    public function scopeAprobados($query)
    {
        return $query->where('estado', 'aprobado');
    }

    /**
     * Scope para ajustes aplicados
     */
    public function scopeAplicados($query)
    {
        return $query->where('estado', 'aplicado');
    }

    /**
     * Verificar si el ajuste está pendiente
     */
    public function estaPendiente(): bool
    {
        return $this->estado === 'propuesto';
    }

    /**
     * Verificar si el ajuste está aprobado
     */
    public function estaAprobado(): bool
    {
        return $this->estado === 'aprobado';
    }

    /**
     * Verificar si el ajuste está aplicado
     */
    public function estaAplicado(): bool
    {
        return $this->estado === 'aplicado';
    }
}
