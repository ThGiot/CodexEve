<?php
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
    public function getAssociationsOptions(int $clientId): array {
        $sql = "SELECT id, nom FROM grid_association WHERE client_id = :client_id ORDER BY nom";
        return $this->formatOptions($sql, 'id', 'nom', ['client_id' => $clientId]);
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