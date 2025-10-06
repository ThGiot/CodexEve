<?php

require_once PRIVATE_PATH . '/classes/Table.php'; 
require_once PRIVATE_PATH . '/classes/PageLayout.php'; 
require_once PRIVATE_PATH . '/classes/Form.php'; 
$table = new Table("Liste des Clients", ["ID", "Nom"], "clientListe");

try {
    $sql = "SELECT id, nom FROM client";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($clients as $client) {

        $table->addRow(
            ["ID" => $client['id'], "Nom" => $client['nom'] ],
            [
                ["name" => "View", "link" => "getContent(2,{client_id : '".$client['id']."'})", "class" => ""],
                ["name" => "Remove", "link" => "#!", "class" => "danger"],
            ]
        );
    }
} catch (PDOException $e) {
    echo "Erreur lors de la récupération des clients : " . $e->getMessage();
    exit;
}


$onsubmit = 'event.preventDefault(); node(\'admin_AddClient\', {formId : \'addClient\'})';

$form = new Form(   id: 'addClient', 
                    name: 'addClient',
                    method: 'POST', 
                    action: $onsubmit, 
                    title: 'Ajouter un client'
                );


$form->addField(    type: 'text', 
                    id: 'nomClient',
                    name: 'nomClient', 
                    label: 'Nom',
                    placeholder: 'Nom du client'
                );
$form->setSubmitButton(id: 'buttonSubmit',name: 'submit',value: 'send',text: 'Enregistrer');
$layout = new PageLayout();
$layout->addElement($table->render()); 
$layout->addElement($form->render(),4); 

echo $layout->render();
  ?>