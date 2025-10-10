<?php
require_once PRIVATE_PATH . '/vendor/autoload.php';
use Grid\PosteService;
use Grid\HoraireService;
use App\Database;

$dbh = Database::getConnection();
$posteService = new PosteService($dbh);
$clientId = (int) $_SESSION['client_actif'];

$service = new HoraireService($dbh);
$tableau = $service->getPostesParHoraire($clientId, $dbh);

// Dates à traiter
$jours = [
    'jeudi' => ['start' => '2025-06-26 12:00:00', 'end' => '2025-06-26 14:00:00'],
    'vendredi' => ['start' => '2025-06-27 12:00:00', 'end' => '2025-06-27 14:00:00'],
    'samedi' => ['start' => '2025-06-28 12:00:00', 'end' => '2025-06-28 14:00:00'],
    'dimanche' => ['start' => '2025-06-29 12:00:00', 'end' => '2025-06-29 14:00:00'],
];

$totaux = [
    'jeudi' => 0,
    'vendredi' => 0,
    'samedi' => 0,
    'dimanche' => 0,
];

// Parcours des jours
foreach ($jours as $jour => $plage) {
    foreach ($tableau as $horaire) {
        $nb_personne = HoraireService::getNombreParPeriode(
            $clientId,
            $horaire['horaire_id'],
            $plage['start'],
            $plage['end'],
            $dbh
        );
        $totaux[$jour] += $nb_personne * $horaire['nb_postes'];
    }
}

// Affichage
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Jour</th><th>Total Sandwichs</th></tr>";
foreach ($totaux as $jour => $total) {
    echo "<tr><td>" . ucfirst($jour) . "</td><td>$total</td></tr>";
}
echo "</table>";
?>
