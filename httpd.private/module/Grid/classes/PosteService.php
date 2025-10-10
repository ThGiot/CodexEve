<?php
namespace Grid;
use PDO;
class PosteService {
    private PDO $dbh;

    public function __construct(PDO $dbh) {
        $this->dbh = $dbh;
    }

    /**
     * Récupère les associations sous forme d'un tableau formaté pour les listes déroulantes
     */

     public function getAllOptions(int $clientId): array {
        return [
            'associations' => $this->getAssociationsOptions($clientId),
            'types' => $this->getPosteTypesOptions($clientId),
            'zones' => $this->getZonesOptions($clientId),
            'horaires' => $this->getHorairesOptions($clientId)
        ];
    }
    
    public function getPosteTypesOptions(int $clientId): array {
        $sql = "SELECT id, nom FROM grid_poste_type WHERE client_id = :client_id ORDER BY nom";
        return $this->formatOptions($sql, 'id', 'nom', ['client_id' => $clientId]);
    }
    
    public function getZonesOptions(int $clientId): array {
        $sql = "SELECT id, nom FROM grid_zone WHERE client_id = :client_id ORDER BY nom";
        return $this->formatOptions($sql, 'id', 'nom', ['client_id' => $clientId]);
    }
    
    public function getHorairesOptions(int $clientId): array {
        $sql = "SELECT id, nom FROM grid_horaire WHERE client_id = :client_id ORDER BY nom";
        return $this->formatOptions($sql, 'id', 'nom', ['client_id' => $clientId]);
    }
    public function getHorairesDisponibilite(int $clientId, int $associationId): array {
        $sql = "
            SELECT 
                gh.id AS horaire_id,
                gh.nom AS horaire_nom,
                COALESCE(MAX(gad.nb), 0) AS nb
            FROM grid_horaire gh
            JOIN grid_horaire_periode ghp ON gh.id = ghp.horaire_id
            LEFT JOIN grid_association_dispo gad ON gad.horaire_id = gh.id AND gad.association_id = :association_id
            WHERE gh.client_id = :client_id
            GROUP BY gh.id, gh.nom
            HAVING COUNT(DISTINCT ghp.nom) > 1
            ORDER BY gh.nom
        ";
    
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':client_id', $clientId, PDO::PARAM_INT);
        $stmt->bindParam(':association_id', $associationId, PDO::PARAM_INT);
        $stmt->execute();
    
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getDisposParAssociationEtHoraire(int $clientId): array {
        $sql = "
            SELECT ga.nom AS association_nom, gh.nom AS horaire_nom, gad.nb AS dispo
            FROM grid_association_dispo gad
            JOIN grid_association ga ON gad.association_id = ga.id
            JOIN grid_horaire gh ON gad.horaire_id = gh.id
            WHERE ga.client_id = :client_id_1 AND gh.client_id = :client_id_2
        ";
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':client_id_1', $clientId, PDO::PARAM_INT);
        $stmt->bindParam(':client_id_2', $clientId, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        $result = [];
        foreach ($rows as $row) {
            $result[$row['association_nom']][$row['horaire_nom']] = (int)$row['dispo'];
        }
    
        return $result;
    }
    
    public function getTableauHorairesMultiplies(int $clientId): array {
        $sql = "
            SELECT 
                ga.nom AS association_nom,
                gh.nom AS horaire_nom,
                COUNT(DISTINCT concat_ws('-', ghp.horaire_id, ghp.nom)) AS nb_periodes,
                COUNT(DISTINCT gp.id) AS nb_postes
            FROM grid_poste gp
            JOIN grid_horaire gh ON gp.horaire_id = gh.id
            JOIN grid_horaire_periode ghp ON gh.id = ghp.horaire_id
            JOIN grid_association ga ON gp.association_id = ga.id
            WHERE gh.client_id = :client_id_1 AND gp.client_id = :client_id_2
            GROUP BY ga.nom, gh.nom
            HAVING nb_periodes > 1
            ORDER BY ga.nom, gh.nom
        ";
    
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':client_id_1', $clientId, PDO::PARAM_INT);
        $stmt->bindParam(':client_id_2', $clientId, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        // Construction dynamique du tableau final
        $result = [];
        $horaireNoms = [];
    
        foreach ($rows as $row) {
            $association = $row['association_nom'];
            $horaire = $row['horaire_nom'];
            $val = (int)$row['nb_periodes'] * (int)$row['nb_postes'];
    
            $result[$association][$horaire] = $val;
            $horaireNoms[$horaire] = true;
        }
    
        // Compléter les valeurs manquantes par 0 et calculer les totaux
        $sortedResult = [];
        foreach ($result as $association => $horaires) {
            $total = 0;
            foreach (array_keys($horaireNoms) as $hName) {
                $horaires[$hName] = $horaires[$hName] ?? 0;
                $total += $horaires[$hName];
            }
            $horaires['Total'] = $total;

            $total = $horaires['Total'];
            unset($horaires['Total']);
            ksort($horaires);
            $horaires['Total'] = $total;

            $sortedResult[$association] = $horaires;
        }

        
    
        return $sortedResult;
    }

