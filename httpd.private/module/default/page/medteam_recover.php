<?php
require_once PRIVATE_PATH . '/classes/PageLayout.php'; 
require_once PRIVATE_PATH . '/classes/Form.php'; 
require_once PRIVATE_PATH . '/classes/BootstrapCard.php'; 
require_once PRIVATE_PATH . '/classes/Modal.php'; 


$modal = new Modal(
    id: "mokaRecover", 
    title: "Récupération Moka", 
    body: '<p id="contentModal"></p>',
    headerClass: "",
    okayButtonClass: "",
    okayButtonText : "Supprimer",
    cancelButtonClass: "outline-secondary",
    showOkayButton: false,
    showButton : false
);
echo $modal->render();


$layout = new PageLayout();
$card = new BootstrapCard();
$card->setHeader("<b>Récuperer son compte Moka</b>");
$content ="Pour accèder à votre compte Moka merci de renseigner le code inviation qui vous à été communiqué.";
$card->setBody($content);

$onsubmit = 'event.preventDefault(); node(\'default_recover_moka\', {formId : \'formPrestataire\'})';

$form = new Form(   id: 'formPrestataire', 
                    name: 'formPrestataire',
                    method: 'POST', 
                    action: $onsubmit, 
                    title: 'Code Invitation'
                );
$form->addField(    type: 'text', 
                    id: 'code',
                    name: 'code', 
                    label: 'code',
                    placeholder: 'Code Invitation'
                );


$form->setSubmitButton(id: 'buttonSubmit',name: 'submit',value: 'send',text: 'Enregistrer');
$layout = new PageLayout();
$torender = $card->render().' '.$form->render();
$layout->addElement($torender,8); 

echo $layout->render();
?>