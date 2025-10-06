<?php 
// Inclure les fichiers de configuration et de bibliothèques nécessaires
require_once dirname(__DIR__, 3) . '/vendor/autoload.php';
require_once dirname(__DIR__, 3) . '/config.php';
require_once dirname(__DIR__, 3) . '/sql.php';
require_once dirname(__DIR__, 3) . '/classes/ResponseHandler.php';
require_once dirname(__DIR__, 3) . '/classes/RequestHandler.php';


// Initialiser les gestionnaires de réponse et de requête
$responseHandler = new ResponseHandler();
$requestHandler = new RequestHandler();

// Définir les règles de validation
$rules = [
    'evenement_id' => ['type' => 'int']
];

// Gérer la requête avec authentification, assainissement, et validation
$data= $requestHandler->handleRequest($data, $rules); 

$query = "SELECT * FROM thor_evenement WHERE id = :id";
$stmt = $dbh->prepare($query);
$stmt->bindParam(':id', $data['evenement_id']);
$stmt->execute();
$evenement = $stmt->fetch(PDO::FETCH_ASSOC);
$date_evenement = $evenement['date'];
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
    exit($responseHandler->sendResponse(true, 'Le fichier uploadé n\'est pas un fichier Excel valide.'));
}

// Déplacer le fichier uploadé vers le dossier "uploads"
$uploadFilePath = dirname(__DIR__, 3). '/uploads/'.basename($_FILES['file']['name']);
move_uploaded_file($_FILES['file']['tmp_name'], $uploadFilePath);

try {
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($uploadFilePath);
    $worksheet = $spreadsheet->getActiveSheet();
    $rows = $worksheet->toArray();

    // Lecture de la première ligne pour déterminer les indices de colonnes
    $header = array_shift($rows);
    // Nettoyer les en-têtes
$cleanedHeader = array_map(function($e) {
    return strtoupper(trim($e));
}, $header);

$colIndices = [];

// Chercher des correspondances partielles pour chaque colonne requise
foreach (['NOM DU VOLONTAIRE' => 'NOM', 'PRENOM DU VOLONTAIRE' => 'PRENOM', 'CENTRE DE SECOURS' => 'CENTRE_SECOURS', 'QUALIFICATION' => 'QUALIFICATION', 'DISPONIBILITES' => 'DISPONIBILITES', 'REMARQUES' => 'REMARQUES', 'CHAUFFEUR' => 'CHAUFFEUR'] as $search => $colName) {
    foreach ($cleanedHeader as $index => $headerName) {
        if (strpos($headerName, strtoupper($search)) !== false) {
            $colIndices[$colName] = $index;
            break;
        }
    }
}


    // S'assurer que toutes les colonnes nécessaires sont présentes
    if (in_array(false, $colIndices, true)) {
        // Filtrer pour trouver les clés des colonnes manquantes
        $missingColumns = array_keys($colIndices, false, true);
        throw new Exception("Colonnes manquantes dans le fichier Excel : " . implode(', ', $missingColumns));
    }

} catch (Exception $e) {
    exit($responseHandler->sendResponse(true, 'Erreur lors de la lecture du fichier : ' . $e->getMessage()));
}


