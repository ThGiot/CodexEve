<?php
require_once PRIVATE_PATH . '/classes/PageLayout.php'; 
require_once PRIVATE_PATH . '/classes/Form.php'; 
require_once dirname(__DIR__, 3) . '/classes/RequestHandler.php';
require_once dirname(__DIR__, 1) . '/classes/PosteService.php';


$requestHandler = new RequestHandler();
$posteService = new PosteService($dbh);

// Définir les règles de validation
$rules = [
    'association_id' => ['type' => 'int']
];
// Gérer la requête avec authentification, assainissement, et validation
$data = $requestHandler->handleRequest($data, $rules);
$association = $posteService->getAssociation($_SESSION['client_actif'], $data['association_id']);



$onsubmit = 'event.preventDefault(); node(\'grid_association_maj\', {formId : \'formAssociation\', association_id :\''.$association['id'].'\'})';

$form = new Form(   id: 'formAssociation', 
                    name: 'formAssociation',
                    method: 'POST', 
                    action: $onsubmit, 
                    title: 'Fiche Association'
                );

$form->addField(    type: 'text', 
                    id: 'nom',
                    name: 'nom', 
                    label: 'Nom',
                    value: $association['nom'],
                    group:'nom'
                );
$form->addField(    type: 'text', 
                    id: 'adresse',
                    name: 'adresse', 
                    label: 'Adresse',
                    value: $association['adresse'],
                    group:'adresse'
                );
$form->addField(    type: 'text', 
                    id: 'contact_nom',
                    name: 'contact_nom', 
                    label: 'Personne de contacte',
                    value: $association['contact_nom'],
                    group:'contact'
                );
$form->addField(    type: 'text', 
                    id: 'contact_email',
                    name: 'contact_email', 
                    label: 'email',
                    value: $association['contact_email'],
                    group:'contact'
                );                
$form->addField(    type: 'text', 
                    id: 'contact_telephone',
                    name: 'contact_telephone', 
                    label: 'Téléphone',
                    value: $association['contact_telephone'],
                    group:'contact'
                );          
$form->setSubmitButton(id: 'buttonSubmit',name: 'submit',value: 'send',text: 'Enregistrer');
$layout = new PageLayout();
$layout->addElement($form->render()); 
echo $layout->render();

?>