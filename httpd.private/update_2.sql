CREATE TABLE IF NOT EXISTS grid_zone (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(250) NOT NULL
);

CREATE TABLE IF NOT EXISTS grid_poste_type (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(250) NOT NULL
);

CREATE TABLE IF NOT EXISTS grid_horaire (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(250) NOT NULL,
    individuel BOOLEAN DEFAULT 0,
    personne_nb INT(10)
);

CREATE TABLE IF NOT EXISTS grid_horaire_periode (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    horaire_id INT(11) NOT NULL,
    nom VARCHAR(250) NOT NULL,
    date_debut DATETIME NOT NULL,
    date_fin DATETIME NOT NULL,
    FOREIGN KEY (horaire_id) REFERENCES grid_horaire(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS grid_assosiation (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(250) NOT NULL,
    adresse VARCHAR(500) NULL,
    contact_nom VARCHAR(500) NULL,
    contact_email VARCHAR(500) NULL,
    contact_telephone VARCHAR(500) NULL
);

CREATE TABLE IF NOT EXISTS grid_poste (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(250) NOT NULL,
    numero INT(10) DEFAULT NULL,
    horaire_id INT(11) DEFAULT NULL,
    assosiation_id INT(11) DEFAULT NULL,
    zone_id INT(11) DEFAULT NULL,
    poste_type_id INT(11) DEFAULT NULL,
    FOREIGN KEY (horaire_id) REFERENCES grid_horaire(id) ON DELETE SET NULL,
    FOREIGN KEY (assosiation_id) REFERENCES grid_assosiation(id) ON DELETE SET NULL,
    FOREIGN KEY (zone_id) REFERENCES grid_zone(id) ON DELETE SET NULL,
    FOREIGN KEY (poste_type_id) REFERENCES grid_poste_type(id) ON DELETE SET NULL
);

INSERT INTO grid_assosiation (nom) VALUES 
('3iS Stew'),
('BCP stew'),
('BCP'),
('195e Castor'),
('Sc marr 75'),
('Hight Sec'),
('pio gemblo'),
('Coordi'),
('Oussabder'),
('Sc 55°'),
('Sc 48° lio'),
('ZZ Ent pub'),
('Patro Godinne'),
('27° ND'),
('Sc 105°'),
('G S pro'),
('R66'),
('Zzartist'),
('ZZBSpalais'),
('3iS'),
('G S stew'),
('ZZRed&Black'),
('ZZ green'),
('ZZ resto'),
('team front');

INSERT INTO grid_zone (nom) VALUES 
('BS Palais'),
('BS Usvip'),
('Red&Black'),
('Green'),
('Fox resto'),
('Entrée Public'),
('Artist'),
('Camping'),
('Coordination'),
('virgi'),
('Nuit'),
('Jour'),
('Entrée ticket'),
('mont bur sec'),
('Vrigi'),
('Bénéte'),
('appui'),
('strombeek');


INSERT INTO `grid_poste_type` (`id`, `nom`) VALUES (NULL, 'Safety Crew'), (NULL, 'Steward'), (NULL, 'Pro');
INSERT INTO `grid_horaire` (`id`, `nom`, `individuel`, `personne_nb`) VALUES (NULL, 'ABC', '0', '3');

INSERT INTO `grid_horaire_periode` (`id`, `horaire_id`, `nom`, `date_debut`, `date_fin`) VALUES (NULL, '1', 'A', '2025-06-26 08:00:00', '2025-06-26 13:00:00');
INSERT INTO `grid_horaire_periode` (`id`, `horaire_id`, `nom`, `date_debut`, `date_fin`) VALUES (NULL, '1', 'A', '2025-06-27 18:00:00', '2025-06-26 21:00:00');
INSERT INTO `grid_horaire_periode` (`id`, `horaire_id`, `nom`, `date_debut`, `date_fin`) VALUES (NULL, '1', 'A', '2025-06-28 00:00:00', '2025-06-27 03:00:00');
INSERT INTO `grid_horaire_periode` (`id`, `horaire_id`, `nom`, `date_debut`, `date_fin`) VALUES (NULL, '1', 'A', '2025-06-28 14:00:00', '2025-06-27 17:00:00');
INSERT INTO `grid_horaire_periode` (`id`, `horaire_id`, `nom`, `date_debut`, `date_fin`) VALUES (NULL, '1', 'A', '2025-06-28 21:00:00', '2025-06-29 00:00:00');
INSERT INTO `grid_horaire_periode` (`id`, `horaire_id`, `nom`, `date_debut`, `date_fin`) VALUES (NULL, '1', 'A', '2025-06-29 09:00:00', '2025-06-29 15:00:00');

INSERT INTO `grid_poste` (`id`, `nom`, `numero`, `horaire_id`, `assosiation_id`, `zone_id`, `poste_type_id`) VALUES (NULL, 'Salon 58', '1', '1', '13', '1', '1');