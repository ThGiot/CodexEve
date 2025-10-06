
<?php

require_once PRIVATE_PATH . '/classes/PageLayout.php'; 
require_once PRIVATE_PATH . '/classes/Form.php'; 
require_once PRIVATE_PATH . '/classes/BootstrapCard.php'; 





$layout = new PageLayout();
$card = new BootstrapCard();
$card->setHeader("<img width=\"5%\" src=".APPLI_LOGO."> Bienvenue sur Eve !");
$content ="Vous n'appartenez actuellement à aucun client. </br> Pour rejoindre un client, veuillez contacter votre administrateur afin qu'il vous envoie un lien.";
$content .="<p>Si vous désirez créer un nouveau client merci de prendre contact avec nous via admin@hygea-consult.be</p>";
$card->setBody($content);


$card2 = new BootstrapCard();
$card2->setHeader("Utilisateur <b>MEDICAL TEAM !</b>");
$content2 ="<p>Vous êtes un utilisateur Medical Team</p>";
$content2 .='<button onclick = "getContent(10,{});"class="btn btn-phoenix-success me-1 mb-1" type="button">J\'ai un code invitation</button>';
$card2->setBody($content2);

$layout->addElement($card->render(),8,'groupe1'); 
$layout->addElement($card2->render(),8,'groupe2'); 
echo $layout->render();
?>
