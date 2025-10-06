<?php

require_once PRIVATE_PATH . '/classes/HtmlCard.php'; 
require_once PRIVATE_PATH . '/classes/Table.php'; 
require_once PRIVATE_PATH . '/classes/PageLayout.php'; 
require_once PRIVATE_PATH . '/classes/Toolbar.php'; 
require_once PRIVATE_PATH . '/classes/ClearInput.php'; 
require_once PRIVATE_PATH . '/classes/Form.php'; 


// ----------------------------------------------------------------
//Recupération des informations concernant le client
//----------------------------------------------------------------

$client_id = ClearInput::prepare($data['client_id']);
try {
    // Définir la requête SQL
    $sql = "SELECT 
            client.*,
            client_proprietaire.*,
            client.nom AS client_nom, 
            client_proprietaire.nom AS proprietaire_nom
            FROM 
            client 
            JOIN 
            client_proprietaire 
            ON 
            client.id = client_proprietaire.client_id 
            WHERE 
            client.id = :client_id;
            ";
  $stmt = $dbh->prepare($sql);
  $stmt->bindParam(':client_id', $client_id);
  $stmt->execute();
  $client = $stmt->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
  echo "Erreur lors de la récupération des informations du client : " . $e->getMessage();
  exit;
}

// ----------------------------------------------------------------
// Recupération des infomation concernant les modules
// ----------------------------------------------------------------


try {
    // Définir la requête SQL
    $sql = "SELECT m.id,
            m.nom,
           
            CASE
                WHEN p.module_id IS NOT NULL THEN TRUE
                ELSE FALSE
            END AS actif
        FROM module m
        LEFT JOIN module_permission_client p ON m.id = p.module_id AND p.client_id = :client_id";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':client_id', $client_id);
    $stmt->execute();
    $modules = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $module_nombre_actif = $stmt->rowCount();
} catch (PDOException $e) {
  echo "Erreur lors de la récupération des informations module : " . $e->getMessage();
  exit;
}



//----------------------------------------------------------------
// Récupération des informations concernant les utilisateurs du client
//----------------------------------------------------------------

try {
    // Définir la requête SQL
    $sql = "
    SELECT u.id ,
           u.nom,
           u.prenom,
           u.email,
           u.telephone,
           IF(uc.client_id IS NULL, 'false', 'true') AS actif
    FROM user u
    LEFT JOIN user_client uc ON u.id = uc.user_id AND uc.client_id = :client_id
";

    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':client_id', $client_id);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $user_client = $stmt->rowCount();
} catch (PDOException $e) {
  echo "Erreur lors de la récupération des informations des utilisateurs du client in client_vieuw : " . $e->getMessage();
  exit;
}

// Créez l'objet PageLayout
$layout = new PageLayout();

// Ajoutez la barre d'outils (supposant que la classe Toolbar existe et est capable de générer le HTML nécessaire)
/* $toolbar = new Toolbar();
$toolbar->addButton('btn-phoenix-danger', '', 'fa-solid fa-trash-can', 'Supprimer le client');
$toolbar->addButton('btn-phoenix-secondary', "getContent(2,{client_id : '0'})", 'fas fa-history', 'Recharger la page');
$layout->addElement($toolbar->render());
*/
// Créez et ajoutez les cartes
$card = new HtmlCard();
if(!isset($client['client_nom'])) $client['client_nom'] ='';
if(!isset($client['adresse'])) $client['adresse'] ='';
if(!isset($client['note'])) $client['note'] ='';
if(!isset($client['email'])) $client['email'] ='';
if(!isset($client['telephone'])) $client['telephone'] ='';
if(!isset($client['logo'])) $client['logo'] ='';
$clientCard = $card->createClientCard($client['client_nom'], $client['note'], $module_nombre_actif, $user_client, $client['logo'],"node('imageChange',{'id':'".$client_id."', 'categorie' : 'client'})");
$addressCard = $card->createAddressCard($client['adresse'], $client['email'], $client['telephone']);
$layout->addElement($clientCard . $addressCard, 4); // Colonne de taille 4

// Créez et ajoutez la table modules
$table = new Table(title : "Module Actif", columns: ["ID", "Nom", "Status"], id:'moduleTable', grey: true);


foreach($modules as $module){

    if($module['actif'] == 0){
        $statut = "<span id='moduleStatut_".$module["id"]."' class='badge badge-phoenix fs--2 badge-phoenix-danger'><span class='badge-label'>Désactivé</span></span>";
        $action_manage = ["name" => "Manage", "link" => "getContent(21,{client_id : '".$client_id."',module_id : '".$module['id']."'})"];
        $action_switch = ["id" => "moduleSwitch_".$module["id"],"name" => "Activer", "link" => "node('clientModuleStatut', {clientId: '".$client_id."', moduleId :'".$module['id']."', statut: 'add'})", "class" => "success"];
        $actions = [$action_manage , $action_switch];
       
    }else{
        $statut = "<span id='moduleStatut_".$module["id"]."' class='badge badge-phoenix fs--2 badge-phoenix-success'><span class='badge-label'>Activé</span></span>";
        $action_manage = ["name" => "Manage", "link" => "getContent(21,{client_id : '".$client_id."',module_id : '".$module['id']."'})"];
        $action_switch = ["id" => "moduleSwitch_".$module["id"], "name" => "Remove", "link" => "node('clientModuleStatut', {clientId: '".$client_id."', moduleId :'".$module['id']."', statut: 'remove'})", "class" => "danger"];
        $actions = [$action_manage , $action_switch];
    }
    $row = [
        "ID" => $module['id'],
        "Nom" => $module['nom'],
        "Status" => $statut
        
    ];
    
    $table->addRow($row, $actions);
}

$layout->addElement($table->render(), 8); // Colonne de taille 8

// Créez et ajoutez la table clients
$table = new Table("Utilisateurs", ["ID", "Nom","Prénom", "Email", "Téléphone", "Statut"],'userTable', true);


foreach($users as $user){
    if($user['actif'] == 'false'){
        $statut = "<span id='statut_".$user["id"]."' class='badge badge-phoenix fs--2 badge-phoenix-danger'><span class='badge-label'>Désactivé</span></span>";
        $action_manage = ["name" => "Manage", "link" => "getContent(21,{client_id : '".$client_id."',user_id : '".$user['id']."'})"];
        $action_switch = ["id" => "switch_".$user["id"], "name" => "Activer", "link" => "node('clientUserStatut', {clientId: '".$client_id."', userId :'".$user['id']."', statut: 'add'})", "class" => "success"];
        $actions = [$action_manage , $action_switch];
       
    }else{
        $statut = "<span id='statut_".$user["id"]."'  class='badge badge-phoenix fs--2 badge-phoenix-success'><span class='badge-label'>Activé</span></span>";
        $action_manage = ["name" => "Manage", "link" => "getContent(21,{client_id : '".$client_id."',user_id : '".$user['id']."'})"];
        $action_switch = ["id" => "switch_".$user["id"], "name" => "Remove", "link" => "node('clientUserStatut', {clientId: '".$client_id."', userId :'".$user['id']."', statut: 'remove'})", "class" => "danger"];
        $actions = [$action_manage , $action_switch];
    }
    
    $row = [
        "ID" => $user['id'],
        "Nom" => $user['nom'],
        "Prénom" => $user['prenom'],
        "Email" => $user['email'],
        "Téléphone" => $user['telephone'],
        "Statut" => $statut
        
    ];
    $actions = [$action_manage , $action_switch];
    $table->addRow($row, $actions);
}

$layout->addElement($table->render()); 


// Affichez la sortie
echo $layout->render(narrow: true);


?>

