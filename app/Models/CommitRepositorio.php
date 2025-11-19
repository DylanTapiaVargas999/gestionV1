<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Services\CommitGitHubService;

class CommitRepositorio extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $table = 'commits_repositorio';

    protected $fillable = [
        'id',
        'url_repositorio',
        'hash_commit',
        'autor',
        'mensaje',
        'fecha_commit',
        'ec_id',
    ];

    protected $casts = [
        'fecha_commit' => 'datetime',
    ];

    /**
     * Elemento de configuración al que pertenece este commit
     */
    public function elementoConfiguracion(): BelongsTo
    {
        return $this->belongsTo(ElementoConfiguracion::class, 'ec_id');
    }

    /**
     * Versiones que referencian este commit
     */
    public function versiones(): HasMany
    {
        return $this->hasMany(VersionEC::class, 'commit_id');
    }

    /**
     * Tareas que referencian este commit
     */
    public function tareas(): HasMany
    {
        return $this->hasMany(TareaProyecto::class, 'commit_id');
    }

    /**
     * Obtiene la URL completa del commit en GitHub
     */
    public function getUrlCompletaAttribute(): string
    {
        return $this->url_repositorio . '/commit/' . $this->hash_commit;
    }

    /**
     * Obtiene el hash corto del commit (primeros 7 caracteres)
     */
    public function getHashCortoAttribute(): string
    {
        return substr($this->hash_commit, 0, 7);
    }

    /**
     * Obtiene información actualizada del commit desde GitHub API
     * Usa el servicio para consultar dinámicamente
     *
     * @return array|null
     */
    public function obtenerDatosActualizados(): ?array
    {
        $commitService = new CommitGitHubService();
        $commitUrl = $this->url_completa;

        return $commitService->obtenerDatosCommit($commitUrl);
    }

    /**
     * Actualiza los campos cacheados (autor, mensaje, fecha) desde GitHub
     *
     * @return bool
     */
    public function actualizarMetadata(): bool
    {
        $datos = $this->obtenerDatosActualizados();

        if (!$datos) {
            return false;
        }

        $this->autor = $datos['autor'] ?? $this->autor;
        $this->mensaje = $datos['mensaje'] ?? $this->mensaje;
        $this->fecha_commit = $datos['fecha_commit'] ?? $this->fecha_commit;

        return $this->save();
    }
}

