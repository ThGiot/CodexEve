INSERT INTO `module` (`id`, `nom`, `logo`) VALUES (NULL, 'Clinical', NULL);

CREATE TABLE clinical_hospital (
                                   id INT AUTO_INCREMENT PRIMARY KEY,
                                   code VARCHAR(10) NOT NULL UNIQUE,
                                   label VARCHAR(255) NOT NULL,
                                   created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                                   updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE clinical_procedure (
                                    id INT AUTO_INCREMENT PRIMARY KEY,
                                    slug VARCHAR(100) NOT NULL UNIQUE,
                                    title VARCHAR(255) NOT NULL,
                                    type VARCHAR(50) DEFAULT NULL,
                                    system VARCHAR(100) DEFAULT NULL,
                                    category VARCHAR(100) DEFAULT NULL,
                                    summary TEXT DEFAULT NULL,
                                    body LONGTEXT DEFAULT NULL,
                                    version VARCHAR(50) DEFAULT NULL,
                                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE clinical_procedure_tag (
                                        id INT AUTO_INCREMENT PRIMARY KEY,
                                        name VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE clinical_procedure_tag_link (
                                             id INT AUTO_INCREMENT PRIMARY KEY,
                                             procedure_id INT NOT NULL,
                                             tag_id INT NOT NULL,
                                             FOREIGN KEY (procedure_id) REFERENCES clinical_procedure(id)
                                                 ON DELETE CASCADE ON UPDATE CASCADE,
                                             FOREIGN KEY (tag_id) REFERENCES clinical_procedure_tag(id)
                                                 ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE clinical_procedure_variant (
                                            id INT AUTO_INCREMENT PRIMARY KEY,
                                            procedure_id INT NOT NULL,
                                            hospital_id INT NOT NULL,
                                            note TEXT DEFAULT NULL,
                                            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                                            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                                            FOREIGN KEY (procedure_id) REFERENCES clinical_procedure(id)
                                                ON DELETE CASCADE ON UPDATE CASCADE,
                                            FOREIGN KEY (hospital_id) REFERENCES clinical_hospital(id)
                                                ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE clinical_procedure_variant_block (
                                                  id INT AUTO_INCREMENT PRIMARY KEY,
                                                  variant_id INT NOT NULL,
                                                  html LONGTEXT NOT NULL,
                                                  FOREIGN KEY (variant_id) REFERENCES clinical_procedure_variant(id)
                                                      ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE clinical_user_hospital (
                                        id INT AUTO_INCREMENT PRIMARY KEY,
                                        user_id INT NOT NULL,
                                        hospital_id INT NOT NULL,
                                        FOREIGN KEY (hospital_id) REFERENCES clinical_hospital(id)
                                            ON DELETE CASCADE ON UPDATE CASCADE
    -- la clé user_id peut pointer vers ta table users si elle existe :
    -- FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
INSERT INTO clinical_hospital (code, label)
VALUES
    ('H1', 'Hôpital Universitaire'),
    ('H2', 'Centre Hospitalier Communautaire');
INSERT INTO clinical_procedure_tag (name)
VALUES
    ('arythmie'), ('ECG'), ('urgence'),
    ('potassium'), ('électrolyte'),
    ('pré-hospitalier'), ('voie aérienne'), ('ventilation'), ('masque-ballon');

INSERT INTO clinical_procedure (slug, title, type, system, category, summary, body, version, created_at, updated_at) VALUES


ALTER TABLE clinical_user_hospital
    ADD CONSTRAINT fk_clinical_user_hospital_user
        FOREIGN KEY (user_id)
            REFERENCES users(id)
            ON DELETE CASCADE
            ON UPDATE CASCADE;                                                                                                                  ('bradycardia', 'Bradycardie', 'general', 'Cardiaque', 'ACLS',
                                                                                                                          "Évaluation initiale et stabilisation d'une bradycardie symptomatique adulte.",
                                                                                                                          '<p><strong>Évaluation initiale :</strong> vérifier la conscience, voie aérienne, respiration, circulation. Monitorer ECG, saturométrie et pression artérielle.</p>
                                                                                                                         <p>Si la bradycardie est symptomatique, appliquer le protocole local. Rechercher des causes réversibles telles que l\'<span class="inline-link" data-link="hypokalaemia">hypokaliémie</span>, l\'hypoxie ou l\'hypothermie.</p>
                                                                                                                             <p class="muted">Dosages et posologies affichés à titre indicatif uniquement pour le prototype.</p>',
 '0.1-demo', '2025-03-02', '2025-09-10'),

('hypokalaemia', 'Hypokaliémie', 'general', 'Métabolique', 'Électrolytes',
 'Évaluation et conduite à tenir devant un potassium bas.',
 '<p><strong>Vue d\'ensemble :</strong> confirmer l\'hypokaliémie, préciser le contexte (pertes digestives, médicaments, alcalose...).</p>
<p>Évaluer la sévérité (ECG, signes musculaires) et corriger le magnésium si nécessaire. Cette fiche est fournie pour la maquette.</p>',
 '0.1-demo', '2025-04-11', '2025-08-05'),

('anaphylaxis-smur', 'Anaphylaxie (SMUR)', 'smur', 'Respiratoire', 'Allergie',
 'Reconnaissance et actions immédiates en situation pré-hospitalière (prototype).',
 '<p><strong>Reconnaissance :</strong> apparition rapide de symptômes cutanés, respiratoires et hémodynamiques après exposition à un allergène.</p>
<p><strong>Actions immédiates :</strong> administration d\'adrénaline intramusculaire selon le protocole local, oxygénothérapie et préparation du transport médicalisé.</p>',
                                                                                                                          '0.1-demo', '2025-02-01', '2025-09-14'),

                                                                                                                         ('airway-basic', 'Voie aérienne — gestes de base (SMUR)', 'smur', 'Respiratoire', 'Voies aériennes',
                                                                                                                          'Manœuvres d\'ouverture et ventilation au masque (prototype).',
 '<p><strong>Positionnement :</strong> bascule prudente de la tête, subluxation mandibulaire si suspicion de trauma cervical.</p>
<p>Ventilation masque-ballon avec deux opérateurs lorsque possible. Contenu fictif pour test design.</p>',
 '0.1-demo', '2025-01-05', '2025-07-22');