    public function getAssociationsOptions(int $clientId): array {
        $sql = "SELECT id, nom FROM grid_association WHERE client_id = :client_id ORDER BY nom";
        return $this->formatOptions($sql, 'id', 'nom', ['client_id' => $clientId]);
    }
    
    public function getTableauCroiseVolontaires(int $clientId, int $associationId): array {
        $sql = "
            SELECT 
                gz.nom AS zone_nom,
                gh.nom AS horaire_nom,
                SUM(
                    (SELECT COUNT(DISTINCT CONCAT_WS('-', ghp.horaire_id, ghp.nom))
                     FROM grid_horaire_periode ghp
                     WHERE ghp.horaire_id = gh.id)
                ) AS nb_total_volontaire,
                COUNT(*) AS nb_postes
            FROM grid_poste gp
            JOIN grid_zone gz ON gp.zone_id = gz.id
            JOIN grid_horaire gh ON gp.horaire_id = gh.id
            WHERE gp.client_id = :client_id
              AND gp.association_id = :association_id
            GROUP BY gz.nom, gh.nom
            ORDER BY gz.nom, gh.nom
        ";
    
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':client_id', $clientId, PDO::PARAM_INT);
        $stmt->bindParam(':association_id', $associationId, PDO::PARAM_INT);
        $stmt->execute();
    
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        $result = [];
        // Initialisation des horaires possibles
        $horaireNoms = [];
        foreach ($rows as $row) {
            $horaireNoms[$row['horaire_nom']] = true;
        }
        ksort($horaireNoms);

        // Initialisation du tableau avec des zéros
        $result = [];
        foreach ($rows as $row) {
            $zone = $row['zone_nom'];
            $horaire = $row['horaire_nom'];
            $val = (int) $row['nb_total_volontaire'];

            // Initialiser si pas encore fait
            if (!isset($result[$zone])) {
                foreach ($horaireNoms as $h => $_) {
                    $result[$zone][$h] = 0;
                }
            }

            // Affecter la valeur correcte
            $result[$zone][$horaire] = $val;
        }

        // Calcul des totaux par ligne
        foreach ($result as $zone => $horaires) {
            $result[$zone]['Total'] = array_sum($horaires);
        }

        // Trier chaque ligne + mettre Total à la fin
        $sorted = [];
        foreach ($result as $zone => $horaires) {
            $total = $horaires['Total'];
            unset($horaires['Total']);
            ksort($horaires);
            $horaires['Total'] = $total;
            $sorted[$zone] = $horaires;
        }

