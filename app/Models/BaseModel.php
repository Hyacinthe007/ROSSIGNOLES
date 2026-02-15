<?php
declare(strict_types=1);

namespace App\Models;

use PDO;
use PDOException;
use Exception;

/**
 * Modèle de base avec méthodes CRUD génériques
 */

class BaseModel {
    protected $table;
    protected $primaryKey = 'id';
    protected $db;
    protected $fillable = [];
    protected $hidden = [];
    
    /** @var bool Activer l'audit automatique pour ce modèle */
    protected $auditable = false;
    
    public function __construct() {
        $this->db = $this->getConnection();
    }
    
    /**
     * Obtient la connexion PDO
     */
    protected function getConnection() {
        return self::getDBConnection();
    }
    
    /**
     * Obtient la connexion PDO (méthode statique publique pour les services)
     */
    public static function getDBConnection() {
        static $pdo = null;
        
        if ($pdo === null) {
            $config = require CONFIG_PATH . '/database.php';
            
            try {
                $dsn = "mysql:host={$config['host']};dbname={$config['database']};charset={$config['charset']}";
                
                // Add persistent connection option to prevent "MySQL server has gone away"
                $options = $config['options'];
                $options[PDO::ATTR_PERSISTENT] = true;
                $options[PDO::ATTR_TIMEOUT] = 5;
                
                $pdo = new PDO($dsn, $config['username'], $config['password'], $options);
                
                // Forcer l'encodage UTF-8 pour la connexion
                $pdo->exec("SET NAMES 'utf8mb4' COLLATE 'utf8mb4_unicode_ci'");
                $pdo->exec("SET CHARACTER SET utf8mb4");
            } catch (PDOException $e) {
                error_log("Database connection error: " . $e->getMessage());
                die("Erreur de connexion à la base de données. Veuillez vérifier que MySQL est démarré et que la base de données 'ecole' existe.");
            }
        }
        
        return $pdo;
    }
    
