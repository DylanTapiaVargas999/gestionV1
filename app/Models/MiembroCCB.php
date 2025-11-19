<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MiembroCCB extends Pivot
{
    public $incrementing = false;
    public $timestamps = false;

    protected $table = 'miembros_ccb';

    protected $fillable = [
        'ccb_id',
        'usuario_id',
        'rol_en_ccb',
    ];

    /**
     * Relación con el comité de cambios
     */
    public function comite(): BelongsTo
    {
        return $this->belongsTo(ComiteCambio::class, 'ccb_id');
    }

    /**
     * Relación con el usuario
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}
