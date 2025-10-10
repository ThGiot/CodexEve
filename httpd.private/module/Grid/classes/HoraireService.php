<?php
declare(strict_types=1);
namespace Grid;
use PDO;

class HoraireService {
    private PDO $dbh;

    public function __construct(PDO $dbh) {
        $this->dbh = $dbh;
    }

    /**
     * 📌 Récupère tous les horaires avec leurs périodes associées.
     */
    public function getHoraires(int $clientId): array {
        $query = "
            SELECT *            
            FROM grid_horaire h
            WHERE h.client_id = :client_id
            ORDER BY h.nom ASC
        ";
        $stmt = $this->dbh->prepare($query);
        $stmt->bindValue(':client_id', $clientId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
     public function getHorairesAvecPeriodes(int $clientId): array {
        $query = "
            SELECT h.id AS horaire_id, h.nom AS horaire_nom, 
                   hp.id AS periode_id, hp.date_debut, hp.date_fin
            FROM grid_horaire h
            LEFT JOIN grid_horaire_periode hp ON h.id = hp.horaire_id
            WHERE h.client_id = :client_id
            ORDER BY hp.date_debut ASC
        ";
        $stmt = $this->dbh->prepare($query);
        $stmt->bindValue(':client_id', $clientId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function mettreAJourHoraire(int $horaireId, array $periodes, int $clientId): bool {
        try {
            // Démarrer une transaction
            $this->dbh->beginTransaction();
    
            // ❌ Suppression des anciennes périodes
            $query = "DELETE FROM grid_horaire_periode WHERE horaire_id = :horaire_id AND client_id = :client_id";
            $stmt = $this->dbh->prepare($query);
            $stmt->bindValue(':horaire_id', $horaireId, PDO::PARAM_INT);
            $stmt->bindValue(':client_id', $clientId, PDO::PARAM_INT);
            $stmt->execute();
    
            // ✅ Ajout des nouvelles périodes
            $query = "INSERT INTO grid_horaire_periode (horaire_id, nom, date_debut, date_fin, client_id) 
                      VALUES (:horaire_id, :nom, :date_debut, :date_fin, :client_id)";
            $stmt = $this->dbh->prepare($query);
    
            foreach ($periodes as $periode) {
                $stmt->bindValue(':horaire_id', $horaireId, PDO::PARAM_INT);
                $stmt->bindValue(':nom', $periode['nom']);
                $stmt->bindValue(':date_debut', $periode['date_debut']);
                $stmt->bindValue(':date_fin', $periode['date_fin']);
                $stmt->bindValue(':client_id', $clientId, PDO::PARAM_INT);
                $stmt->execute();
            }
    
            // ✅ Valider la transaction
            $this->dbh->commit();
            return true;
        } catch (PDOException $e) {
            // ❌ Annuler la transaction en cas d'erreur
            $this->dbh->rollBack();
            error_log("Erreur lors de la mise à jour de l'horaire ID: {$horaireId} - " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * 📌 Récupère un horaire spécifique et ses périodes.
     */
    public function getHoraireAvecPeriodes(int $horaireId): ?array {
        $query = "
            SELECT h.id AS horaire_id, h.nom AS horaire_nom, 
                   hp.id AS periode_id, hp.date_debut, hp.date_fin, hp.nom AS periode_nom 
            FROM grid_horaire h
            LEFT JOIN grid_horaire_periode hp ON h.id = hp.horaire_id
            WHERE h.id = :horaire_id
            ORDER BY hp.nom, hp.date_debut ASC
        ";
        $stmt = $this->dbh->prepare($query);
        $stmt->bindValue(':horaire_id', $horaireId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * 📌 Ajoute une nouvelle période à un horaire.
     */
    public function ajouterPeriode(int $horaireId, string $nom, string $dateDebut, string $dateFin, int $clientId): bool {
        $query = "INSERT INTO grid_horaire_periode (horaire_id, nom, date_debut, date_fin, client_id) 
                  VALUES (:horaire_id, :nom, :date_debut, :date_fin, :client_id)";
        $stmt = $this->dbh->prepare($query);
        $stmt->bindValue(':horaire_id', $horaireId, PDO::PARAM_INT);
        $stmt->bindValue(':nom', $nom);
        $stmt->bindValue(':date_debut', $dateDebut);
        $stmt->bindValue(':date_fin', $dateFin);
        $stmt->bindValue(':client_id', $clientId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * 📌 Met à jour une période existante.
     */
    public function mettreAJourPeriode(int $periodeId, string $dateDebut, string $dateFin): bool {
        $query = "UPDATE grid_horaire_periode 
                  SET date_debut = :date_debut, date_fin = :date_fin 
                  WHERE id = :periode_id";
        $stmt = $this->dbh->prepare($query);
        $stmt->bindValue(':date_debut', $dateDebut);
        $stmt->bindValue(':date_fin', $dateFin);
        $stmt->bindValue(':periode_id', $periodeId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getPostesParHoraire(int $clientId,PDO $dbh): array {
        $sql = "
            SELECT horaire_id, COUNT(*) as nb_postes
            FROM grid_poste
            WHERE client_id = :client_id
            GROUP BY horaire_id
        ";

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':client_id', $clientId, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $rows;
    }
    public static function getNombreParPeriode(int $clientId,int $horaire_id,$heureDebut,$heureFin, PDO $dbh){
        $sql = "
        SELECT COUNT(DISTINCT nom) AS nb_present
        FROM grid_horaire_periode
        WHERE horaire_id = :horaire_id
        AND date_debut <= :heure_fin
        AND date_fin >= :heure_debut

    ";

    $stmt =$dbh->prepare($sql);
    $stmt->bindParam(':heure_debut', $heureDebut);
    $stmt->bindParam(':heure_fin', $heureFin);
    $stmt->bindParam(':horaire_id', $horaire_id);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['nb_present']?? null;

    }
    public static function getNom(int $clientId,int $id, PDO $dbh): ?string {
        $query = "SELECT nom FROM grid_horaire WHERE id = :id AND client_id = :client_id";
        $stmt = $dbh->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':client_id', $clientId, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['nom'] : null;
    }
    
    public function supprimerHoraire(int $horaireId): bool {
        try {
            // Démarrer une transaction
            $this->dbh->beginTransaction();
    
            // Supprimer d'abord les périodes associées
            $query = "DELETE FROM grid_horaire_periode WHERE horaire_id = :horaire_id";
            $stmt = $this->dbh->prepare($query);
            $stmt->bindValue(':horaire_id', $horaireId, PDO::PARAM_INT);
            $stmt->execute();
    
            // Supprimer ensuite l'horaire
            $query = "DELETE FROM grid_horaire WHERE id = :horaire_id";
            $stmt = $this->dbh->prepare($query);
            $stmt->bindValue(':horaire_id', $horaireId, PDO::PARAM_INT);
            $stmt->execute();
    
            // Tout s'est bien passé, on valide la transaction
            $this->dbh->commit();
            return true;
    
        } catch (PDOException $e) {
            // Une erreur s'est produite, on annule tout
            $this->dbh->rollBack();
            error_log("Erreur lors de la suppression de l'horaire ID: {$horaireId} - " . $e->getMessage());
            return false;
        }
    }

    public function supprimerPeriode(int $periodeId): bool {
        try {
            // Démarrer une transaction
            $this->dbh->beginTransaction();
    
            // Supprimer d'abord les périodes associées
            $query = "DELETE FROM grid_horaire_periode WHERE id = :id";
            $stmt = $this->dbh->prepare($query);
            $stmt->bindValue(':id', $periodeId, PDO::PARAM_INT);
            $stmt->execute();
            // Tout s'est bien passé, on valide la transaction
            $this->dbh->commit();
            return true;
    
        } catch (PDOException $e) {
            // Une erreur s'est produite, on annule tout
            $this->dbh->rollBack();
            error_log("Erreur lors de la suppression de la plage horaire ID: {$periodeId} - " . $e->getMessage());
            return false;
        }
    }
    
}
