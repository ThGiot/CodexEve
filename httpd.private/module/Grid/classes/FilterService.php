<?php
namespace Grid;
use PDO;

class FilterService
{
    private PDO $dbh;

    public function __construct(PDO $dbh)
    {
        $this->dbh = $dbh;
    }

    /**
     * Récupère les options pour les select (associations, types et zones).
     *
     * @param int         $clientId L'identifiant du client
     * @param string|null $filterType Optionnellement, spécifier 'associations', 'types' ou 'zones'
     *                                pour ne récupérer que ce type d'option.
     * @return array Si $filterType est null, retourne un tableau associatif contenant les trois listes.
     *               Sinon, retourne uniquement la liste correspondant au type demandé.
     * @throws InvalidArgumentException Si le $filterType est invalide.
     */
    public function getSelectOptions(int $clientId, ?string $filterType = null): array
    {
        $sql = "SELECT DISTINCT 
                    ga.nom AS association, 
                    gpt.nom AS poste_type, 
                    gz.nom AS zone_nom
                FROM grid_poste gp
                JOIN grid_association ga ON ga.id = gp.association_id
                JOIN grid_poste_type gpt ON gpt.id = gp.poste_type_id
                JOIN grid_zone gz ON gz.id = gp.zone_id
                WHERE gp.client_id = :client_id
                ORDER BY ga.nom, gpt.nom, gz.nom";

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':client_id', $clientId, PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $associations = [];
        $types        = [];
        $zones        = [];

        foreach ($results as $row) {
            if (!in_array($row['association'], $associations, true)) {
                $associations[] = $row['association'];
            }
            if (!in_array($row['poste_type'], $types, true)) {
                $types[] = $row['poste_type'];
            }
            if (!in_array($row['zone_nom'], $zones, true)) {
                $zones[] = $row['zone_nom'];
            }
        }

        if ($filterType === null) {
            return [
                'associations' => $associations,
                'types'        => $types,
                'zones'        => $zones,
            ];
        }

        switch ($filterType) {
            case 'associations':
                return $associations;
            case 'types':
                return $types;
            case 'zones':
                return $zones;
            default:
                throw new InvalidArgumentException("Le type d'option '$filterType' n'est pas valide.");
        }
    }
}
?>