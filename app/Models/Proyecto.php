<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Proyecto extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'proyectos';

    public static function generarCodigo()
    {
        $año = date('Y');

        // Obtener el último contador para este año
        $ultimoCodigo = static::where('codigo', 'like', "PRO-{$año}-%")
            ->orderBy('codigo', 'desc')
            ->first();

        if (!$ultimoCodigo) {
            $contador = 1;
        } else {
            // Extraer el número del último código
            preg_match('/PRO-\d{4}-(\d{3})/', $ultimoCodigo->codigo, $matches);
            $contador = isset($matches[1]) ? ((int)$matches[1] + 1) : 1;
        }

        // Formatear el contador a 3 dígitos
        return sprintf("PRO-%s-%03d", $año, $contador);
    }

    /**
     * Indica si el ID del modelo es auto-incrementable.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * El tipo de dato del ID auto-incrementable.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Nombres de las columnas de timestamps personalizados.
     */
    const CREATED_AT = 'creado_en';
    const UPDATED_AT = 'actualizado_en';

    /**
     * Los atributos que se pueden asignar masivamente.
     *
     * @var array<string>
     */
    protected $fillable = [
        'id',
        'codigo',
        'nombre',
        'descripcion',
        'id_metodologia',
        'fecha_inicio',
        'fecha_fin',
        'link_repositorio',
        'creado_por',
    ];

    /**
     * Los atributos que deben ser casteados.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'creado_en' => 'datetime',
        'actualizado_en' => 'datetime',
    ];

    /**
     * Relación: Un proyecto tiene muchos equipos.
     */
    public function equipos(): HasMany
    {
        return $this->hasMany(Equipo::class, 'proyecto_id');
    }

    /**
     * Relación: Un proyecto tiene muchos elementos de configuración.
     */
    public function elementosConfiguracion(): HasMany
    {
        return $this->hasMany(ElementoConfiguracion::class, 'proyecto_id');
    }

    /**
     * Relación: Un proyecto tiene muchos impedimentos.
     */
    public function impedimentos(): HasMany
    {
        return $this->hasMany(Impedimento::class, 'id_proyecto', 'id');
    }

    /**
     * Relación: Un proyecto tiene muchas tareas.
     */
    public function tareas(): HasMany
    {
        return $this->hasMany(TareaProyecto::class, 'id_proyecto', 'id');
    }

    /**
     * Relación: Un proyecto tiene muchas liberaciones.
     */
    public function liberaciones(): HasMany
    {
        return $this->hasMany(Liberacion::class, 'proyecto_id');
    }

    /**
     * Relación: Un proyecto tiene un comité de cambios.
     */
    public function comiteCambio()
    {
        return $this->hasOne(ComiteCambio::class, 'proyecto_id', 'id');
    }

    /**
     * Relación: Un proyecto tiene muchas solicitudes de cambio.
     */
    public function solicitudesCambio(): HasMany
    {
        return $this->hasMany(SolicitudCambio::class, 'proyecto_id', 'id');
    }

    /**
     * Relación: Un proyecto tiene muchos sprints (solo para Scrum).
     */
    public function sprints(): HasMany
    {
        return $this->hasMany(Sprint::class, 'id_proyecto', 'id');
    }

    /**
     * Relación: Obtener el sprint activo actual del proyecto.
     */
    public function sprintActivo()
    {
        return $this->hasOne(Sprint::class, 'id_proyecto', 'id')
            ->where('estado', 'activo')
            ->latest('fecha_inicio');
    }

    /**
     * Relación: Un proyecto pertenece a un usuario creador.
     */
    public function creador()
    {
        return $this->belongsTo(Usuario::class, 'creado_por');
    }

    /**
     * Relación: Un proyecto pertenece a una metodología.
     */
    public function metodologia()
    {
        return $this->belongsTo(Metodologia::class, 'id_metodologia', 'id_metodologia');
    }

    /**
     * Relación: Un proyecto tiene muchos usuarios a través de usuarios_roles.
     */
    public function usuarios(): BelongsToMany
    {
        return $this->belongsToMany(Usuario::class, 'usuarios_roles', 'proyecto_id', 'usuario_id')
            ->withPivot('rol_id');
    }

    /**
     * Scope para obtener proyectos activos (puedes personalizar la lógica).
     */
    public function scopeActivos($query)
    {
        return $query; // Por ahora retorna todos, puedes agregar filtros
    }

    /**
     * Verificar si un usuario es líder de algún equipo del proyecto.
     */
    public function esLider($usuarioId)
    {
        return $this->equipos()->where('lider_id', $usuarioId)->exists();
    }

    /**
     * Obtener el equipo donde el usuario es líder.
     */
    public function equipoDondeEsLider($usuarioId)
    {
        return $this->equipos()->where('lider_id', $usuarioId)->first();
    }
}