        return $sorted;

    }
    
    
    
    
    /**
     * Récupère un poste spécifique
     */
    public function getPoste(int $clientId, int $posteId): ?array {
        $sql = "SELECT gp.id AS poste_id,
                    gp.nom, 
                    gp.numero, 
                    gh.nom AS horaire, 
                    gh.id AS horaire_id,
                    ga.nom AS association, 
                    gpt.nom AS poste_type,
                    gz.nom AS zone_nom,
                    ga.id AS association_id, 
                    gpt.id AS poste_type_id, 
                    gz.id AS zone_id
                FROM grid_poste gp
                LEFT JOIN grid_horaire gh ON gp.horaire_id = gh.id 
                LEFT JOIN grid_association ga ON ga.id = gp.association_id
                LEFT JOIN grid_poste_type gpt ON gpt.id = gp.poste_type_id
                LEFT JOIN grid_zone gz ON gz.id = gp.zone_id
                WHERE gp.client_id = :client_id AND gp.id = :poste_id
                ORDER BY gz.nom, gpt.nom, ga.nom";

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':client_id', $clientId, PDO::PARAM_INT);
        $stmt->bindParam(':poste_id', $posteId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function getZones(int $clientId): ?array {
        $sql = "SELECT * FROM grid_zone WHERE client_id = :client_id 
                ORDER BY nom";

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':client_id', $clientId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: null;
    }

    public function getAssociations(int $clientId): ?array {
        $sql = "SELECT * FROM grid_association WHERE client_id = :client_id 
                ORDER BY nom";

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':client_id', $clientId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: null;
    }

    public function getAssociation(int $clientId,int $id): ?array {
        $sql = "SELECT * FROM grid_association WHERE client_id = :client_id AND id = :id 
                ORDER BY nom";

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':client_id', $clientId, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function getHorairesParJour(int $clientId, int $associationId): array {
    $sql = "SELECT
        gp.nom AS poste_nom,
            DAYNAME(ghp.date_debut) AS jour_semaine,
            TIME(ghp.date_debut) AS heure_debut,
            TIME(ghp.date_fin) AS heure_fin,
            gz.nom as zone_nom,  
            ghp.nom AS periode_nom,
            gpt.nom as poste_type
        FROM grid_poste gp
        JOIN grid_horaire gh ON gp.horaire_id = gh.id
        JOIN grid_poste_type gpt ON gp.poste_type_id = gpt.id
        JOIN grid_horaire_periode ghp ON gh.id = ghp.horaire_id
        JOIN grid_zone gz ON gz.id = gp.zone_id
        WHERE gp.client_id = :client_id
        AND gp.association_id = :association_id
        ORDER BY ghp.date_debut,ghp.nom, gp.nom
    ";

    $stmt = $this->dbh->prepare($sql);
    $stmt->bindParam(':client_id', $clientId, PDO::PARAM_INT);
    $stmt->bindParam(':association_id', $associationId, PDO::PARAM_INT);
    $stmt->execute();

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
  
    // 1. Construire un index rapide
    $index = [];
    foreach ($rows as $key => $row) {
        $poste = $row['poste_nom'];
        $jour = $row['jour_semaine'];
        $heure = $row['heure_debut'];
        $index[$poste][$jour][$heure] = $key; // On stocke l'index dans $rows pour pouvoir le modifier/supprimer ensuite
    }

    // 2. Parcourir et vérifier la condition
    $jours = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

    foreach ($rows as $i => $row) {
        // ⛔ Si la ligne est 00:00 → 00:00, on NE FAIT RIEN
        if ($row['heure_debut'] === '00:00:00' && $row['heure_fin'] === '00:00:00') {
            continue;
        }
    
        // ✅ Si la ligne se termine à minuit, elle est candidate à une fusion
        if ($row['heure_fin'] === '00:00:00') {
            $poste = $row['poste_nom'];
            $jour = $row['jour_semaine'];
            $j = array_search($jour, $jours);
            $jourSuivant = $jours[($j + 1) % 7];
    
            if (isset($index[$poste][$jourSuivant]['00:00:00'])) {
                $idxSuivant = $index[$poste][$jourSuivant]['00:00:00'];
                $rowSuivante = $rows[$idxSuivant];
    
                // ❌ On ne fusionne pas si la suivante est aussi 00:00 → 00:00
                if ($rowSuivante['heure_debut'] === '00:00:00' && $rowSuivante['heure_fin'] === '00:00:00') {
                    continue;
                }
    
                // ✅ Sinon, on fusionne et on supprime la ligne suivante
                $rows[$i]['heure_fin'] = $rowSuivante['heure_fin'];
                unset($rows[$idxSuivant]);
                unset($index[$poste][$jourSuivant]['00:00:00']);
            }
        }
    }
    


    // Traduction des jours
    $joursMap = [
        'Thursday' => 'Jeudi',
        'Friday' => 'Vendredi',
        'Saturday' => 'Samedi',
        'Sunday' => 'Dimanche',
        'Monday' => 'Lundi'
    ];

    $result = [];

    
    $rows = array_map(function($row) use ($joursMap) {
        if (isset($joursMap[$row['jour_semaine']])) {
            $row['jour_semaine'] = $joursMap[$row['jour_semaine']];
        }
        return $row;
    }, $rows);
    

        return $rows;
    }

    

    
    public function getPostes(int $clientId): array {
        $sql = "SELECT gp.id AS poste_id,
                       gp.nom, 
                       gp.numero, 
                       gh.nom AS horaire, 
                       gh.id AS horaire_id,
                       ga.nom AS association, 
                       gpt.nom AS poste_type,
                       gz.nom AS zone_nom,
                       ga.id AS association_id, 
                       gpt.id AS poste_type_id, 
                       gz.id AS zone_id
                FROM grid_poste gp
                LEFT JOIN grid_horaire gh ON gp.horaire_id = gh.id 
                LEFT JOIN grid_association ga ON ga.id = gp.association_id
                LEFT JOIN grid_poste_type gpt ON gpt.id = gp.poste_type_id
                LEFT JOIN grid_zone gz ON gz.id = gp.zone_id
                WHERE gp.client_id = :client_id
                ORDER BY gz.nom, gpt.nom, ga.nom";
    
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':client_id', $clientId, PDO::PARAM_INT);
        $stmt->execute();
    
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    

    /**
     * Exécute une requête SQL et retourne les résultats formatés en options de liste déroulante
     */
    private function formatOptions(string $sql, string $idKey, string $textKey, array $params = []): array {
        $stmt = $this->dbh->prepare($sql);
    
        // Lier les paramètres s'ils existent
        foreach ($params as $key => $value) {
            $stmt->bindValue(":$key", $value, PDO::PARAM_INT);
        }
    
        $stmt->execute();
    
        return array_map(fn($row) => ['value' => $row[$idKey], 'text' => $row[$textKey]], $stmt->fetchAll(PDO::FETCH_ASSOC));
    }
    
}
?>