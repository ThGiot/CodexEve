<?php
require_once __DIR__ . '/../httpd.private/config.php';
require_once PRIVATE_PATH . '/sql.php';
require_once PRIVATE_PATH . '/classes/Modal.php';

// Assainir et vérifier les variables GET
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
$key = isset($_GET['key']) ? htmlspecialchars(trim($_GET['key'])) : '';

// Vérifier si les deux paramètres sont présents
if (empty($user_id) || empty($key)) {
    $title ="Erreur";
    $classe ="warning";
    $message = "Paramètres invalides.";
    require 'login.php';
    exit();
}

try {
    // Vérifier si une clé correspondante existe dans la table user_recover
    $query = "SELECT * FROM user_recover WHERE user_id = :user_id AND `key` = :key";
    $stmt = $dbh->prepare($query);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':key', $key, PDO::PARAM_STR);
    $stmt->execute();

    // Si aucune correspondance n'est trouvée
    if ($stmt->rowCount() == 0) {
        $title ="Erreur";
        $classe ="warning";
        $message = "La clé de récupération ne correspond pas.";
        require 'login.php';
        exit();
    }

    // Si une correspondance est trouvée, inclure la page de réinitialisation du mot de passe
    require 'reset_password.php';

} catch (PDOException $e) {
    $title ="Erreur";
    $classe ="warning";
    $message = "Erreur lors de la vérification de la clé de récupération.";
    require 'login.php';
    exit();
}
?>
