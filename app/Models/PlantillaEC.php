<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlantillaEC extends Model
{
    use HasFactory;

    protected $table = 'plantillas_ec';

    protected $fillable = [
        'metodologia_id',
        'nombre',
        'tipo',
        'descripcion',
        'orden',
        'es_recomendado',
        'tarea_nombre',
        'tarea_descripcion',
        'porcentaje_inicio',
        'porcentaje_fin',
        'relaciones',
    ];

    protected $casts = [
        'es_recomendado' => 'boolean',
        'porcentaje_inicio' => 'decimal:2',
        'porcentaje_fin' => 'decimal:2',
        'relaciones' => 'array', // JSON cast
    ];

    /**
     * Relación: Una plantilla pertenece a una metodología
     */
    public function metodologia(): BelongsTo
    {
        return $this->belongsTo(Metodologia::class, 'metodologia_id', 'id_metodologia');
    }

    /**
     * Calcular fecha basada en el porcentaje y el rango del proyecto
     */
    public function calcularFecha($fechaInicio, $fechaFin, $porcentaje)
    {
        $inicio = \Carbon\Carbon::parse($fechaInicio);
        $fin = \Carbon\Carbon::parse($fechaFin);

        $duracionTotal = $inicio->diffInDays($fin);
        $diasDesdeInicio = round($duracionTotal * ($porcentaje / 100));

        return $inicio->copy()->addDays($diasDesdeInicio);
    }

    /**
     * Obtener badge de color según tipo
     */
    public function getTipoBadgeAttribute(): string
    {
        return match($this->tipo) {
            'DOCUMENTO' => 'badge-info',
            'CODIGO' => 'badge-success',
            'SCRIPT_BD' => 'badge-warning',
            'CONFIGURACION' => 'badge-secondary',
            'OTRO' => 'badge-ghost',
            default => 'badge-ghost',
        };
    }

    /**
     * Obtener icono según tipo
     */
    public function getTipoIconoAttribute(): string
    {
        return match($this->tipo) {
            'DOCUMENTO' => '📄',
            'CODIGO' => '💻',
            'SCRIPT_BD' => '🗄️',
            'CONFIGURACION' => '⚙️',
            'OTRO' => '📦',
            default => '📦',
        };
    }
}