    /**
     * Trouve un enregistrement par ID
     */
    public function find($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            if ($e->getCode() == '42S02') {
                error_log("Table {$this->table} n'existe pas dans la base de données");
                return null;
            }
            throw $e;
        }
    }
    
    /**
     * Alias pour find() - Trouve un enregistrement par ID
     */
    public function findById($id) {
        return $this->find($id);
    }
    
    /**
     * Alias pour all() - Récupère tous les enregistrements
     */
    public function getAll($conditions = [], $orderBy = null, $limit = null) {
        return $this->all($conditions, $orderBy, $limit);
    }
    
    /**
     * Récupère tous les enregistrements
     */
    public function all($conditions = [], $orderBy = null, $limit = null) {
        try {
            $sql = "SELECT * FROM {$this->table}";
            $params = [];
            
            // Filtrer les conditions pour ne garder que les colonnes existantes
            $filteredConditions = [];
            if (!empty($conditions)) {
                foreach ($conditions as $key => $value) {
                    if ($this->columnExists($key)) {
                        $filteredConditions[$key] = $value;
                    } else {
                        error_log("Colonne {$key} ignorée dans {$this->table} (n'existe pas)");
                    }
                }
            }
            
            if (!empty($filteredConditions)) {
                $where = [];
                foreach ($filteredConditions as $key => $value) {
                    $where[] = "{$key} = ?";
                    $params[] = $value;
                }
                $sql .= " WHERE " . implode(" AND ", $where);
            }
            
            // Vérifier et nettoyer ORDER BY
            $validOrderBy = null;
            if ($orderBy) {
                $parts = explode(',', $orderBy);
                $validParts = [];
                foreach ($parts as $part) {
                    $part = trim($part);
                    if (empty($part)) continue;
                    
                    $orderParts = preg_split('/\s+/', $part);
                    $orderColumn = $orderParts[0];
                    $orderDirection = strtoupper(isset($orderParts[1]) ? $orderParts[1] : 'ASC');
                    if (!in_array($orderDirection, ['ASC', 'DESC'])) {
                        $orderDirection = 'ASC';
                    }
                    
                    if ($this->columnExists($orderColumn)) {
                        $validParts[] = "{$orderColumn} {$orderDirection}";
                    } else {
                        error_log("Colonne ORDER BY '{$orderColumn}' ignorée dans {$this->table} (n'existe pas)");
                    }
                }
                if (!empty($validParts)) {
                    $validOrderBy = implode(', ', $validParts);
                }
            }
            
            if ($validOrderBy) {
                $sql .= " ORDER BY {$validOrderBy}";
            }
            
            if ($limit) {
                $sql .= " LIMIT " . (int)$limit;
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            // Si la table n'existe pas, retourner un tableau vide
            if ($e->getCode() == '42S02') {
                error_log("Table {$this->table} n'existe pas dans la base de données");
                return [];
            }
            // Si une colonne n'existe toujours pas malgré les vérifications, retourner un tableau vide
            if ($e->getCode() == '42S22') {
                error_log("Colonne non trouvée dans {$this->table}: " . $e->getMessage());
                // Essayer sans ORDER BY et sans conditions
                try {
                    $sql = "SELECT * FROM {$this->table}";
                    if ($limit) {
                        $sql .= " LIMIT " . (int)$limit;
                    }
                    $stmt = $this->db->prepare($sql);
                    $stmt->execute();
                    return $stmt->fetchAll();
                } catch (PDOException $e2) {
                    error_log("Erreur lors de la récupération des données: " . $e2->getMessage());
                    return [];
                }
            }
            throw $e;
        }
    }
    
    /**
     * Vérifie si une colonne existe dans la table
     */
    public function columnExists($columnName) {
        try {
            $stmt = $this->db->prepare("SELECT {$columnName} FROM {$this->table} LIMIT 1");
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Crée un nouvel enregistrement avec audit automatique
     */
    public function create($data) {
        try {
            $data = $this->filterFillable($data);
            
            $fields = array_keys($data);
            $placeholders = array_fill(0, count($fields), '?');
            
            $sql = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ") 
                    VALUES (" . implode(', ', $placeholders) . ")";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute(array_values($data));
            
            $id = (int)$this->db->lastInsertId();

            // Audit automatique
            if ($this->auditable && class_exists('\App\Middleware\AuditMiddleware')) {
                \App\Middleware\AuditMiddleware::logCreate($this->table, $id, $data);
            }
            
            return $id;
        } catch (PDOException $e) {
            if ($e->getCode() == '42S02') {
                error_log("Table {$this->table} n'existe pas dans la base de données");
                throw new Exception("La table {$this->table} n'existe pas. Veuillez importer le schéma SQL.");
            }
            throw $e;
        }
    }
    
    /**
     * Met à jour un enregistrement avec audit automatique
     */
    public function update($id, $data) {
        $data = $this->filterFillable($data);
        
        // Récupérer les anciennes valeurs si l'audit est activé
        $oldData = null;
        if ($this->auditable && class_exists('\App\Middleware\AuditMiddleware')) {
            $oldData = $this->find($id);
        }

        $fields = [];
        $values = [];
        foreach ($data as $key => $value) {
            $fields[] = "{$key} = ?";
            $values[] = $value;
        }
        $values[] = $id;
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . 
               " WHERE {$this->primaryKey} = ?";
        
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute($values);

        // Audit automatique
        if ($result && $oldData && class_exists('\App\Middleware\AuditMiddleware')) {
            \App\Middleware\AuditMiddleware::logUpdate($this->table, (int)$id, (array)$oldData, $data);
        }

        return $result;
    }
    
    /**
     * Supprime un enregistrement avec audit automatique
     */
    public function delete($id) {
        // Récupérer les anciennes valeurs si l'audit est activé
        $oldData = null;
        if ($this->auditable && class_exists('\App\Middleware\AuditMiddleware')) {
            $oldData = $this->find($id);
        }

        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?");
        $result = $stmt->execute([$id]);

        // Audit automatique
        if ($result && $oldData && class_exists('\App\Middleware\AuditMiddleware')) {
            \App\Middleware\AuditMiddleware::logDelete($this->table, (int)$id, (array)$oldData);
        }

        return $result;
    }
    
    /**
     * Filtre les données selon fillable
     */
    protected function filterFillable($data) {
        if (empty($this->fillable)) {
            return $data;
        }
        
        return array_intersect_key($data, array_flip($this->fillable));
    }
    
    /**
     * Exécute une requête personnalisée
     */
    public function query($sql, $params = []) {
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            if ($e->getCode() == '42S02') {
                error_log("Table n'existe pas dans la base de données");
                return [];
            }
            // Ne pas relancer l'exception pour les colonnes manquantes (42S22)
            // Laisser le contrôleur gérer cette erreur
            throw $e;
        }
    }

    /**
     * Exécute une requête SQL (INSERT, UPDATE, DELETE)
     */
    public function execute($sql, $params = []) {
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Erreur d'exécution SQL: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Exécute une requête et retourne une seule ligne
     */
    public function queryOne($sql, $params = []) {
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch();
        } catch (PDOException $e) {
            if ($e->getCode() == '42S02') {
                error_log("Table n'existe pas dans la base de données");
                return null;
            }
            throw $e;
        }
    }
    /**
     * Démarre une transaction
     */
    public function beginTransaction() {
        return $this->db->beginTransaction();
    }

    /**
     * Valide une transaction
     */
    public function commit() {
        return $this->db->commit();
    }

    /**
     * Annule une transaction
     */
    public function rollback() {
        return $this->db->rollBack();
    }

    /**
     * Retourne le nom de la table associée au modèle
     */
    public function getTable() {
        return $this->table;
    }
    /**
     * Génère le prochain numéro séquentiel pour une colonne donnée
     * Format: Eco - ddmmaa - 00001
     * 
     * @param string $column Nom de la colonne (ex: numero_facture)
     * @param string $prefix Prefixe (ex: Eco)
     * @param int $padding Nombre de zéros (par défaut 5)
     * @return string Le nouveau numéro
     */
    public function generateNextNumber($column, $prefix, $padding = 5) {
        $date = date('dmy'); // ddmmaa
        $separator = '-';
        $pattern = $prefix . $separator . $date . $separator . '%';
        
        $sql = "SELECT {$column} FROM {$this->table} 
                WHERE {$column} LIKE ? 
                ORDER BY {$column} DESC LIMIT 1";
        
        $lastRow = $this->queryOne($sql, [$pattern]);
        
        $nextNumber = 1;
        if ($lastRow) {
            $parts = explode($separator, $lastRow[$column]);
            $lastIndex = end($parts);
            $nextNumber = (int)$lastIndex + 1;
        }
        
        return $prefix . $separator . $date . $separator . str_pad((string)$nextNumber, $padding, '0', STR_PAD_LEFT);
    }
}

