<?php
require_once PRIVATE_PATH . '/classes/PageLayout.php'; 
require_once PRIVATE_PATH . '/classes/Form.php'; 
require_once dirname(__DIR__, 3) . '/classes/RequestHandler.php';


$requestHandler = new RequestHandler();

// Définir les règles de validation
$rules = [
    'prestataire_id' => ['type' => 'int']
];
// Gérer la requête avec authentification, assainissement, et validation
$data = $requestHandler->handleRequest($data, $rules);


try {
    $sql = "SELECT * FROM moka_prestataire WHERE id = :id AND client_id= :client_id";
    $stmt = $dbh->prepare($sql);
    $stmt ->bindParam(':id', $data['prestataire_id']);
    $stmt ->bindParam(':client_id', $_SESSION['client_actif']);
    $stmt->execute();
    $prestataire = $stmt->fetch(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    echo "Erreur lors de la récupération du prestataire : " . $e->getMessage();
    exit;
}

//Récupération des users Eve
$sql = "SELECT * FROM user ORDER BY nom, prenom";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
$optionsSelectUser=[];

//Ajout d'un élément null

$optionsSelectUser[] = [
    'value' => 'null', 
    'text' => 'Aucune liaison'
];

foreach ($users as $user) {
    $optionsSelectUser[] = [
        'value' => $user['id'], 
        'text' => $user['nom'].' '.$user['prenom'].' - id: '.$user['id']
    ];
}

$onsubmit = 'event.preventDefault(); node(\'moka_prestataire_maj_save\', {formId : \'formPrestataire\', prestataire_id :\''.$prestataire['id'].'\'})';

$form = new Form(   id: 'formPrestataire', 
                    name: 'formPrestataire',
                    method: 'POST', 
                    action: $onsubmit, 
                    title: 'Fiche Prestataire'
                );

$form->addField(
                    type: 'select', 
                    id: 'user_id',
                    name: 'user_id', 
                    label: 'Liaison User Eve',
                    options: $optionsSelectUser,
                    selectedValue: $prestataire['user_id'],
                    group: 'identification_app',                 
                    );
$form->addField(    type: 'text', 
                    id: 'nom',
                    name: 'nom', 
                    label: 'Nom',
                    value: $prestataire['nom'],
                    group: 'identification'
                );
$form->addField(    type: 'text', 
                    id: 'p_id',
                    name: 'p_id', 
                    label: 'p_id',
                    value: $prestataire['p_id'],
                    group: 'identification_app',
                    options: ['disabled' => 'disabled']
                );
$form->addField(    type: 'text', 
                    id: 'inami',
                    name: 'inami', 
                    label: 'inami',
                    value: $prestataire['inami'],
                    group: 'identification',
                    options: ['disabled' => 'disabled']
                );
$form->addField(    type: 'text', 
                    id: 'prenom',
                    name: 'prenom', 
                    label: 'Prénom',
                    value: $prestataire['prenom'],
                    group: 'identification'
                );
$form->addField(    type: 'text', 
                    id: 'telephone',
                    name: 'telephone', 
                    label: 'telephone',
                    value: $prestataire['telephone'],
                    group:'coordonnee'
                );
$form->addField(    type: 'text', 
                    id: 'email',
                    name: 'email', 
                    label: 'Email',
                    value: $prestataire['email'],
                    group:'coordonnee'
                );
$form->addField(    type: 'text', 
                    id: 'adresse',
                    name: 'adresse', 
                    label: 'adresse',
                    value: $prestataire['adresse'],
                    group: 'fact'
            );
$form->addField(    type: 'text', 
                    id: 'niss',
                    name: 'niss', 
                    label: 'niss',
                    value: $prestataire['niss'],
                    group:'fact'
            );
$form->addField(    type: 'text', 
                    id: 'bce',
                    name: 'bce', 
                    label: 'BCE',
                    value: $prestataire['bce'],
                    group:'fact'
            );
$form->addField(    type: 'text', 
                    id: 'societe',
                    name: 'societe', 
                    label: 'Société',
                    value: $prestataire['societe'],
                    group:'fact'
            );
$form->addField(    type: 'text', 
                    id: 'compte',
                    name: 'compte', 
                    label: 'compte',
                    value: $prestataire['compte'],
                    group:'fact'
            );

$form->setSubmitButton(id: 'buttonSubmit',name: 'submit',value: 'send',text: 'Enregistrer');
$layout = new PageLayout();
$layout->addElement($form->render()); 
echo $layout->render();

?>