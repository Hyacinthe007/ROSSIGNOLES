-- Ajout des permissions pour le module de paie
-- À exécuter après la migration principale

-- 1. Ajouter les permissions
INSERT INTO permissions (code, module, action, description, created_at) VALUES
('paie.read', 'paie', 'read', 'Consulter la paie et les bulletins', NOW()),
('paie.create', 'paie', 'create', 'Créer des bulletins de paie', NOW()),
('paie.update', 'paie', 'update', 'Modifier la configuration de paie', NOW()),
('paie.validate', 'paie', 'validate', 'Valider des bulletins de paie', NOW()),
('paie.delete', 'paie', 'delete', 'Supprimer des bulletins (brouillon)', NOW())
ON DUPLICATE KEY UPDATE description = VALUES(description);

-- 2. Attribuer toutes les permissions au groupe Administrateur (id=1)
INSERT INTO group_permissions (group_id, permission_id)
SELECT 1, id FROM permissions WHERE module = 'paie'
ON DUPLICATE KEY UPDATE group_id = group_id;

-- 3. Attribuer les permissions de lecture au groupe Comptable (id=3) si existe
INSERT INTO group_permissions (group_id, permission_id)
SELECT 3, id FROM permissions WHERE module = 'paie' AND action IN ('read', 'create', 'validate')
ON DUPLICATE KEY UPDATE group_id = group_id;

SELECT 'Permissions de paie ajoutées avec succès !' as message;
