<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ComiteCambio extends Model
{
    const CREATED_AT = 'creado_en';
    const UPDATED_AT = null;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $table = 'comite_cambios';

    protected $fillable = [
        'id',
        'proyecto_id',
        'nombre',
        'quorum',
    ];

    protected $casts = [
        'quorum' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = Str::uuid()->toString();
            }
            // Si no se define nombre, usar nombre del proyecto
            if (empty($model->nombre)) {
                $proyecto = Proyecto::find($model->proyecto_id);
                $model->nombre = 'CCB - ' . ($proyecto?->nombre ?? 'Proyecto');
            }
            // Si no se define quorum, calcular automáticamente (50% + 1)
            if (empty($model->quorum)) {
                $model->quorum = 1; // Mínimo 1, se actualiza al agregar miembros
            }
        });
    }

    /**
     * Relación con el proyecto
     */
    public function proyecto(): BelongsTo
    {
        return $this->belongsTo(Proyecto::class, 'proyecto_id');
    }

    /**
     * Relación con los miembros del CCB
     */
    public function miembros(): BelongsToMany
    {
        return $this->belongsToMany(Usuario::class, 'miembros_ccb', 'ccb_id', 'usuario_id')
            ->withPivot('rol_en_ccb');
    }

    /**
     * Relación con los votos emitidos
     */
    public function votos(): HasMany
    {
        return $this->hasMany(VotoCCB::class, 'ccb_id');
    }

    /**
     * Verificar si un usuario es miembro del CCB
     */
    public function esMiembro($usuarioId): bool
    {
        return $this->miembros()->where('usuario_id', $usuarioId)->exists();
    }

    /**
     * Calcular y actualizar el quorum automáticamente
     */
    public function calcularQuorum(): void
    {
        $totalMiembros = $this->miembros()->count();
        $this->quorum = (int) ceil($totalMiembros / 2); // 50% redondeado hacia arriba
        $this->save();
    }

    /**
     * Obtener solicitudes pendientes de votación
     */
    public function solicitudesPendientes()
    {
        return SolicitudCambio::where('proyecto_id', $this->proyecto_id)
            ->where('estado', 'EN_REVISION')
            ->with(['solicitante', 'items.elementoConfiguracion', 'votos'])
            ->orderBy('creado_en', 'desc')
            ->get();
    }
}
