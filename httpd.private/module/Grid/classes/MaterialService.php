<?php
namespace Grid;

use App\Database;
use PDO;
class MaterialService {

    public static function getAllMaterials($clientId): array {
        $pdo = Database::getConnection();

        $sql = "
            SELECT *
            FROM grid_materials gh
            WHERE client_id = :client_id
            ORDER BY nom
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':client_id', $clientId, PDO::PARAM_INT);
        $stmt->execute();
    
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function addMaterial(int $clientId, string $material_nom): array {
        try {
            $pdo = Database::getConnection();
    
            // Vérifie si le nom est vide
            if (trim($material_nom) === '') {
                return [
                    'success' => false,
                    'message' => "Le nom du matériel ne peut pas être vide."
                ];
            }
    
            // Vérifie si ce matériel existe déjà 
            $checkSql = "
                SELECT id FROM grid_materials
                WHERE client_id = :client_id AND nom = :nom
            ";
            $checkStmt = $pdo->prepare($checkSql);
            $checkStmt->execute([
                ':client_id' => $clientId,
                ':nom' => $material_nom
            ]);
            if ($checkStmt->fetch()) {
                return [
                    'success' => false,
                    'message' => "Ce matériel existe déjà."
                ];
            }
    
            // Insertion
            $insertSql = "
                INSERT INTO grid_materials (client_id, nom)
                VALUES (:client_id, :nom)
            ";
            $insertStmt = $pdo->prepare($insertSql);
            $insertStmt->bindParam(':client_id', $clientId, PDO::PARAM_INT);
            $insertStmt->bindParam(':nom', $material_nom, PDO::PARAM_STR);
            $insertStmt->execute();
    
            $materialId = $pdo->lastInsertId();
    
            return [
                'success' => true,
                'message' => "Matériel ajouté avec succès.",
                'material_id' => $materialId,
                'material_nom' => $material_nom
            ];
    
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => "Erreur base de données : " . $e->getMessage()
            ];
        }
    }

    public static function deleteMaterial(int $clientId, int $materialId): array {
        try {
            $pdo = Database::getConnection();
    
            // Vérifie si le matériel existe pour ce client
            $checkSql = "
                SELECT id FROM grid_materials
                WHERE id = :id AND client_id = :client_id
            ";
            $checkStmt = $pdo->prepare($checkSql);
            $checkStmt->execute([
                ':id' => $materialId,
                ':client_id' => $clientId
            ]);
    
            if (!$checkStmt->fetch()) {
                return [
                    'success' => false,
                    'message' => "Matériel introuvable ou non autorisé."
                ];
            }
    
            // Suppression
            $deleteSql = "
                DELETE FROM grid_materials
                WHERE id = :id AND client_id = :client_id
            ";
            $deleteStmt = $pdo->prepare($deleteSql);
            $deleteStmt->execute([
                ':id' => $materialId,
                ':client_id' => $clientId
            ]);
    
            return [
                'success' => true,
                'message' => "Matériel supprimé avec succès."
            ];
    
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => "Erreur lors de la suppression : " . $e->getMessage()
            ];
        }
    }
    
    
}
?>