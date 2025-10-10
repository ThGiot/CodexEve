<?php

class SelectHandler {
    private $conn;
    private $allowedTables = [
        'grid_association',
        'grid_association_dispo',
        'grid_horaire',
        'grid_poste',
        'grid_poste_type',
        'grid_zone',
        'grid_horaire_periode',
    ];

    private int $forcedClientId = 6;

    public function __construct(PDO $dbh) {
        $this->conn = $dbh;
    }

    public function getAll($table, $filters = [], $pagination = [], $order = []) {
        if (!in_array($table, $this->allowedTables)) {
            throw new Exception("Access to table '$table' is not allowed.");
        }

        $sql = "SELECT * FROM `$table`";
        $params = [];
        $where = [];

        // ⛔ Forcer client_id = 6
        $where[] = "`client_id` = :client_id";
        $params['client_id'] = $this->forcedClientId;

        // 🎯 Ajouter les autres filtres
        foreach ($filters as $key => $value) {
            if (!preg_match('/^[a-zA-Z0-9_]+$/', $key)) {
                throw new Exception("Invalid column name: $key");
            }
            $where[] = "`$key` = :$key";
            $params[$key] = $value;
        }

        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        // 🔃 Tri
        if (!empty($order['by']) && preg_match('/^[a-zA-Z0-9_]+$/', $order['by'])) {
            $direction = strtoupper($order['direction']) === 'DESC' ? 'DESC' : 'ASC';
            $sql .= " ORDER BY `{$order['by']}` $direction";
        }

        // 📐 Pagination
        if (!empty($pagination['limit']) && is_numeric($pagination['limit'])) {
            $sql .= " LIMIT :limit";
            $params['limit'] = (int) $pagination['limit'];

            if (!empty($pagination['offset']) && is_numeric($pagination['offset'])) {
                $sql .= " OFFSET :offset";
                $params['offset'] = (int) $pagination['offset'];
            }
        }

        $stmt = $this->conn->prepare($sql);

        foreach ($params as $key => $value) {
            $paramType = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindValue(":$key", $value, $paramType);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
