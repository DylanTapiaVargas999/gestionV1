<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Liberacion extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'liberaciones';

    public $incrementing = false;
    protected $keyType = 'string';

    const CREATED_AT = 'creado_en';
    const UPDATED_AT = null;

    protected $fillable = [
        'id',
        'proyecto_id',
        'etiqueta',
        'nombre',
        'descripcion',
        'fecha_liberacion',
    ];

    protected $casts = [
        'fecha_liberacion' => 'date',
        'creado_en' => 'datetime',
    ];

    /**
     * Relación: Una liberación pertenece a un proyecto
     */
    public function proyecto(): BelongsTo
    {
        return $this->belongsTo(Proyecto::class, 'proyecto_id');
    }

    /**
     * Relación: Una liberación tiene muchos items
     */
    public function items(): HasMany
    {
        return $this->hasMany(ItemLiberacion::class, 'liberacion_id');
    }

    /**
     * Scope: Liberaciones recientes
     */
    public function scopeRecientes($query, $limit = 5)
    {
        return $query->orderBy('creado_en', 'desc')->limit($limit);
    }

    /**
     * Scope: Liberaciones por proyecto
     */
    public function scopePorProyecto($query, $proyectoId)
    {
        return $query->where('proyecto_id', $proyectoId);
    }

    /**
     * Obtener cantidad de elementos en la liberación
     */
    public function getCantidadElementosAttribute(): int
    {
        return $this->items()->count();
    }

    /**
     * Verificar si la liberación está vacía
     */
    public function estaVacia(): bool
    {
        return $this->items()->count() === 0;
    }
}
