<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyScrum extends Model
{
    use HasFactory;

    /**
     * Tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'daily_scrums';

    /**
     * La clave primaria de la tabla.
     *
     * @var string
     */
    protected $primaryKey = 'id_daily';

    /**
     * Los atributos que se pueden asignar masivamente.
     *
     * @var array<string>
     */
    protected $fillable = [
        'id_sprint',
        'id_usuario',
        'fecha',
        'que_hice_ayer',
        'que_hare_hoy',
        'impedimentos',
        'notas_adicionales',
    ];

    /**
     * Los atributos que deben ser casteados.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'fecha' => 'date',
        'que_hice_ayer' => 'array',
        'que_hare_hoy' => 'array',
        'impedimentos' => 'array',
        'creado_en' => 'datetime',
        'actualizado_en' => 'datetime',
    ];

    /**
     * Nombres de las columnas de timestamps personalizados.
     */
    const CREATED_AT = 'creado_en';
    const UPDATED_AT = 'actualizado_en';

    /**
     * Relación: Un daily scrum pertenece a un sprint.
     */
    public function sprint(): BelongsTo
    {
        return $this->belongsTo(Sprint::class, 'id_sprint', 'id_sprint');
    }

    /**
     * Relación: Un daily scrum pertenece a un usuario.
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id');
    }

    /**
     * Verificar si tiene impedimentos reportados.
     */
    public function tieneImpedimentos()
    {
        return !empty($this->impedimentos) && count($this->impedimentos) > 0;
    }

    /**
     * Obtener el daily scrum de un usuario para una fecha específica.
     */
    public static function porUsuarioYFecha($usuarioId, $fecha)
    {
        return static::where('id_usuario', $usuarioId)
            ->whereDate('fecha', $fecha)
            ->first();
    }

    /**
     * Obtener todos los daily scrums de un sprint para una fecha.
     */
    public static function porSprintYFecha($sprintId, $fecha)
    {
        return static::where('id_sprint', $sprintId)
            ->whereDate('fecha', $fecha)
            ->with('usuario')
            ->get();
    }
}
