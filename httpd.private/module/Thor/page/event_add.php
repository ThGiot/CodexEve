<?php
require_once PRIVATE_PATH . '/classes/PageLayout.php'; 
require_once PRIVATE_PATH . '/classes/Form.php'; 




$onsubmit = 'event.preventDefault(); node(\'thor_evenement_add\', {formId : \'formEvenement\'})';

$form = new Form(   id: 'formEvenement', 
                    name: 'formEvenement',
                    method: 'POST', 
                    action: $onsubmit, 
                    title: 'Ajout Evenement'
                );
$form->addField(    type: 'text', 
                    id: 'nom',
                    name: 'nom', 
                    label: 'Nom',
                    placeholder: 'nom',
                    group: 'identification'
                );
$form->addField(    type: 'date', 
                    id: 'date',
                    name: 'date', 
                    label: 'Date',
                    group: 'date'
                );


$form->setSubmitButton(id: 'buttonSubmit',name: 'submit',value: 'send',text: 'Enregistrer');
$layout = new PageLayout();
$layout->addElement($form->render()); 
echo $layout->render();

?>
