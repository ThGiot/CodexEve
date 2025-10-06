-- Ajouter la colonne client_id aux tables existantes
ALTER TABLE grid_zone ADD COLUMN client_id INT(11) NOT NULL DEFAULT 0;
ALTER TABLE grid_poste_type ADD COLUMN client_id INT(11) NOT NULL DEFAULT 0;
ALTER TABLE grid_horaire ADD COLUMN client_id INT(11) NOT NULL DEFAULT 0;
ALTER TABLE grid_horaire_periode ADD COLUMN client_id INT(11) NOT NULL DEFAULT 0;
ALTER TABLE grid_assosiation ADD COLUMN client_id INT(11) NOT NULL DEFAULT 0;
ALTER TABLE grid_poste ADD COLUMN client_id INT(11) NOT NULL DEFAULT 0;

-- Mise à jour des tables pour les données existantes (inutile ici car la valeur par défaut est déjà 0)
UPDATE grid_zone SET client_id = 0;
UPDATE grid_poste_type SET client_id = 0;
UPDATE grid_horaire SET client_id = 0;
UPDATE grid_horaire_periode SET client_id = 0;
UPDATE grid_assosiation SET client_id = 0;
UPDATE grid_poste SET client_id = 0;


-- Suppression des colonnes existantes si elles n'ont pas encore la contrainte
ALTER TABLE grid_zone DROP COLUMN client_id;
ALTER TABLE grid_poste_type DROP COLUMN client_id;
ALTER TABLE grid_horaire DROP COLUMN client_id;
ALTER TABLE grid_horaire_periode DROP COLUMN client_id;
ALTER TABLE grid_assosiation DROP COLUMN client_id;
ALTER TABLE grid_poste DROP COLUMN client_id;

-- Ajouter la colonne client_id avec clé étrangère
ALTER TABLE grid_zone ADD COLUMN client_id INT(11) NOT NULL DEFAULT 0, ADD CONSTRAINT fk_grid_zone_client FOREIGN KEY (client_id) REFERENCES client(id) ON DELETE CASCADE;
ALTER TABLE grid_poste_type ADD COLUMN client_id INT(11) NOT NULL DEFAULT 0, ADD CONSTRAINT fk_grid_poste_type_client FOREIGN KEY (client_id) REFERENCES client(id) ON DELETE CASCADE;
ALTER TABLE grid_horaire ADD COLUMN client_id INT(11) NOT NULL DEFAULT 0, ADD CONSTRAINT fk_grid_horaire_client FOREIGN KEY (client_id) REFERENCES client(id) ON DELETE CASCADE;
ALTER TABLE grid_horaire_periode ADD COLUMN client_id INT(11) NOT NULL DEFAULT 0, ADD CONSTRAINT fk_grid_horaire_periode_client FOREIGN KEY (client_id) REFERENCES client(id) ON DELETE CASCADE;
ALTER TABLE grid_assosiation ADD COLUMN client_id INT(11) NOT NULL DEFAULT 0, ADD CONSTRAINT fk_grid_assosiation_client FOREIGN KEY (client_id) REFERENCES client(id) ON DELETE CASCADE;
ALTER TABLE grid_poste ADD COLUMN client_id INT(11) NOT NULL DEFAULT 0, ADD CONSTRAINT fk_grid_poste_client FOREIGN KEY (client_id) REFERENCES client(id) ON DELETE CASCADE;

