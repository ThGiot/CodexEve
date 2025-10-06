<?php
set_time_limit(30000); // 5 minutes par exemple

$clef ='mklnzeijnhje7Y78392h984894za6515698zarzzefsfesfeszzéae';
$url = "https://medteam.be/migre.php";
require_once __DIR__ . '/../httpd.private/config.php';
    require_once PRIVATE_PATH . '/sql.php';

// Les données à envoyer
/*
$donnees = [
    'clef' => $clef,
    'table' => 'facture'
];

// Initialisation de cURL
$ch = curl_init();

// Configuration des options de cURL
curl_setopt($ch, CURLOPT_URL, $url . '?' . http_build_query($donnees));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Exécution de la requête cURL
$response = curl_exec($ch);

// Vérification des erreurs
if (curl_errno($ch)) {
    echo 'Erreur cURL : ' . curl_error($ch);
} else {
    // Traitement de la réponse
    $resultats = json_decode($response, true);

    

    echo'<pre>';
   // print_r($resultats);
    echo'</pre>';

    foreach($resultats as $data){

        $query = "INSERT INTO  moka_facture (   id, 
                                                designation, 
                                                montant, 
                                                date, 
                                                numero,
                                                analytique,
                                                nom,
                                                prenom,
                                                niss,
                                                bce,
                                                compte,
                                                adresse,
                                                prestataire_id,
                                                client_id,
                                                protected) VALUES(
                                                    
                                                    :id, 
                                                :designation, 
                                                :montant, 
                                                :date, 
                                                :numero,
                                                :analytique,
                                                :nom,
                                                :prenom,
                                                :niss,
                                                :bce,
                                                :compte,
                                                :adresse,
                                                :prestataire_id,
                                                3,
                                                0)";
        $stmt = $dbh->prepare($query);
    
        $stmt->bindParam(':id', $data['id']);
        $stmt->bindParam(':designation', $data['designation']);
        $stmt->bindParam(':montant', $data['montant']);
        $stmt->bindParam(':date', $data['year']);
        $stmt->bindParam(':numero', $data['num']);
        $stmt->bindParam(':analytique', $data['analytique']);
        $stmt->bindParam(':nom', $data['nom']);
        $stmt->bindParam(':prenom', $data['prenom']);
        $stmt->bindParam(':niss', $data['NISS']);
        $stmt->bindParam(':bce', $data['bce']);
        $stmt->bindParam(':compte', $data['compte']);
        $stmt->bindParam(':adresse', $data['adresse']);
        $stmt->bindParam(':prestataire_id', $data['id_user']);
       try {
        $stmt->execute();
    } catch (Exception $e) {
        echo "Erreur lors de l'insertion d'une ligne : " . $e->getMessage();
        // Vous pouvez également enregistrer l'erreur dans un fichier de log ou effectuer d'autres actions.
    }
        
    }
}

// Fermeture de la session cURL
curl_close($ch);

$donnees = [
    'clef' => $clef,
    'table' => 'facture_detail'
];

// Initialisation de cURL
$ch = curl_init();

// Configuration des options de cURL
curl_setopt($ch, CURLOPT_URL, $url . '?' . http_build_query($donnees));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Exécution de la requête cURL
$response = curl_exec($ch);
$resultats = json_decode($response, true);
foreach($resultats as $data){

    $query = "INSERT INTO  moka_facture_detail (    
                                            facture_id, 
                                            designation, 
                                            montant
                                            ) VALUES(
                                                
                                               
                                            :facture_id,
                                            :designation, 
                                            :montant)";
    $stmt = $dbh->prepare($query);

    
    $stmt->bindParam(':facture_id', $data['facture_id']);
    $stmt->bindParam(':designation', $data['designation']);
    $stmt->bindParam(':montant', $data['montant']);
    try {
        $stmt->execute();
    } catch (Exception $e) {
        echo "Erreur lors de l'insertion d'une ligne : " . $e->getMessage().'</br>';
        // Vous pouvez également enregistrer l'erreur dans un fichier de log ou effectuer d'autres actions.
    }
    
}
curl_close($ch);

$donnees = [
    'clef' => $clef,
    'table' => 'user'
];

// Initialisation de cURL
$ch = curl_init();

// Configuration des options de cURL
curl_setopt($ch, CURLOPT_URL, $url . '?' . http_build_query($donnees));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$resultats = json_decode($response, true);
foreach ($resultats as $data) {
    $query = "INSERT INTO moka_prestataire (
        client_id, 
        id, 
        nom, 
        prenom, 
        societe, 
        niss, 
        inami, 
        adresse, 
        telephone, 
        email, 
        compte, 
        bce, 
        p_id, 
        grade, 
        section
    ) VALUES (
        3, 
        :id, 
        :nom, 
        :prenom, 
        :societe, 
        :niss, 
        :inami, 
        :adresse, 
        :telephone, 
        :email, 
        :compte, 
        :bce, 
        :p_id, 
        :grade, 
        :section
    )";
    
    $stmt = $dbh->prepare($query);

    // Bind des valeurs. Remplacer 'NULL' par des valeurs par défaut si nécessaire.
    $stmt->bindParam(':id', $data['id']); // ou une autre valeur si différente
    $stmt->bindParam(':nom', $data['nom']);
    $stmt->bindParam(':prenom', $data['prenom']);
    $stmt->bindParam(':societe', $societeValue); // Remplacer par une valeur appropriée
    $stmt->bindParam(':niss', $data['NISS']);
    $stmt->bindParam(':inami', $data['inami']); // Remplacer par une valeur appropriée
    $stmt->bindParam(':adresse', $data['adresse']);
    $stmt->bindParam(':telephone', $data['gsm']); // Remplacer par une valeur appropriée
    $stmt->bindParam(':email', $data['mail']); // Remplacer par une valeur appropriée
    $stmt->bindParam(':compte', $data['compte']);
    $stmt->bindParam(':bce', $data['bce']);
    $stmt->bindParam(':p_id', $data['P_ID']); // Remplacer par une valeur appropriée
    $stmt->bindParam(':grade', $data['P_GRADE']); // Remplacer par une valeur appropriée
    $stmt->bindParam(':section', $data['section']); // Remplacer par une valeur appropriée

    try {
        $stmt->execute();
    } catch (Exception $e) {
        echo "Erreur lors de l'insertion d'une ligne : " . $e->getMessage().'</br>';
        // Vous pouvez également enregistrer l'erreur dans un fichier de log ou effectuer d'autres actions.
    }
}

curl_close($ch);
*/
$donnees = [
    'clef' => $clef,
    'table' => 'prestation_hs'
];

