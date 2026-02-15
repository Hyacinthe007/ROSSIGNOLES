<?php
declare(strict_types=1);

namespace App\Core;

/**
 * Système d'événements métier pour ROSSIGNOLES V2
 * 
 * Permet de découpler les actions métier de leurs conséquences.
 * Ex: Quand un paiement est reçu → mettre à jour l'échéancier + notifier le parent
 * 
 * Usage:
 *   // Enregistrer un listener (dans index.php ou un fichier de config)
 *   EventDispatcher::listen('paiement.recu', [EcheancierListener::class, 'onPaiementRecu']);
 *   EventDispatcher::listen('paiement.recu', [NotificationListener::class, 'onPaiementRecu']);
 * 
 *   // Dispatcher un événement (dans le contrôleur ou service)
 *   EventDispatcher::dispatch('paiement.recu', [
 *       'paiement_id' => $paiementId,
 *       'eleve_id' => $eleveId,
 *       'montant' => $montant
 *   ]);
 */
class EventDispatcher
{
    /** @var array<string, array<callable>> Listeners enregistrés par événement */
    private static array $listeners = [];

    /** @var array<array> Historique des événements dispatchés (debug) */
    private static array $history = [];

    /** @var bool Mode debug (log tous les événements) */
    private static bool $debug = false;

    /**
     * Enregistre un listener pour un événement
     *
     * @param string   $event    Nom de l'événement (ex: 'paiement.recu')
     * @param callable $listener Fonction ou [Classe, méthode] à appeler
     * @param int      $priority Priorité (plus élevé = exécuté en premier)
     */
    public static function listen(string $event, callable $listener, int $priority = 0): void
    {
        self::$listeners[$event][] = [
            'callback' => $listener,
            'priority' => $priority,
        ];

        // Trier par priorité décroissante
        usort(self::$listeners[$event], function ($a, $b) {
            return $b['priority'] <=> $a['priority'];
        });
    }

    /**
     * Dispatche un événement vers tous ses listeners
     *
     * @param string $event Nom de l'événement
     * @param array  $data  Données de l'événement
     * @return array Résultats de chaque listener
     */
    public static function dispatch(string $event, array $data = []): array
    {
        $results = [];
        $startTime = microtime(true);

        // Ajouter des métadonnées à l'événement
        $data['_event'] = $event;
        $data['_dispatched_at'] = date('Y-m-d H:i:s');
        $data['_user_id'] = $_SESSION['user_id'] ?? null;

        if (!isset(self::$listeners[$event])) {
            if (self::$debug) {
                error_log("[EventDispatcher] Événement '{$event}' sans listener");
            }
            return $results;
        }

        foreach (self::$listeners[$event] as $listenerInfo) {
            $callback = $listenerInfo['callback'];

            try {
                // Si c'est un tableau [Classe::class, 'méthode'], instancier la classe
                if (is_array($callback) && is_string($callback[0]) && class_exists($callback[0])) {
                    $instance = new $callback[0]();
                    $result = call_user_func([$instance, $callback[1]], $data);
                } else {
                    $result = call_user_func($callback, $data);
                }

                $results[] = [
                    'listener' => self::getListenerName($callback),
                    'success'  => true,
                    'result'   => $result,
                ];
            } catch (\Throwable $e) {
                $listenerName = self::getListenerName($callback);
                error_log("[EventDispatcher] Erreur dans listener '{$listenerName}' pour '{$event}': " . $e->getMessage());

                $results[] = [
                    'listener' => $listenerName,
                    'success'  => false,
                    'error'    => $e->getMessage(),
                ];

                // Ne pas arrêter les autres listeners si l'un échoue
            }
        }

        // Enregistrer dans l'historique
        $duration = round((microtime(true) - $startTime) * 1000, 2);
        self::$history[] = [
            'event'      => $event,
            'listeners'  => count(self::$listeners[$event]),
            'duration_ms' => $duration,
            'timestamp'  => date('Y-m-d H:i:s'),
        ];

        if (self::$debug) {
            error_log("[EventDispatcher] '{$event}' dispatché → " . count($results) . " listeners en {$duration}ms");
        }

        return $results;
    }

    /**
     * Vérifie si un événement a des listeners
     */
    public static function hasListeners(string $event): bool
    {
        return !empty(self::$listeners[$event]);
    }

    /**
     * Retourne la liste des événements enregistrés
     */
    public static function getRegisteredEvents(): array
    {
        $events = [];
        foreach (self::$listeners as $event => $listeners) {
            $events[$event] = count($listeners);
        }
        return $events;
    }

    /**
     * Retourne l'historique des événements dispatchés
     */
    public static function getHistory(): array
    {
        return self::$history;
    }

    /**
     * Active/désactive le mode debug
     */
    public static function setDebug(bool $debug): void
    {
        self::$debug = $debug;
    }

    /**
     * Supprime tous les listeners (utile pour les tests)
     */
    public static function clear(): void
    {
        self::$listeners = [];
        self::$history = [];
    }

    /**
     * Supprime les listeners d'un événement spécifique
     */
    public static function removeListeners(string $event): void
    {
        unset(self::$listeners[$event]);
    }

    /**
     * Obtient un nom lisible pour un listener
     */
    private static function getListenerName(callable $callback): string
    {
        if (is_array($callback)) {
            $class = is_object($callback[0]) ? get_class($callback[0]) : $callback[0];
            return $class . '::' . $callback[1];
        }

        if (is_string($callback)) {
            return $callback;
        }

        return 'Closure';
    }
}
