<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class VotoCCB extends Model
{
    const CREATED_AT = 'votado_en';
    const UPDATED_AT = null;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $table = 'votos_ccb';

    protected $fillable = [
        'id',
        'ccb_id',
        'solicitud_cambio_id',
        'usuario_id',
        'voto',
        'comentario',
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
     * Relación con el comité de cambios
     */
    public function comite(): BelongsTo
    {
        return $this->belongsTo(ComiteCambio::class, 'ccb_id');
    }

    /**
     * Relación con la solicitud de cambio
     */
    public function solicitudCambio(): BelongsTo
    {
        return $this->belongsTo(SolicitudCambio::class, 'solicitud_cambio_id');
    }

    /**
     * Relación con el usuario que votó
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    /**
     * Obtener el badge de color según el voto
     */
    public function getVotoBadgeAttribute(): string
    {
        return match($this->voto) {
            'APROBAR' => 'badge-success',
            'RECHAZAR' => 'badge-error',
            'ABSTENERSE' => 'badge-warning',
            default => 'badge-ghost',
        };
    }

    /**
     * Obtener el texto del voto
     */
    public function getVotoTextoAttribute(): string
    {
        return match($this->voto) {
            'APROBAR' => '✅ Aprobar',
            'RECHAZAR' => '❌ Rechazar',
            'ABSTENERSE' => '⚠️ Abstenerse',
            default => $this->voto,
        };
    }

    /**
     * Obtener el icono del voto
     */
    public function getVotoIconoAttribute(): string
    {
        return match($this->voto) {
            'APROBAR' => '✅',
            'RECHAZAR' => '❌',
            'ABSTENERSE' => '⚠️',
            default => '❓',
        };
    }
}
