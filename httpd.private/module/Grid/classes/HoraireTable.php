<?php
namespace Grid;

require_once PRIVATE_PATH . '/classes/Table.php';
use \Table; 

use PDO;

class HoraireTable {
    private Table $table; 

    public function __construct(array $horaires) {
        $this->table = new Table(
            title: "Liste des horaires",
            columns: ["Horaire", "Personnes"],
            id: "horaireListe"
        );

        foreach ($horaires as $horaire) {
            $periodeText = !empty($horaire['periode_id']) ? "Période " . $horaire['periode_id'] : "Aucune période";
            $this->table->addRow(
                [
                    "Horaire" => $horaire['nom'],
                    "Personnes" => $horaire['personne_nb'] ?? 'N/A',
                ],
                [
                    ["name" => "Edit", "link" => "getContent(301, {horaire_id: '".$horaire['id']."'})", "class" => ""],
                    ["name" => "Delete", "link" => "node('grid_horaire_dell', {horaireId: '".$horaire['id']."'})", "class" => "danger"],
                ]
            );
        }
    }

    public function render(): string {
        return $this->table->render();
    }
}

?>