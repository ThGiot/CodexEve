<?php
require_once __DIR__ . '/../httpd.private/config.php';
require_once PRIVATE_PATH . '/sql.php';
require_once PRIVATE_PATH . '/classes/Modal.php';

// Assainir et vérifier les variables GET
$user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
$key = isset($_POST['key']) ? htmlspecialchars(trim($_POST['key'])) : '';

// Vérifier si les deux paramètres sont présents
if (empty($user_id) || empty($key)) {
    $message = "Paramètres invalides.";
    $title ="Erreur";
    $classe ="warning";
    require 'login.php';
    exit();
}

// Vérifier si le formulaire a été soumis et vérifier les mots de passe
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $confirm_password = isset($_POST['confirm_password']) ? trim($_POST['confirm_password']) : '';

    if (empty($password) || empty($confirm_password)) {
        $title ="Erreur";
        $classe ="warning";
        $message = "Tous les champs doivent être remplis.";
        require 'reset_password.php';
        exit();
    }

    if ($password !== $confirm_password) {
        $title ="Erreur";
    $classe ="warning";
        $message = "Les mots de passe ne correspondent pas.";
        require 'reset_password.php';
        exit();
    }

    // Vérifier si la clé est valide
    try {
        $query = "SELECT * FROM user_recover WHERE user_id = :user_id AND `key` = :key";
        $stmt = $dbh->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':key', $key, PDO::PARAM_STR);
        $stmt->execute();

        // Si aucune correspondance n'est trouvée
        if ($stmt->rowCount() == 0) {
            $title ="Erreur";
            $classe ="warning";
            $message = "La clé de récupération ne correspond pas ou a expiré.";
            require 'login.php';
            exit();
        }

        // Clé valide, mise à jour du mot de passe de l'utilisateur
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $query = "UPDATE user SET password = :password WHERE id = :user_id";
        $stmt = $dbh->prepare($query);
        $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        // Supprimer l'entrée de récupération pour éviter la réutilisation
        $query = "DELETE FROM user_recover WHERE user_id = :user_id";
        $stmt = $dbh->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        $message = "Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter.";
        require 'login.php';
        exit();
    } catch (PDOException $e) {
        $message = "Erreur lors de la réinitialisation du mot de passe.";
        $title ="Erreur";
        $classe ="warning";
        require 'reset_password.php';
        exit();
    }
} else {
    // Si le formulaire n'a pas été soumis, rediriger vers la page de réinitialisation du mot de passe
    require 'reset_password.php';
    exit();
}
?>
