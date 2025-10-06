<?php
$host = 'mysql'; // ou l'adresse de votre serveur de base de données
$username = 'root'; // votre nom d'utilisateur MySQL
$password = 'rootpassword'; // votre mot de passe MySQL
$database = '2023-10-05 V 1.1.3'; // le nom de votre base de données



// Créer une connexion à la base de données
$conn = new mysqli($host, $username, $password, $database);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Récupérer la liste des colonnes VARCHAR
$sql = "SELECT table_name, column_name, character_maximum_length 
        FROM information_schema.columns 
        WHERE table_schema = '$database' 
        AND data_type = 'varchar';";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Parcourir les résultats et générer les commandes ALTER TABLE
    while($row = $result->fetch_assoc()) {
        $alterSql = "ALTER TABLE `{$row['table_name']}` MODIFY `{$row['column_name']}` VARCHAR({$row['character_maximum_length']}) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;";
        if ($conn->query($alterSql) === TRUE) {
            echo "La colonne {$row['column_name']} de la table {$row['table_name']} a été convertie avec succès.\n";
        } else {
            echo "Erreur lors de la conversion de la colonne {$row['column_name']} de la table {$row['table_name']}: " . $conn->error . "\n";
        }
    }
} else {
    echo "Aucune colonne VARCHAR trouvée.\n";
}

// Fermer la connexion
$conn->close();
?>
