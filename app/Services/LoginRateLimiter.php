<?php
declare(strict_types=1);

namespace App\Services;

/**
 * Rate Limiter pour les tentatives de connexion
 * Protection contre les attaques par force brute
 * 
 * Stockage fichier dans storage/cache/login_attempts/
 */
class LoginRateLimiter {
    private string $storagePath;
    private int $maxAttempts;
    private int $decayMinutes;

    public function __construct(int $maxAttempts = 5, int $decayMinutes = 15) {
        $this->storagePath = STORAGE_PATH . '/cache/login_attempts';
        $this->maxAttempts = $maxAttempts;
        $this->decayMinutes = $decayMinutes;

        if (!is_dir($this->storagePath)) {
            mkdir($this->storagePath, 0755, true);
        }
    }

    /**
     * Vérifie si le nombre maximum de tentatives est atteint
     */
    public function tooManyAttempts(string $key): bool {
        $this->cleanup();
        return $this->getAttempts($key) >= $this->maxAttempts;
    }

    /**
     * Enregistre une tentative échouée
     */
    public function hit(string $key): void {
        $file = $this->getFilePath($key);
        $data = $this->getData($key);
        $data['attempts'] = ($data['attempts'] ?? 0) + 1;
        $data['expires_at'] = time() + ($this->decayMinutes * 60);
        file_put_contents($file, json_encode($data), LOCK_EX);
    }

    /**
     * Efface les tentatives pour une clé donnée
     */
    public function clear(string $key): void {
        $file = $this->getFilePath($key);
        if (file_exists($file)) {
            unlink($file);
        }
    }

    /**
     * Retourne le nombre de tentatives
     */
    public function getAttempts(string $key): int {
        $data = $this->getData($key);
        if (empty($data) || ($data['expires_at'] ?? 0) < time()) {
            $this->clear($key);
            return 0;
        }
        return $data['attempts'] ?? 0;
    }

    /**
     * Retourne le nombre de minutes restantes avant déblocage
     */
    public function remainingMinutes(string $key): int {
        $data = $this->getData($key);
        if (empty($data)) return 0;
        $remaining = ($data['expires_at'] ?? 0) - time();
        return max(1, (int)ceil($remaining / 60));
    }

    /**
     * Lit les données depuis le fichier
     */
    private function getData(string $key): array {
        $file = $this->getFilePath($key);
        if (!file_exists($file)) return [];
        $content = file_get_contents($file);
        if ($content === false) return [];
        return json_decode($content, true) ?: [];
    }

    /**
     * Génère le chemin du fichier à partir de la clé
     */
    private function getFilePath(string $key): string {
        return $this->storagePath . '/' . md5($key) . '.json';
    }

    /**
     * Nettoie les fichiers expirés (appelé périodiquement)
     */
    private function cleanup(): void {
        static $lastCleanup = 0;
        if (time() - $lastCleanup < 60) return; // Max 1 cleanup par minute
        $lastCleanup = time();

        $files = glob($this->storagePath . '/*.json');
        if (!$files) return;

        foreach ($files as $file) {
            $content = file_get_contents($file);
            if ($content === false) continue;
            $data = json_decode($content, true);
            if (!$data || ($data['expires_at'] ?? 0) < time()) {
                @unlink($file);
            }
        }
    }
}
