<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * Servicio para consultar información de commits desde GitHub API
 *
 * Este servicio obtiene información de commits de GitHub de forma dinámica
 * sin almacenar toda la información en la base de datos.
 */
class CommitGitHubService
{
    /**
     * Extrae información de una URL de commit de GitHub
     *
     * @param string $commitUrl URL del commit (ej: github.com/user/repo/commit/abc123)
     * @return array|null Array con información del commit o null si falla
     */
    public function extraerInfoCommit(string $commitUrl): ?array
    {
        // Formato esperado: github.com/OWNER/REPO/commit/HASH
        $pattern = '#github\.com/([^/]+)/([^/]+)/commit/([a-f0-9]+)#i';

        if (!preg_match($pattern, $commitUrl, $matches)) {
            return null; // URL no es formato válido
        }

        return [
            'owner' => $matches[1],
            'repo' => $matches[2],
            'hash' => $matches[3],
            'url_repositorio' => "https://github.com/{$matches[1]}/{$matches[2]}",
            'commit_url' => $commitUrl,
        ];
    }

    /**
     * Valida si una URL de commit es válida
     *
     * @param string $commitUrl
     * @return bool
     */
    public function esUrlCommitValida(string $commitUrl): bool
    {
        return $this->extraerInfoCommit($commitUrl) !== null;
    }

    /**
     * Obtiene información detallada de un commit desde GitHub API
     * Usa cache para evitar consultas repetidas
     *
     * @param string $commitUrl URL del commit
     * @param int $cacheDuration Duración del cache en minutos (default: 60)
     * @return array|null Array con información del commit o null si falla
     */
    public function obtenerDatosCommit(string $commitUrl, int $cacheDuration = 60): ?array
    {
        $info = $this->extraerInfoCommit($commitUrl);

        if (!$info) {
            return null;
        }

        $cacheKey = "commit_github_{$info['hash']}";

        // Intentar obtener desde cache
        return Cache::remember($cacheKey, $cacheDuration * 60, function () use ($info) {
            return $this->consultarGitHubAPI($info['owner'], $info['repo'], $info['hash']);
        });
    }

    /**
     * Consulta la API de GitHub para obtener información del commit
     *
     * @param string $owner Propietario del repositorio
     * @param string $repo Nombre del repositorio
     * @param string $hash SHA del commit
     * @return array|null
     */
    private function consultarGitHubAPI(string $owner, string $repo, string $hash): ?array
    {
        try {
            $apiUrl = "https://api.github.com/repos/{$owner}/{$repo}/commits/{$hash}";

            $response = Http::timeout(10)
                ->withHeaders([
                    'Accept' => 'application/vnd.github.v3+json',
                    'User-Agent' => 'SGCS-Laravel-App',
                    // Si tienes un token de GitHub, agrégalo aquí para evitar rate limits:
                    // 'Authorization' => 'token ' . config('services.github.token'),
                ])
                ->get($apiUrl);

            if (!$response->successful()) {
                Log::warning("Error al consultar GitHub API: {$apiUrl}", [
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);
                return null;
            }

            $data = $response->json();

            // Extraer información relevante
            return [
                'sha' => $data['sha'] ?? $hash,
                'autor' => $data['commit']['author']['name'] ?? 'Desconocido',
                'autor_email' => $data['commit']['author']['email'] ?? null,
                'mensaje' => $data['commit']['message'] ?? '',
                'fecha_commit' => $data['commit']['author']['date'] ?? null,
                'url' => $data['html_url'] ?? null,
                'stats' => [
                    'total' => $data['stats']['total'] ?? 0,
                    'additions' => $data['stats']['additions'] ?? 0,
                    'deletions' => $data['stats']['deletions'] ?? 0,
                ],
                'archivos_modificados' => count($data['files'] ?? []),
                'archivos' => $this->extraerArchivosModificados($data['files'] ?? []),
            ];

        } catch (\Exception $e) {
            Log::error('Error al consultar GitHub API para commit', [
                'owner' => $owner,
                'repo' => $repo,
                'hash' => $hash,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Extrae información resumida de los archivos modificados
     *
     * @param array $files
     * @return array
     */
    private function extraerArchivosModificados(array $files): array
    {
        return array_map(function ($file) {
            return [
                'nombre' => $file['filename'] ?? '',
                'status' => $file['status'] ?? '',
                'additions' => $file['additions'] ?? 0,
                'deletions' => $file['deletions'] ?? 0,
                'changes' => $file['changes'] ?? 0,
            ];
        }, $files);
    }

    /**
     * Obtiene solo el mensaje del commit (cache más largo)
     *
     * @param string $commitUrl
     * @return string|null
     */
    public function obtenerMensajeCommit(string $commitUrl): ?string
    {
        $datos = $this->obtenerDatosCommit($commitUrl, 120); // Cache de 2 horas
        return $datos['mensaje'] ?? null;
    }

    /**
     * Obtiene solo el autor del commit
     *
     * @param string $commitUrl
     * @return string|null
     */
    public function obtenerAutorCommit(string $commitUrl): ?string
    {
        $datos = $this->obtenerDatosCommit($commitUrl, 120);
        return $datos['autor'] ?? null;
    }

    /**
     * Obtiene la fecha del commit
     *
     * @param string $commitUrl
     * @return string|null
     */
    public function obtenerFechaCommit(string $commitUrl): ?string
    {
        $datos = $this->obtenerDatosCommit($commitUrl, 120);
        return $datos['fecha_commit'] ?? null;
    }

    /**
     * Limpia el cache de un commit específico
     *
     * @param string $commitUrl
     * @return bool
     */
    public function limpiarCacheCommit(string $commitUrl): bool
    {
        $info = $this->extraerInfoCommit($commitUrl);

        if (!$info) {
            return false;
        }

        $cacheKey = "commit_github_{$info['hash']}";
        return Cache::forget($cacheKey);
    }
}
