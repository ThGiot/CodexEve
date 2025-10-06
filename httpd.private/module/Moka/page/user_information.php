<?php
require_once PRIVATE_PATH . '/classes/PageLayout.php'; 
require_once PRIVATE_PATH . '/classes/Form.php'; 
require_once dirname(__DIR__, 3) . '/classes/RequestHandler.php';
require_once PRIVATE_PATH . '/classes/BootstrapCard.php'; 
try {
    $sql = "SELECT * FROM moka_prestataire WHERE user_id = :user_id AND client_id= :client_id";
    $stmt = $dbh->prepare($sql);
    $stmt ->bindParam(':user_id', $_SESSION['user']['id']);
    $stmt ->bindParam(':client_id', $_SESSION['client_actif']);
    $stmt->execute();
    $prestataire = $stmt->fetch(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    echo "Erreur lors de la récupération du prestataire : " . $e->getMessage();
    exit;
}

if(empty($prestataire)){
    $layout = new PageLayout();
    $card = new BootstrapCard();
    $card->setHeader("<b>Erreur</b>");
    $content .="Vous n'avez pas de compte prestataire associé à votre compte !</b>";
    $content .='</br> Merci de contacter un administrateur.';
    $card->setBody($content);
    $layout->addElement($card->render(),6); 
    echo $layout->render();
    exit();
}

$onsubmit = 'event.preventDefault(); node(\'moka_prestataire_maj_save\', {formId : \'formPrestataire\', prestataire_id :\''.$prestataire['id'].'\'})';

$form = new Form(   id: 'formPrestataire', 
                    name: 'formPrestataire',
                    method: 'POST', 
                    action: $onsubmit, 
                    title: 'Fiche Prestataire'
                );
$form->addField(    type: 'text', 
                    id: 'nom',
                    name: 'nom', 
                    label: 'Nom',
                    value: $prestataire['nom'],
                    group: 'identification',
                    options: ['disabled' => 'disabled']
                );
$form->addField(    type: 'text', 
                    id: 'p_id',
                    name: 'p_id', 
                    label: 'ID Ebrigade',
                    value: $prestataire['p_id'],
                    group: 'identification',
                    options: ['disabled' => 'disabled']
                );
$form->addField(    type: 'text', 
                    id: 'inami',
                    name: 'inami', 
                    label: 'inami',
                    value: $prestataire['inami'],
                    group: 'identification'
                );
$form->addField(    type: 'text', 
                    id: 'prenom',
                    name: 'prenom', 
                    label: 'Prénom',
                    value: $prestataire['prenom'],
                    group: 'identification',
                    options: ['disabled' => 'disabled']
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
                    label: 'Société (Laisser vide si vous n\'êtes pas en SRL)',
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