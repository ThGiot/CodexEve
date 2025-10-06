
<?php
if(!isset($_SESSION['user'])){   
    $auth = false;
    exit('Get NavItem FAILED. IN get_navitem.php | NO USER IN SESSION');
    
}
require __DIR__ . '/../sql.php';
$navData = [];
$query = "  SELECT m.nom as nom, m.logo as logo, m.id as id 
            FROM module_permission_client mpc
            JOIN module m
            ON  m.id = mpc.module_id
            WHERE client_id = :client_id";
$stmt = $dbh->prepare($query);

// Exécuter la requête en liant les paramètres
$stmt->bindParam(':client_id', $_SESSION['client_actif']);
$stmt->execute();
$resultat= $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($resultat as $module){
    
    if(LOCAL == true)$module['logo'] = PUBLIC_PATH . '' . $module['logo'];
    array_push($navData , [
        "icon" => $module['logo'],
        "title" => $module['nom'],
        "link" => $module['id']
    ]);
}


  $navData = json_encode($navData);
  print_r($navData);
?>