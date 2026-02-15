<?php
declare(strict_types=1);

namespace App\Core;

/**
 * Cache fichier simple pour ROSSIGNOLES V2
 * 
 * Stocke les données en cache dans le dossier storage/cache
 * Idéal pour les données rarement modifiées (cycles, niveaux, matières, configuration)
 * 
 * Usage:
 *   $cycles = Cache::remember('cycles_actifs', 3600, function() {
 *       return (new Cycle())->all(['actif' => 1]);
 *   });
 *   
 *   Cache::forget('cycles_actifs');       // Invalider une clé
 *   Cache::flush();                       // Tout vider
 *   Cache::put('key', $data, 600);        // Stocker pour 10 min
 *   $data = Cache::get('key');            // Récupérer
 */
class Cache
{
    /** @var string Chemin du dossier de cache */
    private static string $cachePath = '';

    /** @var bool Cache activé */
    private static bool $enabled = true;

    /**
     * Initialise le chemin de cache
     */
    private static function init(): void
    {
        if (empty(self::$cachePath)) {
            self::$cachePath = (defined('STORAGE_PATH') ? STORAGE_PATH : __DIR__ . '/../../storage') . '/cache';
            
            if (!is_dir(self::$cachePath)) {
                mkdir(self::$cachePath, 0755, true);
            }
        }
    }

    /**
     * Récupère une valeur du cache, ou l'exécute et la met en cache
     *
     * @param string   $key      Clé de cache
     * @param int      $ttl      Durée de vie en secondes (0 = pas d'expiration)
     * @param callable $callback Fonction qui retourne les données à mettre en cache
     * @return mixed
     */
    public static function remember(string $key, int $ttl, callable $callback): mixed
    {
        $cached = self::get($key);
        
        if ($cached !== null) {
            return $cached;
        }

        $value = $callback();
        self::put($key, $value, $ttl);

        return $value;
    }

    /**
     * Récupère une valeur du cache
     *
     * @param string $key     Clé de cache
     * @param mixed  $default Valeur par défaut si non trouvé
     * @return mixed
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        if (!self::$enabled) {
            return $default;
        }

        self::init();

        $file = self::getFilePath($key);

        if (!file_exists($file)) {
            return $default;
        }

        $content = file_get_contents($file);
        if ($content === false) {
            return $default;
        }

        $data = @unserialize($content);
        if ($data === false) {
            // Fichier corrompu, le supprimer
            @unlink($file);
            return $default;
        }

        // Vérifier l'expiration
        if ($data['expires_at'] > 0 && $data['expires_at'] < time()) {
            @unlink($file);
            return $default;
        }

        return $data['value'];
    }

    /**
     * Stocke une valeur dans le cache
     *
     * @param string $key   Clé de cache
     * @param mixed  $value Valeur à stocker
     * @param int    $ttl   Durée de vie en secondes (0 = pas d'expiration)
     * @return bool
     */
    public static function put(string $key, mixed $value, int $ttl = 3600): bool
    {
        if (!self::$enabled) {
            return false;
        }

        self::init();

        $data = [
            'key'        => $key,
            'value'      => $value,
            'expires_at' => $ttl > 0 ? time() + $ttl : 0,
            'created_at' => time(),
        ];

        $file = self::getFilePath($key);
        
        return file_put_contents($file, serialize($data), LOCK_EX) !== false;
    }

    /**
     * Vérifie si une clé existe dans le cache (et n'est pas expirée)
     */
    public static function has(string $key): bool
    {
        return self::get($key) !== null;
    }

    /**
     * Supprime une entrée du cache
     */
    public static function forget(string $key): bool
    {
        self::init();

        $file = self::getFilePath($key);

        if (file_exists($file)) {
            return @unlink($file);
        }

        return true;
    }

    /**
     * Supprime toutes les entrées du cache dont la clé commence par le préfixe donné
     * Ex: Cache::forgetPrefix('classes_') supprime classes_list, classes_details_5, etc.
     */
    public static function forgetPrefix(string $prefix): int
    {
        self::init();

        $count = 0;
        $pattern = self::$cachePath . '/' . md5('') . '*'; // On scanne tout
        
        // Scanner tous les fichiers de cache
        $files = glob(self::$cachePath . '/*.cache');
        if ($files === false) {
            return 0;
        }

        foreach ($files as $file) {
            $content = @file_get_contents($file);
            if ($content === false) continue;
            
            $data = @unserialize($content);
            if ($data === false) continue;
            
            if (isset($data['key']) && str_starts_with($data['key'], $prefix)) {
                @unlink($file);
                $count++;
            }
        }

        return $count;
    }

    /**
     * Vide entièrement le cache
     */
    public static function flush(): int
    {
        self::init();

        $count = 0;
        $files = glob(self::$cachePath . '/*.cache');
        
        if ($files === false) {
            return 0;
        }

        foreach ($files as $file) {
            if (@unlink($file)) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Nettoie les entrées expirées (à appeler via cron)
     */
    public static function cleanup(): int
    {
        self::init();

        $count = 0;
        $files = glob(self::$cachePath . '/*.cache');
        
        if ($files === false) {
            return 0;
        }

        foreach ($files as $file) {
            $content = @file_get_contents($file);
            if ($content === false) continue;
            
            $data = @unserialize($content);
            if ($data === false) {
                @unlink($file);
                $count++;
                continue;
            }
            
            if ($data['expires_at'] > 0 && $data['expires_at'] < time()) {
                @unlink($file);
                $count++;
            }
        }

        return $count;
    }

    /**
     * Active ou désactive le cache (utile en développement)
     */
    public static function setEnabled(bool $enabled): void
    {
        self::$enabled = $enabled;
    }

    /**
     * Génère le chemin de fichier pour une clé
     */
    private static function getFilePath(string $key): string
    {
        return self::$cachePath . '/' . md5($key) . '.cache';
    }
}
