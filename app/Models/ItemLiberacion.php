<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemLiberacion extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'items_liberacion';

    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'liberacion_id',
        'ec_id',
        'version_ec_id',
    ];

    /**
     * Relación: Un item pertenece a una liberación
     */
    public function liberacion(): BelongsTo
    {
        return $this->belongsTo(Liberacion::class, 'liberacion_id');
    }

    /**
     * Relación: Un item pertenece a un elemento de configuración
     */
    public function elementoConfiguracion(): BelongsTo
    {
        return $this->belongsTo(ElementoConfiguracion::class, 'ec_id');
    }

    /**
     * Relación: Un item puede tener una versión específica
     */
    public function versionEc(): BelongsTo
    {
        return $this->belongsTo(VersionEc::class, 'version_ec_id');
    }
}
