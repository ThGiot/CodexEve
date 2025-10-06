<?php
require_once PRIVATE_PATH . '/classes/PageLayout.php'; 
require_once PRIVATE_PATH . '/classes/Form.php'; 




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



$onsubmit = 'event.preventDefault(); node(\'moka_prestataire_add\', {formId : \'formPrestataire\'})';

$form = new Form(   id: 'formPrestataire', 
                    name: 'formPrestataire',
                    method: 'POST', 
                    action: $onsubmit, 
                    title: 'Ajout Prestataire'
                );
$form->addField(    type: 'text', 
                    id: 'nom',
                    name: 'nom', 
                    label: 'Nom',
                    placeholder: 'nom',
                    group: 'identification'
                );
$form->addField(    type: 'text', 
                    id: 'p_id',
                    name: 'p_id', 
                    label: 'p_id',
                    placeholder: '',
                    group: 'identification',
                    options: ['disabled' => 'disabled']
                );
$form->addField(    type: 'text', 
                    id: 'prenom',
                    name: 'prenom', 
                    label: 'Prénom',
                    placeholder: 'prenom',
                    group: 'identification'
                );
$form->addField(    type: 'text', 
                    id: 'inami',
                    name: 'inami', 
                    label: 'inami',
                    placeholder: 'inami',
                    group: 'identification'
                    );
$form->addField(    type: 'text', 
                    id: 'telephone',
                    name: 'telephone', 
                    label: 'telephone',
                    placeholder: 'telephone',
                    group:'coordonnee'
                );
$form->addField(    type: 'text', 
                    id: 'email',
                    name: 'email', 
                    label: 'Email',
                    placeholder: 'email',
                    group:'coordonnee'
                );
$form->addField(    type: 'text', 
                    id: 'adresse',
                    name: 'adresse', 
                    label: 'adresse',
                    placeholder: 'adresse',
                    group: 'fact'
            );
$form->addField(    type: 'text', 
                    id: 'niss',
                    name: 'niss', 
                    label: 'niss',
                    placeholder: 'niss',
                    group:'fact'
            );
$form->addField(    type: 'text', 
                    id: 'bce',
                    name: 'bce', 
                    label: 'BCE',
                    placeholder: 'bce',
                    group:'fact'
            );
$form->addField(    type: 'text', 
                    id: 'compte',
                    name: 'compte', 
                    label: 'compte',
                    placeholder: 'compte',
                    group:'fact'
            );
$form->addField(    type: 'text', 
                    id: 'societe',
                    name: 'societe', 
                    label: 'societe',
                    placeholder: 'societe',
                    group:'fact'
            );

$form->setSubmitButton(id: 'buttonSubmit',name: 'submit',value: 'send',text: 'Enregistrer');
$layout = new PageLayout();
$layout->addElement($form->render()); 
echo $layout->render();

?>
