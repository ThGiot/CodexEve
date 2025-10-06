<?php
set_time_limit(30000); // 5 minutes par exemple

$clef = 'mklnzeijnhje7Y78392h984894za6515698zarzzefsfesfeszzéae';
$url = "https://medteam.be/migre.php";
require_once __DIR__ . '/../httpd.private/config.php';
require_once PRIVATE_PATH . '/sql.php';

session_start();

function viderTables($dbh) {
    // Désactiver les contraintes de clé étrangère
    $dbh->exec("SET FOREIGN_KEY_CHECKS = 0");

    $tables = ['facture', 'facture_detail', 'prestataire', 'prestation_ebrigade'];
    foreach ($tables as $table) {
        $dbh->exec("TRUNCATE TABLE moka_$table");
    }

    // Réactiver les contraintes de clé étrangère
    $dbh->exec("SET FOREIGN_KEY_CHECKS = 1");
}

function fetchAndInsert($url, $clef, $table, $dbh, $offset = 0, $limit = 500) { // Modifié à 500
    // Correspondance des noms de tables côté `medteam.be`
    $tableMap = [
        'prestataire' => 'user',
        'prestation_ebrigade' => 'prestation_hs'
    ];
    $sourceTable = isset($tableMap[$table]) ? $tableMap[$table] : $table;

    // Récupérer les données
    $donnees = ['clef' => $clef, 'table' => $sourceTable, 'offset' => $offset, 'limit' => $limit];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url . '?' . http_build_query($donnees));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        throw new Exception('Erreur cURL : ' . curl_error($ch));
    }
    curl_close($ch);
    $resultats = json_decode($response, true);

    if (!$resultats) {
        throw new Exception('Aucune donnée récupérée ou erreur de décodage JSON');
    }

    foreach ($resultats as $data) {
        if ($table == 'facture') {
            $query = "INSERT INTO moka_facture (id, designation, montant, date, numero, analytique, nom, prenom, niss, bce, compte, adresse, prestataire_id, client_id, protected) VALUES (:id, :designation, :montant, :date, :numero, :analytique, :nom, :prenom, :niss, :bce, :compte, :adresse, :prestataire_id, 3, 0)";
            $stmt = $dbh->prepare($query);
            $stmt->bindParam(':id', $data['id']);
            $stmt->bindParam(':designation', $data['designation']);
            $stmt->bindParam(':montant', $data['montant']);
            $stmt->bindParam(':date', $data['year']);
            $stmt->bindParam(':numero', $data['num']);
            $stmt->bindParam(':analytique', $data['analytique']);
            $stmt->bindParam(':nom', $data['nom']);
            $stmt->bindParam(':prenom', $data['prenom']);
            $stmt->bindParam(':niss', $data['NISS']);
            $stmt->bindParam(':bce', $data['bce']);
            $stmt->bindParam(':compte', $data['compte']);
            $stmt->bindParam(':adresse', $data['adresse']);
            $stmt->bindParam(':prestataire_id', $data['id_user']);
        } elseif ($table == 'facture_detail') {
            $query = "INSERT INTO moka_facture_detail (facture_id, designation, montant) VALUES (:facture_id, :designation, :montant)";
            $stmt = $dbh->prepare($query);
            $stmt->bindParam(':facture_id', $data['facture_id']);
            $stmt->bindParam(':designation', $data['designation']);
            $stmt->bindParam(':montant', $data['montant']);
        } elseif ($table == 'prestataire') {
            $query = "INSERT INTO moka_prestataire (client_id, id, nom, prenom, societe, niss, inami, adresse, telephone, email, compte, bce, p_id, grade, section) VALUES (3, :id, :nom, :prenom, :societe, :niss, :inami, :adresse, :telephone, :email, :compte, :bce, :p_id, :grade, :section)";
            $stmt = $dbh->prepare($query);
            $stmt->bindParam(':id', $data['id']);
            $stmt->bindParam(':nom', $data['nom']);
            $stmt->bindParam(':prenom', $data['prenom']);
            $stmt->bindParam(':societe', $data['societe']);
            $stmt->bindParam(':niss', $data['NISS']);
            $stmt->bindParam(':inami', $data['inami']);
            $stmt->bindParam(':adresse', $data['adresse']);
            $stmt->bindParam(':telephone', $data['gsm']);
            $stmt->bindParam(':email', $data['mail']);
            $stmt->bindParam(':compte', $data['compte']);
            $stmt->bindParam(':bce', $data['bce']);
            $stmt->bindParam(':p_id', $data['P_ID']);
            $stmt->bindParam(':grade', $data['P_GRADE']);
            $stmt->bindParam(':section', $data['section']);
        } elseif ($table == 'prestation_ebrigade') {
            $query = "INSERT INTO moka_prestation_ebrigade (client_id, E_CODE, P_ID, heure, commentaire, statut, E_LIBELLE, date, timestamp) VALUES (3, :E_CODE, :P_ID, :heure, :commentaire, :statut, :E_LIBELLE, :date, :timestamp)";
            $stmt = $dbh->prepare($query);
            if ($data['statut'] == 4) $data['statut'] = 3;
            $stmt->bindParam(':E_CODE', $data['E_CODE']);
            $stmt->bindParam(':P_ID', $data['P_ID']);
            $stmt->bindParam(':heure', $data['heure']);
            $stmt->bindParam(':commentaire', $data['commentaire']);
            $stmt->bindParam(':statut', $data['statut']);
            $stmt->bindParam(':E_LIBELLE', $data['E_LIBELLE']);
            $stmt->bindParam(':date', $data['date']);
            $stmt->bindParam(':timestamp', $data['timestamp']);
        }

        try {
            $stmt->execute();
        } catch (Exception $e) {
            echo "Erreur lors de l'insertion d'une ligne : " . $e->getMessage() . '</br>';
        }
    }

    return count($resultats);
}

