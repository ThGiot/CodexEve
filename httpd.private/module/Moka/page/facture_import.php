<?php

require_once PRIVATE_PATH . '/classes/PageLayout.php'; 
require_once PRIVATE_PATH . '/classes/Form.php'; 


$onsubmit = 'event.preventDefault(); node(\'moka_import_ebrigade\', {formId : \'importEbrigade\'})';

$form = new Form(   id: 'importEbrigade', 
                    name: 'importEbrigade',
                    method: 'POST', 
                    action: $onsubmit, 
                    title: 'Import de facture API - Ebrigade'
                );


$form->addField(    type: 'text', 
                    id: 'designation',
                    name: 'designation', 
                    label: 'Désignation',
                    placeholder: 'Désignation'
                );
$form->addField(    type: 'date', 
                    id: 'dateStart',
                    name: 'dateStart', 
                    label: 'Date de départ',
                    placeholder: '2023-01-01'
                );
$form->addField(    type: 'date', 
                    id: 'dateFin',
                    name: 'dateFin', 
                    label: 'Date de Fin',
                    placeholder: '2023-01-01'
                );
$form->setSubmitButton(id: 'buttonSubmit',name: 'submit',value: 'send',text: 'Enregistrer');
$layout = new PageLayout();
$layout->addElement($form->render(),6); 
echo $layout->render();

?>