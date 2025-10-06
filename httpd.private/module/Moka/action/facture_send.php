<?php
require_once dirname(__DIR__, 3) . '/config.php';
require_once dirname(__DIR__, 3) . '/sql.php';
require_once dirname(__DIR__, 2) . '/role.php';
require_once dirname(__DIR__, 3) . '/classes/ResponseHandler.php';
require_once dirname(__DIR__, 3) . '/classes/RequestHandler.php';
require_once dirname(__DIR__, 3) . '/classes/DBHandler.php';
require_once dirname(__DIR__, 3) . '/classes/EmailSender.php';
require_once dirname(__DIR__, 1) . '/fonctions/getLastPDFsForClient.php';

// Gérer la requête avec authentification, assainissement, et validation
$responseHandler = new ResponseHandler();
$requestHandler = new RequestHandler();
if($role >= 3){
    exit('Niveau d\'accès insufisant'.$role);
}
$rules = [];
$data = $requestHandler->handleRequest($data, $rules);
$requestHandler ->verifyModulePermission(3,$dbh);

//Récupération des dernier documents


$sql = "SELECT * FROM client WHERE id = :client_id ";
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':client_id', $_SESSION['client_actif']);
            $stmt->execute();
            $client = $stmt->fetch(PDO::FETCH_ASSOC);
$client_nom = $client['nom'];

$directoryPath = dirname(__DIR__, 1) . '/facture/';
$clientActif = $_SESSION['client_actif']; 
$latestPDFs = getLastPDFsForClient($clientActif, $directoryPath);
$content ='';
foreach ($latestPDFs as $pdfFilePath) {
    $filename = basename($pdfFilePath);
    $parts = explode('_', $filename);

    
        $analytique = $parts[1]; // L'analytique est la seconde partie
        if (count($parts) >= 3) {
        // Créer une nouvelle carte pour chaque fichier PDF
       
      
        $date= $parts[2].'-'.$parts[3].'-'.$parts[4].':'.$parts[5]; // Définir le corps de la carte avec l'icône PDF

        $sql = "SELECT * FROM moka_analytique WHERE client_id = :client_id AND REPLACE(REPLACE(REPLACE(analytique, ' ', ''), '/', ''), '-', '') = :analytique";
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':client_id', $_SESSION['client_actif']);
            $stmt->bindParam(':analytique', $analytique);
            $stmt->execute();
            $analytique_distrib = $stmt->fetch(PDO::FETCH_ASSOC);
            $to = $analytique_distrib['distribution'];
    
        $emailSender = new EmailSender();
      
        $subject = 'Facture '.$client_nom.' | '.$analytique;
        $htmlMessage = $emailSender->generateHtmlFacture($date, $analytique, $filename,$client_nom);
        $textMessage = "";
        $attachmentPath = __DIR__ . '/../facture/'.$filename;
        $attachments = [$attachmentPath];
        $cc = []; // Remplacer par les adresses e-mail réelles
        $bcc = []; // Remplacer par les adresses e-mail réelles
    
        // Envoi de l'email
        $result = $emailSender->sendEmail($to, $subject, $htmlMessage, $textMessage, $attachments, $cc, $bcc);
       
       
    }
}


exit($responseHandler->sendResponse(true,'facture envoyées'));
?>