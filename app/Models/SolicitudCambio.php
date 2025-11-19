<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SolicitudCambio extends Model
{
    const CREATED_AT = 'creado_en';
    const UPDATED_AT = 'actualizado_en';

    protected $table = 'solicitudes_cambio';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'proyecto_id',
        'titulo',
        'descripcion',
        'solicitante_id',
        'prioridad',
        'estado',
        'resumen_impacto',
        'liberacion_objetivo_id',
        'origen_cambio',
        // Campos de auditoría
        'aprobado_por',
        'aprobado_en',
        'rechazado_por',
        'rechazado_en',
        'motivo_rechazo',
    ];

    protected $casts = [
        'aprobado_en' => 'datetime',
        'rechazado_en' => 'datetime',
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

    // Relaciones
    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'proyecto_id');
    }

    public function solicitante()
    {
        return $this->belongsTo(Usuario::class, 'solicitante_id');
    }

    public function liberacion()
    {
        return $this->belongsTo(Liberacion::class, 'liberacion_objetivo_id');
    }

    public function items()
    {
        return $this->hasMany(ItemCambio::class, 'solicitud_cambio_id');
    }

    public function aprobador()
    {
        return $this->belongsTo(Usuario::class, 'aprobado_por');
    }

    public function rechazador()
    {
        return $this->belongsTo(Usuario::class, 'rechazado_por');
    }

    public function votos()
    {
        return $this->hasMany(VotoCCB::class, 'solicitud_cambio_id');
    }
}
