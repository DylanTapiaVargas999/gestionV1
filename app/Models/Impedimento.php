<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Impedimento extends Model
{
    use HasFactory;

    /**
     * Tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'impedimentos';

    /**
     * La clave primaria de la tabla.
     *
     * @var string
     */
    protected $primaryKey = 'id_impedimento';

    /**
     * Los atributos que se pueden asignar masivamente.
     *
     * @var array<string>
     */
    protected $fillable = [
        'id_proyecto',
        'id_sprint',
        'id_usuario_reporta',
        'id_usuario_asignado',
        'titulo',
        'descripcion',
        'prioridad',
        'estado',
        'fecha_reporte',
        'fecha_resolucion',
        'solucion',
    ];

    /**
     * Los atributos que deben ser casteados.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'fecha_reporte' => 'datetime',
        'fecha_resolucion' => 'datetime',
        'creado_en' => 'datetime',
        'actualizado_en' => 'datetime',
    ];

    /**
     * Nombres de las columnas de timestamps personalizados.
     */
    const CREATED_AT = 'creado_en';
    const UPDATED_AT = 'actualizado_en';

    /**
     * Estados posibles del impedimento.
     */
    const ESTADOS = [
        'abierto' => 'Abierto',
        'en_progreso' => 'En Progreso',
        'resuelto' => 'Resuelto',
        'cerrado' => 'Cerrado',
    ];

    /**
     * Niveles de prioridad.
     */
    const PRIORIDADES = [
        'baja' => 'Baja',
        'media' => 'Media',
        'alta' => 'Alta',
        'critica' => 'Crítica',
    ];

    /**
     * Relación: Un impedimento pertenece a un proyecto.
     */
    public function proyecto(): BelongsTo
    {
        return $this->belongsTo(Proyecto::class, 'id_proyecto', 'id');
    }

    /**
     * Relación: Un impedimento puede pertenecer a un sprint.
     */
    public function sprint(): BelongsTo
    {
        return $this->belongsTo(Sprint::class, 'id_sprint', 'id_sprint');
    }

    /**
     * Relación: Usuario que reporta el impedimento.
     */
    public function usuarioReporta(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_usuario_reporta', 'id');
    }

    /**
     * Relación: Usuario asignado para resolver el impedimento.
     */
    public function usuarioAsignado(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_usuario_asignado', 'id');
    }

    /**
     * Verificar si el impedimento está abierto.
     */
    public function isAbierto()
    {
        return $this->estado === 'abierto';
    }

    /**
     * Verificar si el impedimento está resuelto.
     */
    public function isResuelto()
    {
        return in_array($this->estado, ['resuelto', 'cerrado']);
    }

    /**
     * Resolver el impedimento.
     */
    public function resolver($solucion, $usuarioId = null)
    {
        $this->update([
            'estado' => 'resuelto',
            'fecha_resolucion' => now(),
            'solucion' => $solucion,
            'id_usuario_asignado' => $usuarioId ?: $this->id_usuario_asignado,
        ]);
    }

    /**
     * Obtener impedimentos activos de un proyecto.
     */
    public static function activosDelProyecto($proyectoId)
    {
        return static::where('id_proyecto', $proyectoId)
            ->whereIn('estado', ['abierto', 'en_progreso'])
            ->orderBy('prioridad', 'desc')
            ->orderBy('fecha_reporte', 'desc')
            ->get();
    }

    /**
     * Obtener color CSS según la prioridad.
     */
    public function getColorPrioridadAttribute()
    {
        return match($this->prioridad) {
            'critica' => 'bg-red-100 text-red-800',
            'alta' => 'bg-orange-100 text-orange-800',
            'media' => 'bg-yellow-100 text-yellow-800',
            'baja' => 'bg-green-100 text-green-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}
