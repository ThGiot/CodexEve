<?php
require __DIR__ . '/../sql.php';

if(!isset($_SESSION['user'])){   
    $auth = false;
    exit();
    
}
$query = "SELECT * FROM user_client WHERE 
                                                
                                                user_id = :user_id AND
                                                client_id = :client_id";
$stmt = $dbh->prepare($query);

// Exécuter la requête en liant les paramètres


$stmt->bindParam(':user_id', $_SESSION['user']['id']);
$stmt->bindParam(':client_id', $data['client_id']);
$stmt->execute();

$resultat = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($resultat)) {
    // Aucun résultat trouvé
    echo json_encode(['result' => 'false']);
}else{
    
    $module_liste =  $_SESSION['module_liste'] ;
   // On change le client actif
      $_SESSION['client_actif'] = $data['client_id'];
    //On choisis un module actif et on la passe en session
    $clientIdToSearch = $data['client_id'];  // changez cette valeur pour le client_id que vous recherchez
 
    $foundModuleId = null;
    
    $foundModules = [];
 
    foreach ($module_liste as $element) {
       if ($element['client_id'] == $clientIdToSearch) {
          $foundModules[] = $element;
       }
    }
 
    if (!empty($foundModules)) {
       
       $_SESSION['module_actif'] = $foundModules[0]['module_id'];
       $_SESSION['module_client_liste'] = $foundModules;
  
    } else {
       $_SESSION['module_actif'] = false;
       $_SESSION['module_client_liste'] = false;
    }
 
    echo json_encode(['result' => 'true']);
    
}

?>