// Initialisation de cURL
$ch = curl_init();

// Configuration des options de cURL
curl_setopt($ch, CURLOPT_URL, $url . '?' . http_build_query($donnees));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$resultats = json_decode($response, true);
foreach ($resultats as $data) {
    $query = "INSERT INTO moka_prestation_ebrigade (
       
        client_id, 
        E_CODE,
        P_ID,
        heure,
        commentaire,
        statut,
        E_LIBELLE,
        date,
        timestamp
    ) VALUES (
         
        
        3,
        :E_CODE,
        :P_ID,
        :heure,
        :commentaire,
        :statut,
        :E_LIBELLE,
        :date,
        :timestamp
    )";
    
    $stmt = $dbh->prepare($query);
    if($data['statut'] == 4)$data['statut'] = 3;
    // Bind des valeurs. Remplacer 'NULL' par des valeurs par défaut si nécessaire.
    $stmt->bindParam(':E_CODE', $data['E_CODE']);
    $stmt->bindParam(':P_ID', $data['P_ID']);
    $stmt->bindParam(':heure', $data['heure']);
    $stmt->bindParam(':commentaire', $data['commentaire']);
    $stmt->bindParam(':statut', $data['statut']);
    $stmt->bindParam(':E_LIBELLE', $data['E_LIBELLE']);
    $stmt->bindParam(':date', $data['date']);
    $stmt->bindParam(':timestamp', $data['timestamp']);

    try {
        $stmt->execute();
    } catch (Exception $e) {
        echo "Erreur lors de l'insertion d'une ligne : " . $e->getMessage().'</br>';
        // Vous pouvez également enregistrer l'erreur dans un fichier de log ou effectuer d'autres actions.
    }
}

?>