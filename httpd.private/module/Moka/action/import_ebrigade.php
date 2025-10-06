<?php
require_once dirname(__DIR__, 3) . '/config.php';
require_once dirname(__DIR__, 3) . '/sql.php';
require_once dirname(__DIR__, 3) . '/classes/ResponseHandler.php';
require_once dirname(__DIR__, 3) . '/classes/RequestHandler.php';
require_once dirname(__DIR__, 3) . '/classes/DBHandler.php';

require_once dirname(__DIR__, 1) . '/fonctions/importEbrigadeCurl.php';
require_once dirname(__DIR__, 1) . '/fonctions/activiteInfos.php';
require_once dirname(__DIR__, 1) . '/fonctions/findPrestataire.php';
require_once dirname(__DIR__, 1) . '/fonctions/getPrestation.php';
require_once dirname(__DIR__, 1) . '/fonctions/calcMontantTotal.php';
require_once dirname(__DIR__, 1) . '/fonctions/factureInsert.php';
require_once dirname(__DIR__, 1) . '/fonctions/factureInsertDetail.php';
require_once dirname(__DIR__, 1) . '/fonctions/majFactureMontant.php';

$responseHandler = new ResponseHandler();
$requestHandler = new RequestHandler();


$rules = [
    'dateStart' => ['type' => 'string', 'max_length' => 40],
    'dateFin' => ['type' => 'string', 'max_length' => 40],
    'designation' => ['type' => 'string', 'max_length' => 40]
];
// Gérer la requête avec authentification, assainissement, et validation
$data = $requestHandler->handleRequest($data, $rules);
$designation = $data['designation'];


$data = array(
    "token" => MEDTEAM_EB_API_KEY,
    "dDebut" => $data['dateStart'],
    "dFin" => $data['dateFin'],

);


// Définir la requête SQL



$prestations = importEbrigadeCurl(json_encode($data));
$activites = activiteInfo($dbh, $_SESSION['client_actif']);


$facture = [];
foreach ($activites as $codeActivite => $infoActivite) {
    $facture[$infoActivite['AnalytiqueId']] = [];
}

// Traitement des prestations
foreach ($prestations as $prestation) {
    $codeActivite = substr($prestation['E_LIBELLE'], 0, 4); // Les 4 premiers caractères de E_LIBELLE
    $codeActivite = str_replace(' ', '', $codeActivite); // Retire les espaces
    $codeActivite = str_replace('-', '', $codeActivite); // Retire les tirets
    $analytiqueId = null;
    $remunerationType = null;
    

    // Trouver l'analytique correspondant
    foreach ($activites as $code => $infoActivite) {
       
        if (strpos($prestation['E_LIBELLE'], $code) === 0) {
            $analytiqueId = $infoActivite['AnalytiqueId'];
            $remunerationType = $activites[$codeActivite]['RemunerationType'];
         
            break;
        }
    }

    if ($analytiqueId !== null) {
        $p_id = $prestation['P_ID'];
     
        // Si P_ID n'existe pas encore sous cet analytique, l'initialiser
        if (!isset($facture[$analytiqueId][$p_id])) {
           if($prestataire_infos = findPrestataire($dbh,$_SESSION['client_actif'],$p_id)){
                $facture[$analytiqueId][$p_id] = [
                    'nom' => $prestation['P_NOM'],
                    'prenom' => $prestation['P_PRENOM'],
                    'P_PHONE' => $prestation['P_PHONE'],
                    'P_ID' => $prestation['P_ID'],
                    'prestataire_infos' => [$prestataire_infos],
                    'analytique' =>$activites[$codeActivite]['analytique'],
                    'libelle_nom' => $activites[$codeActivite]['analytique_nom'],
                    'total_montant' => 0,
                    'prestations' => [] 
                ];
            }else{
                $facture[$analytiqueId][$p_id] = [
                    'nom' => $prestation['P_NOM'],
                    'prenom' => $prestation['P_PRENOM'],
                    'P_PHONE' => $prestation['P_PHONE'],
                    'P_ID' => $prestation['P_ID'],
                    'prestataire_infos' => [
                        [
                        'nom' => $prestation['P_NOM'],
                        'prenom' => $prestation['P_PRENOM'],
                        'telephone' => $prestation['P_PHONE']
                        ]
                    ],
                    'analytique' =>$activites[$codeActivite]['analytique'],
                    'libelle_nom' => $activites[$codeActivite]['analytique_nom'],
                    'total_montant' => 0,
                    'prestations' => [] 
                ];
            }
        }

        // Calcul du montant de la prestation
        $montant = 0;
        $montant_perm = $activites[$codeActivite]['Remuneration'][$prestation['P_GRADE']]['MontantPerm'];
        $montant_garde = $activites[$codeActivite]['Remuneration'][$prestation['P_GRADE']]['MontantGarde'];
        if($remunerationType ==  'GARD'){
            $heure = getPrestation($dbh,$_SESSION['client_actif'],$prestation['E_CODE'],$p_id);
            if($heure > $prestation['EP_DUREE']){
                $montant = $montant_perm * ($prestation['EP_DUREE'] + $heure);
            }else{
                $montant = ($prestation['EP_DUREE'] - $heure) * $montant_garde + $heure*$montant_perm;
            }
            
        }else{
            $heure = getPrestation($dbh,$_SESSION['client_actif'],$prestation['E_CODE'],$p_id);
            $montant = $montant_perm * ($prestation['EP_DUREE'] + $heure);
        }
        // Ajouter la prestation sous le P_ID correspondant
        $facture[$analytiqueId][$p_id]['prestations'][] = [
            // Ajoutez ici les détails nécessaires de la prestation, par exemple :
            'date' => $prestation['EH_DATE_DEBUT'],
            'libelle' => $prestation['E_LIBELLE'],
            'duree' => $prestation['EP_DUREE'],
            'heure' => $heure,
            'grade' => $prestation['P_GRADE'],
            'type_remuneration' => $remunerationType,
            'E_CODE' => $prestation['E_CODE'],
            'code' => $codeActivite,
            'montant' => $montant
        ];
    }
}

