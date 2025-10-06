<?php
require_once PRIVATE_PATH . '/classes/PageLayout.php'; 
require_once PRIVATE_PATH . '/classes/Form.php'; 
date_default_timezone_set('Europe/Brussels');
$onsubmit = 'event.preventDefault(); node(\'connectSendImport\', {message : \'test\', formId : \'excell\'})';
$form = new Form('excell', 'import_excell', 'POST', $onsubmit, 'Envoie SMS Excell');
$form->setEnctype('multipart/form-data');
$form->addField('file', 'excell', 'file','Fichier Excell','','');
$form->addField('text', 'nbSegment', 'nbSegment','Nombre de segment', '', '', [
    'disabled' => 'disabled',
]);
$form->addField('textarea', 'message', 'message','Contenu SMS', '', '', [
    'oninput' => 'node(\'connectGetSegment\', {textareaId : \'message\'})',
]);

$form->addDateTimePicker('date', 'date', 'Date d\'envois',date('d/m/y H:i'));

$form->setSubmitButton('buttonSubmit', 'submit', 'send', 'Envoyer');
$form->addHtml('<div id="progressBar" class="progress  mb-3" style="height:15px"> </div>');


$layout = new PageLayout();
$layout->addElement($form->render()); // Colonne de taille 4
echo $layout->render();
?>

  
