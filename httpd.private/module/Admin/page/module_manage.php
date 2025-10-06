<?php

require_once PRIVATE_PATH . '/classes/HtmlCard.php'; 
require_once PRIVATE_PATH . '/classes/Table.php'; 
require_once PRIVATE_PATH . '/classes/PageLayout.php'; 
require_once PRIVATE_PATH . '/classes/Toolbar.php'; 
require_once PRIVATE_PATH . '/classes/ClearInput.php'; 
require_once PRIVATE_PATH . '/classes/Form.php'; 


// ----------------------------------------------------------------
//Recupération des informations concernant le module
//----------------------------------------------------------------

$client_id = ClearInput::prepare($data['client_id']);
$module_id = ClearInput::prepare($data['module_id']);
try {
    // Définir la requête SQL
    $sql = "SELECT * FROM module WHERE id = :module_id";
  $stmt = $dbh->prepare($sql);
  $stmt->bindParam(':module_id', $module_id);
  $stmt->execute();
  $module = $stmt->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
  echo "Erreur lors de la récupération des informations du module : " . $e->getMessage();
  exit;
}

// ----------------------------------------------------------------
// Recupération des infomation concernant les modules
// ----------------------------------------------------------------



//----------------------------------------------------------------
// Récupération des informations concernant les utilisateurs du client
//----------------------------------------------------------------

try {
    // Définir la requête SQL
        $sql = "    SELECT u.id AS user_id,
            u.nom,
            u.prenom,
            u.email,
            u.telephone,
            IF(r.nom IS NULL, 'Aucun', r.nom) AS role
        FROM user_client uc
        JOIN user u ON uc.user_id = u.id
        LEFT JOIN module_permission_role mpr ON uc.user_id = mpr.user_id AND uc.client_id = mpr.client_id AND mpr.module_id = :module_id
        LEFT JOIN role r ON mpr.role_id = r.id
        WHERE uc.client_id = :client_id

";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':client_id', $client_id);
    $stmt->bindParam(':module_id', $module_id);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $user_module = $stmt->rowCount();
} catch (PDOException $e) {
  echo "Erreur lors de la récupération des informations des utilisateurs du client : " . $e->getMessage();
}

//----------------------------------------------------------------
// Récupération des paramètres du modules
//----------------------------------------------------------------

try {
    // Définir la requête SQL
        $sql = "SELECT mp.id AS id, mp.param_name AS param_name, mp.param_type as param_type, mcvp.param_value as param_value
                FROM module_params mp
                JOIN module_client_param_values mcvp ON mp.id = mcvp.param_id
                WHERE mp.module_id = :module_id AND
                mcvp.client_id = :client_id

";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':client_id', $client_id);
    $stmt->bindParam(':module_id', $module_id);
    $stmt->execute();
    $params = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
  echo "Erreur lors de la récupération des informations des utilisateurs du client : " . $e->getMessage();
}


// Créez l'objet PageLayout
$layout = new PageLayout();

$card = new HtmlCard();
$clientCard = $card->createClientCard($module['nom'], '', '', $user_module, $module['logo'],"node('imageChange',{'id':'".$module_id."', 'categorie' : 'module'})");
$layout->addElement($clientCard, 4); // Colonne de taille 4

// Créez et ajoutez la table modules
$table = new Table("Utilisateurs & Permissions", ["ID", "Nom", "Prénom", "Email", "Téléphone", "Role"],'userTable', true);


foreach($users as $user){

    
        
       $action_manage = ["name" => "Changer le role", "link" => "node('getSelectRole', { clientId : $client_id, userId: '".$user['user_id']."', moduleId: '".$module['id']."' });"];
        $action_switch = ["name" => "Remove", "link" => "javascript:void(0);", "class" => "danger"];
        $actions = [$action_manage , $action_switch];
    
    $row = [
        "ID" => $user['user_id'],
        "Nom" => $user['nom'],
        "Prénom" => $user['prenom'],
        "Email" => $user['email'],
        "Téléphone" => $user['telephone'],
        "Role" => $user['role']
        
    ];

    $tdAttributes = [
        "Role" => [
            'id' => 'role_' . $user['user_id']
        ],
        // autres attributs pour d'autres colonnes
    ];
    
    $table->addRow($row, $actions,  $tdAttributes);
}

$layout->addElement($table->render(), 8); // Colonne de taille 8


$table_param = new Table("Paramètre module", ["ID2", "PARAM", "VALUE"],'paramTable', true);


foreach($params as $param){

    
        
        $action_manage = ["name" => "Sauver", "link" => "node('saveParam', { clientId : $client_id, paramId: '".$param['id']."', moduleId: '".$module['id']."' });"];
       
        $actions = [$action_manage ];
    
    $row = [
        "ID" => $param['id'],
        "PARAM" => $param['param_name'],
        "VALUE" => '<input type="text" class="form-control" value="'.$param['param_value'].'" name="param_'.$param['id'].'" id="param_'.$param['id'].'">'     
    ];

    $tdAttributes = [
        "Role" => [
            'id' => 'param_' . $param['id']
        ],
        // autres attributs pour d'autres colonnes
    ];
    
    $table_param->addRow($row, $actions,  $tdAttributes);
}



// Affichez la sortie
echo $layout->render();
$layout = new PageLayout();
//$layout->addElement($table_param->render());
echo $layout->render(narrow: true);
?>

