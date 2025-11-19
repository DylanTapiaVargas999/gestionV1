<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TareaProyecto extends Model
{
    protected $table = 'tareas_proyecto';
    protected $primaryKey = 'id_tarea';
    public $timestamps = true; // Timestamps personalizados

    const CREATED_AT = 'creado_en';
    const UPDATED_AT = 'actualizado_en';

    protected $fillable = [
        'id_proyecto',
        'id_fase',
        'id_ec',
        'id_sprint',  // ← NUEVO: FK a sprints
        'nombre',
        'descripcion',
        'responsable',
        'fecha_inicio',
        'fecha_fin',
        'estado',
        'prioridad',
        // Campos específicos de Scrum
        'story_points',
        // Campos específicos de Cascada
        'horas_estimadas',
        'entregable',
        // Campos comunes
        'criterios_aceptacion',
        'notas',
        'creado_por',
        // Campos de commit
        'commit_url',
        'commit_id',
        // Campos de cronograma inteligente
        'duracion_minima',
        'es_ruta_critica',
        'holgura_dias',
        'fecha_inicio_original',
        'fecha_fin_original',
        'puede_paralelizarse',
        'dependencias',
        'progreso_real',
    ];

    protected $casts = [
        'criterios_aceptacion' => 'array',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'story_points' => 'integer',
        'horas_estimadas' => 'decimal:2',
        'prioridad' => 'integer',
        'creado_en' => 'datetime',
        'actualizado_en' => 'datetime',
        // Casts de cronograma inteligente
        'duracion_minima' => 'integer',
        'es_ruta_critica' => 'boolean',
        'holgura_dias' => 'integer',
        'fecha_inicio_original' => 'date',
        'fecha_fin_original' => 'date',
        'puede_paralelizarse' => 'boolean',
        'dependencias' => 'array',
        'progreso_real' => 'decimal:2',
    ];

    /**
     * Atributos que deben ser añadidos al modelo
     */
    protected $appends = ['progreso'];

    /**
     * Relación con Proyecto
     */
    public function proyecto(): BelongsTo
    {
        return $this->belongsTo(Proyecto::class, 'id_proyecto', 'id');
    }

    /**
     * Relación con Fase de Metodología
     */
    public function fase(): BelongsTo
    {
        return $this->belongsTo(FaseMetodologia::class, 'id_fase', 'id_fase');
    }

    /**
     * Relación con Elemento de Configuración
     */
    public function elementoConfiguracion(): BelongsTo
    {
        return $this->belongsTo(ElementoConfiguracion::class, 'id_ec', 'id');
    }

    /**
     * Relación con Sprint (solo para proyectos Scrum)
     */
    public function sprint(): BelongsTo
    {
        return $this->belongsTo(Sprint::class, 'id_sprint');
    }

    /**
     * Relación con Usuario (responsable)
     */
    public function responsableUsuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'responsable', 'id');
    }

    /**
     * Relación con Commit de GitHub
     */
    public function commit(): BelongsTo
    {
        return $this->belongsTo(CommitRepositorio::class, 'commit_id', 'id');
    }

    /**
     * Scope para filtrar por estado
     */
    public function scopeEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    /**
     * Scope para filtrar por fase
     */
    public function scopeFase($query, $idFase)
    {
        return $query->where('id_fase', $idFase);
    }

    /**
     * Scope para filtrar por sprint (por ID)
     */
    public function scopeSprint($query, $idSprint)
    {
        return $query->where('id_sprint', $idSprint);
    }

    /**
     * Verifica si la tarea está en revisión
     */
    public function estaEnRevision(): bool
    {
        return in_array($this->estado, ['EN_REVISION', 'In Review', 'Review']);
    }

    /**
     * Verifica si la tarea está completada (case-insensitive)
     */
    public function estaCompletada(): bool
    {
        $estadoLower = strtolower(trim($this->estado ?? ''));
        return in_array($estadoLower, [
            'done', 'completado', 'completada',
            'hecho', 'finished', 'finalizado', 'finalizada'
        ]);
    }

    /**
     * Calcular progreso automáticamente según el estado
     * Accessor para obtener el progreso de la tarea (0-100%)
     */
    public function getProgresoAttribute($value = null)
    {
        // Si ya existe el campo progreso_real en BD, usarlo
        if (!is_null($value)) {
            return (int) $value;
        }

        $attrs = $this->getAttributes();
        if (isset($attrs['progreso_real']) && !is_null($attrs['progreso_real'])) {
            return (int) $attrs['progreso_real'];
        }

        // Calcular automáticamente según el estado (case-insensitive)
        $estado = $this->getRawOriginal('estado') ?? $this->estado ?? 'To Do';
        $estadoLower = strtolower(trim($estado));

        // Estados completados
        if (in_array($estadoLower, ['done', 'completado', 'completada', 'hecho', 'finished', 'finalizado', 'finalizada'])) {
            return 100;
        }

        // Mapeo de progreso por estado (case-insensitive)
        $mapeoProgreso = [
            'to do' => 0,
            'pendiente' => 0,
            'product backlog' => 0,
            'sprint planning' => 10,
            'in progress' => 50,
            'en progreso' => 50,
            'in review' => 75,
            'en revisión' => 75,
            'en revision' => 75,
            'testing' => 80,
            'bloqueado' => 25, // Algo de trabajo hecho pero bloqueado
        ];

        return $mapeoProgreso[$estadoLower] ?? 0;
    }
}