$n_facture = 0;
calculerMontantTotal($facture);
foreach($facture as $analytiqueId => $prestataire) {
    
    foreach($prestataire as $prestataire_id => $facture_prestataire){
        $designation_finale = $facture_prestataire['libelle_nom']. ' ' . $designation;
        $facture_id = factureInsert($dbh,$designation_finale, $facture_prestataire['prestataire_infos'][0],$facture_prestataire['analytique'],$facture_prestataire['total_montant'], $_SESSION['client_actif'],$facture_prestataire['P_ID']);
        $n_facture++;
        
        foreach ($facture_prestataire['prestations'] as $facture_detail) {
            if ($facture_detail['type_remuneration'] == 'GARD') {
                $libelle_detail = $facture_detail['date'].' : '.$facture_detail['libelle'] . ' heure(s) encodée(s) : ' . $facture_detail['heure'];
            } elseif ($facture_detail['type_remuneration'] == 'PERM') {
                $libelle_detail = $facture_detail['date'].' : '.$facture_detail['libelle'] . ' heure(s) encodée(s) : '.$facture_detail['duree'].' & heure(s) supplémentaires encodée(s) : ' . $facture_detail['heure'];
            }
         
           $facture_det =factureInsertDetail($dbh,$facture_id,$libelle_detail,$facture_detail['montant']);
          
        }
    }
    
}

//Chercher les correction en attente
//Chercher si une facture non protégée existe pour le prestatataire dans l'analytique
//Si oui, on ajoute le détail et on classe la correction en archive

$sql = "    SELECT ma.analytique AS analytique, mfc.* FROM moka_facture_correction mfc 
            JOIN moka_analytique ma ON mfc.analytique_id = ma.id
            WHERE mfc.client_id = :client_id";
            $stmt = $dbh->prepare($sql);
            $stmt -> bindParam(':client_id', $_SESSION['client_actif']);
            $stmt->execute();
            $corrections = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($corrections as $correction) {
    $query = " SELECT * FROM moka_facture WHERE analytique = :analytique AND client_id = :client_id AND prestataire_id = :prestataire_id AND montant + :montant_correction > 0 AND protected = 0";
    $stmt = $dbh->prepare($query);
    $stmt->bindParam(':analytique', $correction['analytique']);
    $stmt->bindParam(':prestataire_id', $correction['prestataire_id']);
    $stmt->bindParam(':montant_correction', $correction['montant']);
    $stmt->bindParam(':client_id',  $_SESSION['client_actif']);
    $stmt->execute();

    if($stmt->rowCount() > 0) {
        $facture = $stmt->fetch(PDO::FETCH_ASSOC);
        factureInsertDetail($dbh,$facture['id'],$correction['designation'],$correction['montant']);
        majFactureMontant($dbh,$facture['id']);

        $query = "INSERT INTO moka_facture_correction_archive SET facture_id = :facture_id, montant = :montant, designation = :designation, client_id = :client_id";
        $stmt = $dbh->prepare($query);
        $stmt->bindParam(':facture_id', $facture['id']);
        $stmt->bindParam(':montant', $correction['montant']);
        $stmt->bindParam(':designation', $correction['designation']);
        $stmt->bindParam(':client_id',  $_SESSION['client_actif']);
        $stmt->execute();

        $query = "DELETE FROM moka_facture_correction WHERE id =:id";
        $stmt = $dbh->prepare($query);
        $stmt->bindParam(':id', $correction['id']);
        $stmt->execute();
        
    }
}

exit($responseHandler->sendResponse(true, $n_facture.' Factures importées'));
?>

