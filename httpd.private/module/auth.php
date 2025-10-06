<?php

/*
Ce script vérifie les autorisations d'accès d'un utilisateur en fonction de son identifiant de module, 
son identifiant d'utilisateur et son identifiant de client. 
Si les autorisations sont valides, la variable $auth est définie sur true ; sinon, elle est définie sur false.

Le script effectue les étapes suivantes :
1. Vérifie si la session de l'utilisateur est définie. Si elle ne l'est pas, le script se termine prématurément.
2. Exécute une requête SQL pour sélectionner les lignes de la table `module_permission_role` correspondant aux valeurs spécifiées.
3. Récupère les résultats de la requête et les stocke dans la variable $resultat.
4. Vérifie si la variable $resultat est vide. Si c'est le cas, cela signifie qu'aucune autorisation correspondante n'a été trouvée, et $auth est définie sur false.
5. Si $resultat n'est pas vide, cela signifie qu'au moins une autorisation correspondante a été trouvée, et $auth est définie sur true.
*/



if(!isset($_SESSION['user'])){   
    $auth = false;
    exit('aucune session user obtenue AUTH FAILED');
    
}
$query = "SELECT * FROM module_permission_role WHERE 
                                                module_id = :module_id AND
                                                user_id = :user_id AND
                                                client_id = :client_id";
$stmt = $dbh->prepare($query);

// Exécuter la requête en liant les paramètres

$stmt->bindParam(':module_id', $module_id);
$stmt->bindParam(':user_id', $_SESSION['user']['id']);
$stmt->bindParam(':client_id', $_SESSION['client_actif']);
$stmt->execute();


$resultat= $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($resultat)) {
    $auth = false;
}else{
    $auth = true;
    
    
}

?>