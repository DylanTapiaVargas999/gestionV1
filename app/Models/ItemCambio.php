<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ItemCambio extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $table = 'items_cambio';

    protected $fillable = [
        'id',
        'solicitud_cambio_id',
        'ec_id',
        'version_actual_ec_id',
        'version_propuesta',
        'nota',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = Str::uuid()->toString();
            }
        });
    }

    /**
     * Relación con la solicitud de cambio
     */
    public function solicitudCambio(): BelongsTo
    {
        return $this->belongsTo(SolicitudCambio::class, 'solicitud_cambio_id');
    }

    /**
     * Relación con el elemento de configuración afectado
     */
    public function elementoConfiguracion(): BelongsTo
    {
        return $this->belongsTo(ElementoConfiguracion::class, 'ec_id');
    }

    /**
     * Relación con la versión actual del EC
     */
    public function versionActual(): BelongsTo
    {
        return $this->belongsTo(VersionEc::class, 'version_actual_ec_id');
    }

    /**
     * Obtener el badge de color según la versión propuesta
     */
    public function getTipoCambioAttribute(): string
    {
        if (!$this->version_propuesta) {
            return 'Sin cambio de versión';
        }

        $actual = $this->versionActual?->version ?? '0.0.0';
        $propuesta = $this->version_propuesta;

        $actualParts = explode('.', $actual);
        $propuestaParts = explode('.', $propuesta);

        // Cambio mayor (1.0.0 -> 2.0.0)
        if ($actualParts[0] < $propuestaParts[0]) {
            return 'Cambio Mayor';
        }

        // Cambio menor (1.0.0 -> 1.1.0)
        if ($actualParts[1] < $propuestaParts[1]) {
            return 'Cambio Menor';
        }

        // Parche (1.0.0 -> 1.0.1)
        return 'Parche';
    }
}
