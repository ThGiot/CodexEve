<?php
$query = "  SELECT role_id FROM module_permission_role
WHERE user_id = :user_id AND
module_id = :module_id AND
client_id = :client_id";
$stmt = $dbh->prepare($query);
// Exécuter la requête en liant les paramètres
$stmt->bindParam(':user_id', $_SESSION['user']['id']);
$stmt->bindParam(':module_id', $_SESSION['module_actif']);
$stmt->bindParam(':client_id', $_SESSION['client_actif']);
$stmt->execute();
$role= $stmt->fetchAll(PDO::FETCH_ASSOC);
$role = $role[0]['role_id'] ?? null;
?>