foreach ($rows as $row) {
   
    $row_nom = $colIndices['NOM'];
    $row_prenom = $colIndices['PRENOM'];
    $row_cs = $colIndices['CENTRE_SECOURS'];
    $row_pb = $colIndices['CHAUFFEUR'];
    $row_qualification = $colIndices['QUALIFICATION'];
    $row_dispo = $colIndices['DISPONIBILITES'];
    $row_remarque = $colIndices['REMARQUES'];
    
    //cherche si le volontaire existe sinon on l'enregistre 
    try {
        $query = "SELECT * FROM thor_volontaire WHERE nom = :nom AND prenom = :prenom";
        $stmt = $dbh->prepare($query);
        $stmt->bindParam(':nom', $row[$row_nom]);
        $stmt->bindParam(':prenom', $row[$row_prenom]);
        $stmt->execute();
    } catch (PDOException $e) {
        echo $responseHandler->sendResponse(false, "Erreur  : " . $e->getMessage());
        exit();
    }
    
    if ($stmt->rowCount() == 0) {
        $row[$row_pb] = match (strtolower(trim($row[$row_pb]))) {
            'oui' => 1,
            'non' => 0,
            default => 0 
        };
        try{
        $query = "INSERT INTO thor_volontaire (nom, prenom, centre_secours, chauffeur) VALUES (:nom, :prenom, :centre_secours, :chauffeur)";
        $stmt = $dbh->prepare($query);
        $stmt->bindParam(':nom', $row[$row_nom]);
        $stmt->bindParam(':prenom', $row[$row_prenom]);
        $stmt->bindParam(':centre_secours', $row[$row_cs]);
        $stmt->bindParam(':chauffeur', $row[$row_pb]);
        $stmt->execute();
        $volontaire_id = $dbh->lastInsertId();
        }catch (PDOException $e) {
            echo $responseHandler->sendResponse(false, "Erreur  : " . $e->getMessage());
            exit();
        }
    }else{
        $volontaire = $stmt->fetch(PDO::FETCH_ASSOC);
        $volontaire_id = $volontaire['id'];
    }

//Insertion de la qualification

//cherche si le volontaire existe sinon on l'enregistre 
try {
    $query = "SELECT * FROM thor_evenement_qualification WHERE evenement_id = :evenement_id AND volontaire_id = :volontaire_id";
    $stmt = $dbh->prepare($query);
    $stmt->bindParam(':volontaire_id', $volontaire_id);
    $stmt->bindParam('evenement_id', $data['evenement_id']);
    $stmt->execute();
} catch (PDOException $e) {
    echo $responseHandler->sendResponse(false, "Erreur  : " . $e->getMessage());
    exit();
}

if ($stmt->rowCount() == 0) { 
    $query = "INSERT INTO thor_evenement_qualification (evenement_id, volontaire_id, qualification) VALUES (:evenement_id, :volontaire_id, :qualification)";
    $stmt = $dbh->prepare($query);
    $stmt->bindParam('evenement_id', $data['evenement_id']);
    $stmt->bindParam(':volontaire_id', $volontaire_id);
    $stmt->bindParam(':qualification', $row[$row_qualification]);
    $stmt->execute();
    
}else{
    $query = "UPDATE thor_evenement_qualification SET qualification = :qualification WHERE evenement_id = :evenement_id AND volontaire_id = :volontaire_id";
    $stmt = $dbh->prepare($query);
    $stmt->bindParam(':qualification', $row[$row_qualification]);
    $stmt->bindParam(':volontaire_id', $volontaire_id);
    $stmt->bindParam('evenement_id', $data['evenement_id']);
    $stmt->execute();
}



    //Insertion des disponibilités du volontaire 
    $disponibilites = $row[$row_dispo];
    $plages = explode(';', $disponibilites);
    foreach ($plages as $plage) {
        if (empty($plage)) {
            continue;
        }

      
       
        
        preg_match('/(\w+) (\d{2}\/\d{2}\/\d{4}) - (\d{1,2}h\d{2}) \/ (\d{1,2}h\d{2})/', $plage, $matches);

    if (count($matches) === 5) {
        
        $jour_fr = $matches[1]; // Nom du jour en français
        $date_str = $matches[2]; // Date sous forme de chaîne
        $heure_debut_str = $matches[3]; // Heure de début sous forme de chaîne
        $heure_fin_str = $matches[4]; // Heure de fin sous forme de chaîne
       
        // Convertir les heures au format HH:MM
        $heure_debut = str_replace('h', ':', $heure_debut_str);
        $heure_debut .= strpos($heure_debut, ':') === strlen($heure_debut) - 1 ? '00' : '';
        $heure_fin = str_replace('h', ':', $heure_fin_str);
        $heure_fin .= strpos($heure_fin, ':') === strlen($heure_fin) - 1 ? '00' : '';
        
        
      
        // Convertir la date en DateTime
        $date_formattee = DateTime::createFromFormat('d/m/Y', $date_str);
        $date_Debut = $date_formattee->format('Y-m-d');
        
        // Créer les objets DateTime pour les heures de début et de fin
        $datetime_debut = new DateTime("$date_Debut $heure_debut");
        $datetime_fin = new DateTime("$date_Debut $heure_fin");
        
        // Gestion de l'heure de fin si elle est le jour suivant
        if ($datetime_fin < $datetime_debut) {
            $datetime_fin->modify('+1 day');
        }

        
    }

    $date_fin = $datetime_fin->format('Y-m-d H:i:s');
    $query = "DELETE FROM thor_disponibilites WHERE evenement_id = :evenement_id AND volontaire_id = :volontaire_id";
    $stmt = $dbh->prepare($query);
    $stmt->bindParam(':volontaire_id', $volontaire_id);
    $stmt->bindParam(':evenement_id', $data['evenement_id']);
    $stmt->execute();
    
    try {
        $query = "INSERT INTO thor_disponibilites (volontaire_id, evenement_id, debut, fin, remarque) VALUES (:volontaire_id, :evenement_id, :debut, :fin, :remarque)";
        $stmt = $dbh->prepare($query);
        $stmt->bindParam(':volontaire_id', $volontaire_id);
        $stmt->bindParam(':evenement_id', $data['evenement_id']);
        
        $date_debut = $datetime_debut->format('Y-m-d H:i:s');
        $stmt->bindParam(':debut', $date_debut);
        
        $date_fin = $datetime_fin->format('Y-m-d H:i:s');
        $stmt->bindParam(':fin', $date_fin);
        
        $remarque = $row[$row_remarque];
        $stmt->bindParam(':remarque', $remarque);
        
        $stmt->execute();
        }catch (PDOException $e) {
            echo $responseHandler->sendResponse(false, "Erreur  : " . $e->getMessage());
            exit();
        }
            
           
        
    }
}
exit($responseHandler->sendResponse(true, 'Fichier Importé'));

?>