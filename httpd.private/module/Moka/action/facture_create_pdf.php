<?php
require_once dirname(__DIR__, 3) . '/config.php';
require_once dirname(__DIR__, 3) . '/sql.php';
require_once dirname(__DIR__, 3) . '/classes/DBHandler.php';
require_once dirname(__DIR__, 3) . '/classes/PhoneNumberHandler.php';
require_once dirname(__DIR__, 3) . '/classes/ResponseHandler.php';
require_once dirname(__DIR__, 3) . '/classes/RequestHandler.php';
require dirname(__DIR__, 3) . '/vendor/autoload.php';
require_once dirname(__DIR__, 1) . '/fonctions/facture_triage.php';

// Initialiser les gestionnaires de réponse et de requête
$requestHandler = new RequestHandler();
$responseHandler = new ResponseHandler();
$rules = [
    'date_debut' => ['type' => 'string', 'max_length' => 40],
    'date_fin' => ['type' => 'string', 'max_length' => 40]
];

$data = $requestHandler->handleRequest($data, $rules); 


//On vérifie qu'il n'y ai pas de facture en attente
try {
    // Préparer la requête
    $stmt = $dbh->prepare("SELECT COUNT(*) FROM moka_facture WHERE prestataire_id IS NULL");

    // Exécuter la requête
    $stmt->execute();

    // Récupérer le résultat
    $count = $stmt->fetchColumn();

    // Vérifier si toutes les factures ont un prestataire_id non null
    if ($count == 0) {
        
    } else {
    //    exit($responseHandler->sendResponse(false,' ATTENTION ! Factures non générées. Il reste des factures en attentes !'));
    }

} catch (PDOException $e) {
    die("Erreur PDO : " . $e->getMessage());
}


//Il faut trier les factures non protégée
facture_triage (dbh: $dbh, date_debut: $data['date_debut'], date_fin: $data['date_fin']);


//$data = $requestHandler->handleRequest($data, $rules); 
$i=0;

