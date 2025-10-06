<?php

require_once PRIVATE_PATH . '/classes/Table.php'; 
require_once PRIVATE_PATH . '/classes/PageLayout.php'; 
require_once PRIVATE_PATH . '/classes/createModalContent.php'; 
require_once PRIVATE_PATH . '/classes/Modal.php'; 
require_once PRIVATE_PATH . '/classes/Form.php'; 

$modal = new Modal(
    id: "HsDell", 
    title: "Suppresion HS", 
    body: '<b>Vous êtes sur le point de supprimer une HS.</b></br></br>',
    headerClass: "danger",
    okayButtonClass: "primary",
    okayButtonText : "Supprimer",
    cancelButtonClass: "outline-secondary",
    showOkayButton: true,
    showButton : false
);
echo $modal->render();
createModalContent(
    formId: "hsEdit",
    inputs: [
        ['name' => 'Heures', 'type' => 'number', 'prefix' => 'hsEdit'],
    ],
    modalId: "modaleHsEdit",
    modalTitle: "Modification HS",
);

$table = new Table(title: "Liste des prestatations",
                  columns: ["Nom", 
                            "Prénom",
                            "Date",
                            "Prestation",
                            "HS",
                            "Commentaire",
                            "Statut"
                          ], 
                  id:"prestationListe");

try {
    $sql = "SELECT mpe.id AS prestation_id, mpe.*, mp.*    FROM moka_prestation_ebrigade mpe
                        JOIN moka_prestataire mp ON mpe.P_ID = mp.p_id
                        WHERE mpe.client_id =:client_id
                        ORDER BY mpe.date DESC";
    $stmt = $dbh->prepare($sql);
    $stmt -> bindParam(':client_id', $_SESSION['client_actif']);
    $stmt->execute();
    $prestations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($prestations as $prestation) {

        $status = match ($prestation['statut']) {
            0=> '<div class="btn-group" role="group" aria-label="Basic example">            
                <button id="'.$prestation['prestation_id'].'BtnAtt"class="btn btn-primary"   type="button"   onclick="node(\'moka_prestation_change_statut\', {prestationId : \''.$prestation['prestation_id'].'\', statut : \'0\'})">En att.</button>            
                <button id="'.$prestation['prestation_id'].'BtnValider" class="btn btn-secondary" type="button"   onclick="node(\'moka_prestation_change_statut\', {prestationId : \''.$prestation['prestation_id'].'\', statut : \'1\'})">Valider</button>            
                <button id="'.$prestation['prestation_id'].'BtnRefuser" class="btn btn-secondary" type="button"   onclick="node(\'moka_prestation_change_statut\', {prestationId : \''.$prestation['prestation_id'].'\', statut : \'2\'})">Refuser</button>
                <button id="'.$prestation['prestation_id'].'BtnRefuser" class="btn btn-secondary" type="button"   onclick="node(\'moka_prestation_change_statut\', {prestationId : \''.$prestation['prestation_id'].'\', statut : \'4\'})">Encodé  EB</button>
            </div>',
            1 => '<div class="btn-group" role="group" aria-label="Basic example">            
            <button id="'.$prestation['prestation_id'].'BtnAtt"class="btn btn-secondary"   type="button"   onclick="node(\'moka_prestation_change_statut\', {prestationId : \''.$prestation['prestation_id'].'\', statut : \'0\'})">En att.</button>            
            <button id="'.$prestation['prestation_id'].'BtnValider" class="btn btn-success" type="button"   onclick="node(\'moka_prestation_change_statut\', {prestationId : \''.$prestation['prestation_id'].'\', statut : \'1\'})">Valider</button>            
            <button id="'.$prestation['prestation_id'].'BtnRefuser" class="btn btn-secondary" type="button"   onclick="node(\'moka_prestation_change_statut\', {prestationId : \''.$prestation['prestation_id'].'\', statut : \'2\'})">Refuser</button>
            <button id="'.$prestation['prestation_id'].'BtnRefuser" class="btn btn-secondary" type="button"   onclick="node(\'moka_prestation_change_statut\', {prestationId : \''.$prestation['prestation_id'].'\', statut : \'4\'})">Encodé EB</button>
        </div>',
            2 => '<div class="btn-group" role="group" aria-label="Basic example">            
            <button id="'.$prestation['prestation_id'].'BtnAtt"class="btn btn-secondary"   type="button"   onclick="node(\'moka_prestation_change_statut\', {prestationId : \''.$prestation['prestation_id'].'\', statut : \'0\'})">En att.</button>            
            <button id="'.$prestation['prestation_id'].'BtnValider" class="btn btn-secondary" type="button"   onclick="node(\'moka_prestation_change_statut\', {prestationId : \''.$prestation['prestation_id'].'\', statut : \'1\'})">Valider</button>            
            <button id="'.$prestation['prestation_id'].'BtnRefuser" class="btn btn-danger" type="button"   onclick="node(\'moka_prestation_change_statut\', {prestationId : \''.$prestation['prestation_id'].'\', statut : \'2\'})">Refuser</button>
            <button id="'.$prestation['prestation_id'].'BtnRefuser" class="btn btn-secondary" type="button"   onclick="node(\'moka_prestation_change_statut\', {prestationId : \''.$prestation['prestation_id'].'\', statut : \'4\'})">Encodé EB</button>
        </div>',
            3 => 'Appliquée',
            4 => '<div class="btn-group" role="group" aria-label="Basic example">            
            <button id="'.$prestation['prestation_id'].'BtnAtt"class="btn btn-secondary"   type="button"   onclick="node(\'moka_prestation_change_statut\', {prestationId : \''.$prestation['prestation_id'].'\', statut : \'0\'})">En att.</button>            
            <button id="'.$prestation['prestation_id'].'BtnValider" class="btn btn-secondary" type="button"   onclick="node(\'moka_prestation_change_statut\', {prestationId : \''.$prestation['prestation_id'].'\', statut : \'1\'})">Valider</button>            
            <button id="'.$prestation['prestation_id'].'BtnRefuser" class="btn btn-secondary" type="button"   onclick="node(\'moka_prestation_change_statut\', {prestationId : \''.$prestation['prestation_id'].'\', statut : \'2\'})">Refuser</button>
            <button id="'.$prestation['prestation_id'].'BtnRefuser" class="btn btn-warning" type="button"   onclick="node(\'moka_prestation_change_statut\', {prestationId : \''.$prestation['prestation_id'].'\', statut : \'4\'})">Encodé dans EB</button>
        </div>',
            default => 'Erreur, statut inconnu !',
        };

        $table->addRow(
            [ "Nom" => $prestation['nom'], 
              "Prénom" => $prestation['prenom'],
              "Date" => $prestation['date'],
              "Prestation" => $prestation['E_LIBELLE'],
              "HS" => $prestation['heure'],
              "Commentaire" => $prestation['commentaire'],
              "Statut" => $status
             ],
            [
               
                ["name" => "Edit", "link" => "node('moka_hs_edit', {prestationId : '".$prestation['prestation_id']."',nbHeure : '".$prestation['heure']."'})", "class" => "primary"],
                ["name" => "Remove", "link" => "node('moka_prestation_dell', {prestationId : '".$prestation['prestation_id']."'})", "class" => "danger"],
            ],
            [
                "Statut" => ["id" => "statut".$prestation['prestation_id']]
            ]
        );
    }
} catch (PDOException $e) {
    echo "Erreur lors de la récupération des clients : " . $e->getMessage();
    exit;
}



$layout = new PageLayout();
$layout->addElement($table->render()); 
echo $layout->render();
  ?>