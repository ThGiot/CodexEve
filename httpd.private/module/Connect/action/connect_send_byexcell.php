<?php 
$module_id = 2; // ID MODULE CONNECT

// Inclure les fichiers de configuration et de bibliothèques nécessaires
require dirname(__DIR__, 3) . '/vendor/autoload.php';

require_once dirname(__DIR__, 3) . '/config.php';
require_once dirname(__DIR__, 3) . '/sql.php';
require_once dirname(__DIR__, 3) . '/classes/DBHandler.php';
require_once dirname(__DIR__, 3) . '/classes/PhoneNumberHandler.php';
require_once dirname(__DIR__, 3) . '/classes/ResponseHandler.php';
require_once dirname(__DIR__, 3) . '/classes/RequestHandler.php';
require_once dirname(__DIR__, 3) . '/classes/CronJob.php';  


// Initialiser les gestionnaires de réponse et de requête
$responseHandler = new ResponseHandler();
$requestHandler = new RequestHandler();

// Définir les règles de validation
$rules = [
    'message' => ['type' => 'string', 'max_length'=> 7000],
    'date' => ['type' => 'string', 'max_length'=> 25]
];

// Gérer la requête avec authentification, assainissement, et validation
$data= $requestHandler->handleRequest($data, $rules); 
$requestHandler-> verifyModulePermission($module_id,$dbh);


//----------------------------------------------------------------
// lecture du fichier Excell
//----------------------------------------------------------------
// Définir les types MIME valides pour les fichiers Excel
$mimes = [
    'application/vnd.ms-excel',
    'text/xls',
    'text/xlsx',
    'application/vnd.oasis.opendocument.spreadsheet',
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
];

// Vérifier si le type MIME du fichier uploadé est valide
if(!in_array($_FILES["file"]["type"], $mimes)) {
    exit($responseHandler->sendResponse(false, 'Le fichier uploadé n\'est pas un fichier Excel valide.'));
}

// Déplacer le fichier uploadé vers le dossier "uploads"
$uploadFilePath = dirname(__DIR__, 3). '/uploads/'.basename($_FILES['file']['name']);
move_uploaded_file($_FILES['file']['tmp_name'], $uploadFilePath);

// Essayer d'initialiser le lecteur de fichiers Excel
try {
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($uploadFilePath);
    $worksheet = $spreadsheet->getActiveSheet();
    $rows = $worksheet->toArray();
} catch (Exception $e) {
    exit($responseHandler->sendResponse(false, 'Erreur lors de la lecture du fichier : ' . $e->getMessage()));
}




$phoneNumbers = [];


$dbHandler = new DBHandler($dbh);
//Conversion format de la date

$date = DateTime::createFromFormat('d/m/y H:i', $data['date']);
$formattedDate = $date->format('Y-m-d H:i:s');


$messageData = [
    'client_id' => $_SESSION['client_actif'], 
    'created_by' => $_SESSION['user']['id'], 
    'message' => $data['message'],
    'send_date' => $formattedDate 
];

// Insert into messages table and get the last inserted id
$dbHandler->insert('connect_messages', $messageData);
$lastMessageId = $dbh->lastInsertId();

$dateFormat = 'd/m/y H:i';
$inputDate = DateTime::createFromFormat($dateFormat, $data['date']);
$now = new DateTime();
if ($inputDate <= $now) {
    $status = 'pending';
}else{
    $status = 'scheduled';
}

foreach ($rows as $Row) {
    
    $handler = new PhoneNumberHandler();
    $phoneNumber =$handler->formatNumber($Row[0]);
    // Prepare data to insert into the messages and recipients table
    

    // Now, insert into recipients table
    $recipientData = [
        'message_id' => $lastMessageId,
        'phone_number' => $phoneNumber,
        'status' => $status
    ];

    $dbHandler->insert('connect_recipients', $recipientData);
}

// Supprimer le fichier uploadé une fois traité
unlink($uploadFilePath);

if ($inputDate <= $now) {
    $responseHandler->addData('execute', 'true');
    $responseHandler->addData('messageId', $lastMessageId);
  
} 


exit($responseHandler->sendResponse(true, 'success'));


?>