$sql = "SELECT * FROM moka_analytique WHERE client_id =:client_id";
$stmt = $dbh->prepare($sql);
$stmt -> bindParam(':client_id', $_SESSION['client_actif']);
$stmt->execute();
$analytiques = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($analytiques as $analytique) {
    $pdf = new FPDF('P','mm','A4');
    $i=0;
   
    $analytique['analytique'] = str_replace(' ', '', $analytique['analytique']);


    
    
    $sql2 = "SELECT *, YEAR(date) AS annee FROM moka_facture WHERE client_id =:client_id AND REPLACE(analytique, ' ', '') = REPLACE(:analytique, ' ', '') AND prestataire_id IS NOT NULL";
    $stmt2 = $dbh->prepare($sql2);
    $stmt2 -> bindParam(':client_id', $_SESSION['client_actif']);
    $stmt2 -> bindParam(':analytique',   $analytique['analytique']);
    $stmt2->execute();
    $factures = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($factures as $facture) {
       // Convertir les chaînes en objets DateTime
       $dateFacture = DateTime::createFromFormat('Y-m-d', substr($facture['date'], 0, 10));
       $dateDebut = DateTime::createFromFormat('Y-m-d', substr($data['date_debut'], 0, 10));
       $dateFin = DateTime::createFromFormat('Y-m-d', substr($data['date_fin'], 0, 10));
       
        // Vérifier si les objets DateTime ont été créés avec succès
        if ($dateFacture === false || $dateDebut === false || $dateFin === false) {
            // Gérer l'erreur
            echo "Erreur de format de date.";
            // Arrêter le script ou gérer l'erreur comme nécessaire
            exit;
        }

        // Effectuer la comparaison
        if ($dateFacture < $dateDebut || $dateFacture > $dateFin) {
            continue;
        }
        $i++;
       
        $pdf->AddPage();
        
        //On protège la facture
        $query3 = "UPDATE moka_facture SET protected = 1 WHERE id = :facture_id AND client_id =:client_id";
        $stmt3 = $dbh->prepare($query3);
        $stmt3->bindParam(':facture_id', $facture['id']);
        $stmt3->bindParam(':client_id',  $_SESSION['client_actif']);
        $stmt3->execute();
        if($facture['montant'] == 0)continue;
       
        
        //Traitement de l'encodage (merci FPDF)
        $nom = iconv('UTF-8', 'windows-1252', $facture['nom']);
        $prenom = iconv('UTF-8', 'windows-1252', $facture['prenom']);
        //RECUPERATION SOCIETE SI SOCIETE
        $adresse = iconv('UTF-8', 'windows-1252', $facture['adresse']);
        $act = iconv('UTF-8', 'windows-1252', 'Activité code : ');
        $code_centralisateur = iconv('UTF-8', 'windows-1252', $analytique['code_centralisateur']);
        $entite = iconv('UTF-8', 'windows-1252', $analytique['entite']);
        $date = $facture['date'];
        $analytique_string = str_replace(" ", "", $facture['analytique']);
        $ref = iconv('UTF-8', 'windows-1252', $entite.'/'.$analytique_string.'/XXX/'.$code_centralisateur.'/');
        
   
        //génération du numéro de facture
        $n = $facture['numero'];
        if ($n < 10){ $n = '00'.$n;}
        elseif($n <100){ $n = '0'.$n;}
        $year = $facture['annee'];
        $nfacture = $year.'-'.$n.'-MT';

    
        $pdf->SetFont('Arial','B',13);
        $pdf->SetX(176); // régler la position X pour aligner avec la référence
        $pdf->Cell(50 ,4,'FACTURE',0,1);
        
        if (!empty($analytique_string)){
        
            $pdf->SetFont('Arial','',10);
            $pdf->Cell(100 ,2,"",0,0);
            $pdf->Cell(50 ,5,iconv('UTF-8', 'windows-1252','Référence :').' '.$ref,0,1);
        }

        $pdf->Cell(189 ,15,'',0,1);
        $pdf->SetFont('Arial','B',12);
        $pdf->Cell(140 ,5,$nom.' '.$prenom,0,0);
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(25 ,5,'Facture No : '.$nfacture,0,1);

        $pdf->Cell(140 ,5,$adresse,0,0);
        $date = date('d-m-Y', strtotime($date));
        $pdf->Cell(25 ,5,'Date : '.$date,0,1);
        if(empty($facture['bce'])) $facture['bce'] = $facture['niss'];
        $pdf->Cell(140 ,5,$facture['bce'],0,0);
        $pdf->Cell(25 ,5,'Compte : 6132700 ',0,1);
        $pdf->Cell(189 ,10,'',0,1);

        if(LOCAL == true){
            $pdf->Image	(dirname(__DIR__, 4) .'/httpd.public/assets/img/g22.png', 10, 5, 25, 25, 'PNG');
        }else{
             $pdf->Image	(dirname(__DIR__, 4) .'/httpd.www/assets/img/g22.png', 10, 5, 25, 25, 'PNG');
        }
        $pdf->SetFont('Arial','B',12);
        $pdf->Cell(90 ,5,'A L\'attention de ',0,0);
        $pdf->Cell(130 ,5,'Objet :',0,1);
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(90 ,5,'Croix-Rouge de Belgique',0,0);
        $pdf->Cell(25 ,5,$facture['designation'],0,1);
        $pdf->Cell(25 ,5,'Medical Team Bruxelles Capitale',0,1);
        $pdf->Cell(25 ,5,'Rue Rempart des Moines 78,',0,1);
        $pdf->Cell(25 ,5,'1000 Bruxelles',0,1);

        $pdf->SetFont('Arial','',10);
        $pdf->Cell(59 ,5,'',0,0);
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(189 ,10,'',0,1);

        $pdf->SetFont('Arial','B',10);
        /*Heading Of the table*/
        $pdf->Cell(135 ,6,'Designation',1,0,'C');
        $pdf->Cell(50 ,6,'Montant HT',1,1,'C');
        $pdf->SetFont('Arial','',8);
        
        //Ajout des détails
       
        $sql4 = "SELECT * FROM moka_facture_detail WHERE facture_id = :facture_id";
        $stmt4 = $dbh->prepare($sql4);
        $stmt4 -> bindParam(':facture_id', $facture['id']);
        $stmt4->execute();
        $details = $stmt4->fetchAll(PDO::FETCH_ASSOC);
       
        
        foreach ($details as $detail) {
            $x = $pdf->GetX();
            $y = $pdf->GetY();
            $montant = number_format((float)$detail['montant'], 2, ',', '');
            $montant = iconv('UTF-8', 'windows-1252', $montant . '€');
            $designation = iconv('UTF-8', 'windows-1252', $detail['designation']);
            // MultiCell pour la désignation
            $pdf->MultiCell(135, 6, $designation, 1, 'L');
        
            // Hauteur de la dernière MultiCell, nécessaire pour la prochaine cellule
            $newY = $pdf->GetY();
            $cellHeight = $newY - $y;
        
            // Positionnement pour la cellule Montant
            $pdf->SetXY($x + 135, $y);
        
            // Cell pour le montant, hauteur basée sur la MultiCell précédente
            $pdf->Cell(50, $cellHeight, $montant, 1, 1, 'L');
        
            // Réinitialisation de la position Y pour la prochaine ligne
            $pdf->SetY($newY);
        }
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(110 ,6,'TVA non applicable',0,0);
        $pdf->Cell(25 ,6,'Total',0,0);
        $pdf->SetFont('Arial','B',10);
        $montant = number_format((float)$facture['montant'], 2, ',', '');
        $montant = iconv('UTF-8', 'windows-1252', $montant.'€');
        $pdf->Cell(50 ,6,$montant,1,1,'');
    
        $pdf->Cell(10 ,6,'',0,1);
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(25 ,6,'Priere de regler ce montant par virement bancaire sur le compte suivant : '.$facture['compte'],0,1);
        $pdf->Cell(25 ,6,'En renseignant votre numero de facture en communication libre',0,1);

        $pdf->setXY(10,120);
     
    }
    if($i>0)  {
        $analytique['analytique'] = str_replace([' ', '/','-'], '', $analytique['analytique']);
        $pdf->Output(dirname(__DIR__, 1).'/facture/'.str_replace("/", "__", $_SESSION['client_actif'].'_'.$analytique['analytique']).'_'.date("Y_m_d H_i_s").'.pdf','F');
    }
 
 
}  
exit($responseHandler->sendResponse(true,'Factures générées'));
?>