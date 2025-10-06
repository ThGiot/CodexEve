<?php
require_once PRIVATE_PATH . '/classes/PageLayout.php'; 
require_once PRIVATE_PATH . '/classes/Table.php'; 
$table = new Table("Log SMS", ["ID", "Message", "Créer par", "Statut", "Date"], "smsListe");

try {
      // Définir la requête SQL
      $sql = "SELECT cm.id AS cm_id, u.id AS u_id, cm.message, cm.status, u.nom, u.prenom, cm.send_date AS date FROM connect_messages cm
      JOIN user u ON u.id = cm.created_by 
      WHERE client_id = :client_id
      ORDER by date";
    // Préparez la déclaration
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':client_id', $_SESSION['client_actif']);
    // Exécutez la requête
    $stmt->execute();
    // Récupérez tous les résultats
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Traitement des résultats si nécessaire, par exemple :
    foreach ($messages as $message) {

        $option =  ["name" => "Manage", "link" => "getContent(3,{message_id : '".$message['cm_id']."'})", "class" => ""];
        if($message['status'] == 'scheduled'){
            $action_switch = ["name" => "Annuler", "link" => "node('connect_SmsDelete', {smsId: '".$message['cm_id']."'})", "class" => "danger"];
            $option = [$option,$action_switch ];
        }else{
            $option = [$option];
        }
        $table->addRow(
            [   
            "ID" => $message['cm_id'], 
            "Message" => $message['message'],
            "Créer par" => $message['nom']. ' ' .$message['prenom'],
            "Statut"    => $message['status'],
            "Date" => $message['date']
            ],$option
            
        );
    }
} catch (PDOException $e) {
    echo "Erreur lors de la récupération des sms : " . $e->getMessage();
    exit;
}
$layout = new PageLayout();
$layout->addElement($table->render()); 
echo $layout->render();



  ?>