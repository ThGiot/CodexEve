<?php 
//----------------------------------------------------------------
// Inclusion des dépendances 
//----------------------------------------------------------------

require_once PRIVATE_PATH . '/classes/Table.php'; 
require_once PRIVATE_PATH . '/classes/Form.php'; 
require_once PRIVATE_PATH . '/classes/HtmlCard.php'; 
require_once PRIVATE_PATH . '/classes/PageLayout.php'; 

//----------------------------------------------------------------
// Carte Photo & Reset pswd
//----------------------------------------------------------------

$layout = new PageLayout();
$htmlCard = new HtmlCard();
$htmlCard = $htmlCard->createUserProfileCard($_SESSION['user']['avatar'], $_SESSION['user']['nom'], $_SESSION['user']['prenom'],'node(\'userSelfModifAvatar\', {elementId : \'avatarFile\'})');
$layout->addElement($htmlCard);

//----------------------------------------------------------------
// Formulaire info personnelles
//----------------------------------------------------------------

$onsubmit = 'event.preventDefault(); node(\'userSelfModif\', {formId : \'user_self_modif\'})';
$form = new Form('user_self_modif', 'user_self_modif', 'POST', $onsubmit, 'Informations personnelles');
$form->addField('text', 'nom', 'nom','Nom', $_SESSION['user']['nom'], '', [
    'disabled' => 'disabled',
],'group1');
$form->addField('text', 'prenom', 'prenom','Prénom', $_SESSION['user']['prenom'], '', [
  'disabled' => 'disabled',
],'group1');
$form->addField('text', 'login', 'login','Login', $_SESSION['user']['login'], '', [
  'disabled' => 'disabled',
],'group1');

$form->addField('text', 'email', 'email','Email', $_SESSION['user']['email'], '', [],'group2');
$form->addField('text', 'telephone', 'telephone','Téléphone', $_SESSION['user']['telephone'], '', [],'group2');
$form->setSubmitButton('buttonSubmit', 'submit', 'send', 'Enregistrer');
$layout->addElement($form->render());

//-----------------------------------------------------------------
//Récupération liste Client / Module
//-----------------------------------------------------------------
try {
  // Définir la requête SQL
    $sql = "SELECT mpr.id as id, module.nom AS module, client.nom AS client, role.nom AS role 
            FROM module_permission_role mpr
            JOIN module ON module.id = mpr.module_id
            JOIN client ON client.id = mpr.client_id
            JOIN role ON role.id = mpr.role_id
            WHERE mpr.user_id = :user_id
            ORDER BY mpr.client_id";

  $stmt = $dbh->prepare($sql);
  $stmt->bindParam(':user_id', $_SESSION['user']['id']);
  $stmt->execute();
  $modules = $stmt->fetchAll(PDO::FETCH_ASSOC);
  $user_module = $stmt->rowCount();
} catch (PDOException $e) {
  echo "Erreur lors de la récupération des informations des client de l'utilisateur : " . $e->getMessage();
}

// --------------------------------------------------------------------
// Création du tableau 
// --------------------------------------------------------------------
$table = new Table(title: "Accès",
                  columns: ["Client", 
                            "Module",
                            "Role"
                          ], 
                  id:"clientModuleListe",);

foreach($modules as $module){

      
      $row = [
          "Client" => $module['client'],
          "Module" => $module['module'],
          "Role" => $module['role'],
          [

        ],
            ];
            $tdAttributes = [
            "Role" => [
                'id' => 'mpr_' . $module['id']
            ],
            // autres attributs pour d'autres colonnes
      ];
  $table->addRow($row, [],  $tdAttributes);

  
}

//----------------------------------------------------------------
// Render Layout
//----------------------------------------------------------------

$layout->addElement($table->render());
echo $layout->render();
?>
