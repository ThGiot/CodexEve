<?php
require_once dirname(__DIR__, 3) . '/config.php';
require_once dirname(__DIR__, 3) . '/sql.php';
require_once dirname(__DIR__, 3) . '/classes/DBHandler.php';
require_once dirname(__DIR__, 3) . '/classes/PhoneNumberHandler.php';
require_once dirname(__DIR__, 3) . '/classes/ResponseHandler.php';
require_once dirname(__DIR__, 3) . '/classes/RequestHandler.php';
require dirname(__DIR__, 3) . '/vendor/autoload.php';

// Initialiser les gestionnaires de réponse et de requête
$requestHandler = new RequestHandler();
$responseHandler = new ResponseHandler();


$rules = [
    'facture_id' => ['type' => 'INT'],
];

$data = $requestHandler->handleRequest($data, $rules); 

    $pdf = new FPDF('P','mm','A4');
 
   $role = 1;
   $_SESSION['client_actif'] =3;
    
    
    
    $sql2 = "SELECT *, YEAR(date) AS annee FROM moka_facture WHERE client_id =:client_id AND id = :facture_id";
    $stmt2 = $dbh->prepare($sql2);
    $stmt2 -> bindParam(':client_id', $_SESSION['client_actif']);
    $stmt2 -> bindParam(':facture_id', $data['facture_id']);
    $stmt2->execute();
    $facture = $stmt2->fetch(PDO::FETCH_ASSOC);
    //Vérification de l'utilisateur est soit ADMIN soit peut acceder à cette facture 

    if($role >= 3){
        $sql = "SELECT * FROM moka_prestataire WHERE id = :id AND client_id= :client_id";
        $stmt = $dbh->prepare($sql);
        $stmt ->bindParam(':id', $facture['prestataire_id']);
        $stmt ->bindParam(':client_id', $_SESSION['client_actif']);
        $stmt->execute();
        $prestataire = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($prestataire['user_id'] != $_SESSION['user']['id'] AND $_SESSION['user']['role'] >2){
            exit("Authorisation insufisante pour consulter cette facture. Contacter un administrateur.");
        }

        
    }
  


        $pdf->AddPage();
        
        //On protège la facture
        $query3 = "UPDATE moka_facture SET protected = 1 WHERE id = :facture_id AND client_id =:client_id";
        $stmt3 = $dbh->prepare($query3);
        $stmt3->bindParam(':facture_id', $facture['id']);
        $stmt3->bindParam(':client_id',  $_SESSION['client_actif']);
        $stmt3->execute();

        //Traitement de l'encodage (merci FPDF)
        $nom = iconv('UTF-8', 'windows-1252', $facture['nom']);
        $prenom = iconv('UTF-8', 'windows-1252', $facture['prenom']);
        //RECUPERATION SOCIETE SI SOCIETE
        $adresse = iconv('UTF-8', 'windows-1252', $facture['adresse']);
        $act = iconv('UTF-8', 'windows-1252', 'Activité code : ');
       
        $date = $facture['date'];
        $analytique_string = str_replace(" ", "", $facture['analytique']);
        $ref = iconv('UTF-8', 'windows-1252', $analytique_string);
        
   
        //génération du numéro de facture
        $n = $facture['numero'];
        if ($n < 10){ $n = '00'.$n;}
        elseif($n <100){ $n = '0'.$n;}
        $year = $facture['annee'];
        $nfacture = $year.'-'.$n.'-MT';

    
        $pdf->SetFont('Arial','B',13);
        $pdf->Cell(170 ,4,'',0,0);
        $pdf->Cell(59 ,4,'FACTURE',0,1);
        
        if (!empty($analytique_string)){
        
            $pdf->SetFont('Arial','',10);
            $pdf->Cell(130 ,2,"",0,0);
            $pdf->Cell(50 ,5,' ' ,0,1);
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
        if(LOCAL == true) {$pdf->Image	(dirname(__DIR__, 4) .'/httpd.public/assets/img/g22.png', 10, 5, 25, 25, 'PNG');}
        if(LOCAL == false) {$pdf->Image	(dirname(__DIR__, 4) .'/httpd.www/assets/img/g22.png', 10, 5, 25, 25, 'PNG');}
        $pdf->SetFont('Arial','B',12);
        $pdf->Cell(90 ,5,'A L\'attention de ',0,0);
        $pdf->Cell(130 ,5,'Objet :',0,1);
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(90 ,5,'Croix-Rouge de Belgique',0,0);
        $pdf->Cell(25 ,5,$facture['designation'],0,1);
        $pdf->Cell(25 ,5,'Medical Team Bruxelles Capitale',0,1);
        $pdf->Cell(25 ,5,'Rue Rempart des Moines,',0,1);
        $pdf->Cell(25 ,5,'1000 Bruxelles',0,1);

        $pdf->SetFont('Arial','',10);
        $pdf->Cell(59 ,5,'',0,0);
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(189 ,10,'',0,1);

        $pdf->SetFont('Arial','B',10);
        /*Heading Of the table*/
        $pdf->Cell(135 ,6,'Designation',1,0,'C');
        $pdf->Cell(50 ,6,'Montant HT',1,1,'C');
        $pdf->SetFont('Arial','',7);
        
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
     
        
        $pdf->Output('D',''.$nfacture.' '.$nom.'.pdf');
  ?>
