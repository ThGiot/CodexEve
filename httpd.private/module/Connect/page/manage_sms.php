<?php
require_once PRIVATE_PATH . '/classes/PageLayout.php'; 
require_once PRIVATE_PATH . '/classes/Table.php'; 
require_once PRIVATE_PATH . '/classes/Form.php'; 

try {
    // Définir la requête SQL
    $sql = "SELECT * FROM connect_messages WHERE id = :id AND client_id = :client_id";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':id', $data['message_id']);
    $stmt->bindParam(':client_id', $_SESSION['client_actif']);
    $stmt->execute();
    $sms = $stmt->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Erreur lors de la récupération des informations du SMS manage_sms : " . $e->getMessage();
  exit;
}

date_default_timezone_set('Europe/Brussels');
$onsubmit = 'event.preventDefault();';
if($sms['status'] == 'scheduled') $onsubmit .= ' node(\'connect_send_sms\', {messageId : \''.$data['message_id'].'\'})';
$form = new Form('logSms', 'logSms', 'POST', $onsubmit, 'Log SMS n°'.$sms['id']);

switch($sms['status']) {
    case 'pending' :
        $statut = '<button class="btn btn-soft-secondary me-1 mb-1" type="button">Pending...</button>';
    break;

    case 'sent' :
        $statut = '<button class="btn btn-soft-success me-1 mb-1" type="button">Sent</button>';
    break;

    case 'failed' :
        $statut = '<button class="btn btn-soft-danger me-1 mb-1" type="button">Failed</button>';
    break;

    case 'scheduled' :
        $statut = '<button class="btn btn-soft-primary me-1 mb-1" type="button">Scheduled</button>';
    break;

    case 'canceled' :
        $statut = '<button class="btn btn-soft-danger me-1 mb-1" type="button">Canceled</button>';
    break;
}
$form->addHtml('Statut : '.$statut);

if($sms['status'] == 'scheduled') {
    

  


    $form->addField('textarea', 'message', 'message','Contenu SMS', $sms['message'], '',[
        'onchange' => 'node(\'connect_maj_scheduled\', {item :\'message\', id : \'message\', smsId : \''.$sms['id'].'\'})',
    ]);
    
    $date = DateTime::createFromFormat('Y-m-d H:i:s', $sms['send_date'])->format('d/m/y H:i');
    $form->addDateTimePicker('date', 'date', 'Date d\'envois',$date,'',[
        'onchange' => 'node(\'connect_maj_scheduled\', {item :\'date\', id : \'date\',smsId : \''.$sms['id'].'\'})',
    ]);
    $form->setSubmitButton('buttonSubmit', 'submit', 'send', 'Envoyer maintenant !');
    $form->addHtml('<div id="progressBar" class="progress  mb-3" style="height:15px"> </div>');
}else{
    $form->addField('textarea', 'message', 'message','Contenu SMS', $sms['message'], '', [
    
        'disabled' => 'disabled',
    ]);
    $form->addField('text', 'date', 'date','Date d\'envois', $sms['send_date'], '', [
    
        'disabled' => 'disabled',
    ]);
}



// on va ajouter un tableau également 

$table = new Table("Log SMS",["ID", "Destinataire", "Statut", "Envois"], "smsListe");

try {
      // Définir la requête SQL
      $sql = "SELECT id, phone_number, status, DATE_FORMAT(sent_at, '%Y-%m-%d %H:%i:%s') as sent_at FROM connect_recipients WHERE message_id = :message_id";

    // Préparez la déclaration
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':message_id', $sms['id']);
    // Exécutez la requête
    $stmt->execute();
    // Récupérez tous les résultats
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Traitement des résultats si nécessaire, par exemple :
    foreach ($messages as $message) {

        $table->addRow(
            [   
            "ID" => $message['id'], 
            "Destinataire" => $message['phone_number'],
            "Statut" => $message['status'],
            "Envois"    => $message['sent_at']
            ],
            
        );
    }
} catch (PDOException $e) {
    echo "Erreur lors de la récupération des clients : " . $e->getMessage();
    exit;
}

$layout = new PageLayout();
$layout->addElement( $form->render()); // Colonne de taille 4
$layout->addElement($table->render()); // Colonne de taille 4
echo $layout->render();
?>