function processData($dbh, $url, $clef, $table) {
    $offset = isset($_SESSION[$table . '_offset']) ? $_SESSION[$table . '_offset'] : 0;
    $limit = 500; // Modifié à 500

    try {
        $count = fetchAndInsert($url, $clef, $table, $dbh, $offset, $limit);
        if ($count === $limit) {
            $_SESSION[$table . '_offset'] = $offset + $limit;
            echo "<div>Étape suivante pour $table à l'offset $offset</div>";
            echo "<a id=\"nextStep\" href=\"?process=$table\">Étape suivante pour $table</a>";
        } else {
            echo "Importation terminée pour la table $table.<br>";
            unset($_SESSION[$table . '_offset']);
            $_SESSION['current_table_index']++;
            echo "<a id=\"nextStep\" href=\"?process=next\">Passer à la table suivante</a>";
        }
    } catch (Exception $e) {
        echo "Erreur lors de l'importation des données pour $table : " . $e->getMessage() . '<br>';
    }
}

if (isset($_GET['init'])) {
    viderTables($dbh);
    echo "Les tables ont été vidées.<br>";
    $_SESSION = [];
    $_SESSION['current_table_index'] = 0;
    echo "<a id=\"nextStep\" href=\"?process=next\">Commencer l'importation</a>";
}

$tables = ['facture', 'facture_detail', 'prestataire', 'prestation_ebrigade'];

if (isset($_SESSION['current_table_index'])) {
    $current_table_index = $_SESSION['current_table_index'];
    if ($current_table_index < count($tables)) {
        $table = $tables[$current_table_index];
        processData($dbh, $url, $clef, $table);
    } else {
        echo "Importation terminée pour toutes les tables.";
    }
} else {
    echo "<a id=\"nextStep\" href=\"?init\">Initialiser l'importation</a>";
}
?>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const nextStepLink = document.getElementById("nextStep");
        if (nextStepLink) {
            const consoleElement = document.createElement("div");
            consoleElement.textContent = "Étape actuelle : " + nextStepLink.href;
            document.body.insertBefore(consoleElement, document.body.firstChild);
            setTimeout(() => {
                nextStepLink.click();
            }, 500);
        }
    });
</script>
