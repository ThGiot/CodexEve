<?php

/**
 * Étapes et description du code :
 *
 * 1. Inclusion de la classe de connexion
 * 2. Récupération des valeurs de $_POST['login'] et $_POST['password']
 * 3. Création d'une instance de la classe Connexion
 * 4. Appel de la méthode login() pour vérifier l'authentification
 * 5. Stockage du résultat dans $_SESSION['user'] après suppression du mot de passe
 * 6. Requête pour récupérer les clients de l'utilisateur
 * 7. Exécution de la requête et récupération des résultats
 * 8. Création des options pour la sélection des clients disponibles dans $_SESSION['client_select_option']
 * 9. Simplification de l'array pour avoir uniquement la liste des client_id
 * 10. Stockage des client_id accessibles dans $_SESSION['client_id']

 * 12. Pour chaque client_id, récupération de la liste des modules disponibles et des rôles associés
 * 13. Construction d'un tableau $module_result avec les informations des modules
 * 14. Ajout de $module_result à $module_liste
 * 15. Stockage de $module_liste dans $_SESSION['module_liste']
 */


require __DIR__ . '/../classes/Connexion.php';
// Récupérer les valeurs de $_POST['login'] et $_POST['password']


$login = $_POST['login'];
$password = $_POST['password'];


$connexion = new Connexion($dbh);

// Appeler la méthode login() pour vérifier l'authentification
$resultat = $connexion->login($login, $password);
unset($resultat['password']);
if($resultat == 100){
   $message = 'Aucun utilisateur trouvé avec ce login/mot de passe';
   $title = 'Erreur';
   $classe='warning';
  
}elseif($resultat == 101){
   $message = 'Le compte n\'est pas activé.';
   $message .= '<a href="resend_key.php?login='.$_POST['login'].'"> Renvoyer un lien</a>';
   $title = 'Erreur';
   $classe='warning';
  
}elseif($resultat != false) {
   $_SESSION['user'] = $resultat;

   $query = "  SELECT uc.client_id as client_id, c.nom as nom
   FROM user_client uc
   JOIN client c
   ON c.id = uc.client_id
   WHERE user_id = :user_id";
   $stmt = $dbh->prepare($query);
   // Exécuter la requête en liant les paramètres
   $stmt->bindParam(':user_id', $_SESSION['user']['id']);
   $stmt->execute();
   $client= $stmt->fetchAll(PDO::FETCH_ASSOC);
   //Simplification de l'array pour avoir uniquement la liste des client_id
   $client = array_map(function($client) {
   return $client['client_id'];
   }, $client);

   //stockage des client_id accessible
   $_SESSION['client_id'] = $client;
   $_SESSION['client_actif'] = $client[0];

   //Pour chaque client id, on récupère la liste des modules disponibles et le role 
   $module_liste = array();
   foreach ($client as $key => $client_id) {
     
      $query = "  SELECT mpc.module_id as module_id, m.nom as nom, mpr.role_id as role_id, m.logo as logo
                  FROM module_permission_client mpc
                  JOIN module m 
                  ON m.id = mpc.module_id
                  JOIN module_permission_role mpr
                  ON mpr.module_id = mpc.module_id AND mpr.client_id = :client_id_2
                  WHERE mpc.client_id = :client_id AND mpr.user_id = :user_id";

      $stmt = $dbh->prepare($query);
      // Exécuter la requête en liant les paramètres
      $stmt->bindParam(':client_id', $client_id);
      $stmt->bindParam(':client_id_2', $client_id);
      $stmt->bindParam(':user_id', $_SESSION['user']['id']);
    
      $stmt->execute();
      $module = $stmt->fetch(PDO::FETCH_ASSOC);
      if ($module !== false) {
         $module_result = [
            'module_id' => $module['module_id'],
            'client_id' => $client_id,
            'role_id' => $module['role_id'],
            'module_nom' => $module['nom'],
            'logo' => $module['logo']
         ];
         array_push($module_liste, $module_result);
       }

   }

   $_SESSION['module_liste'] = $module_liste;

   //On choisis un module actif et on la passe en session
   $clientIdToSearch = $_SESSION['client_actif'];  // changez cette valeur pour le client_id que vous recherchez

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
}



?>