<?php
require_once PRIVATE_PATH . '/classes/Table.php'; 
require_once PRIVATE_PATH . '/classes/PageLayout.php'; 
require_once PRIVATE_PATH . '/classes/Form.php'; 
require_once PRIVATE_PATH . '/classes/Modal.php'; 



// Récupération des prestations côté ebrigade.
$today = date("Y-m-d");
if(date('d')<= 15){
    $jour = date('Y-m');
    $jour = $jour.'-01';
    $dateDeb = new DateTime($jour); 
    $jour = $dateDeb -> modify('-1 day');
    $jour = $dateDeb -> format('Y-m-d');
}else{
	$jour = date('Y-m');
    $jour = $jour.'-15';
}
$data = array(
    "token" => MEDTEAM_EB_API_KEY,
    "dDebut" => $jour,
    "dFin" => $today,

);
$payload  = json_encode($data);
$ch = curl_init(MEDTEAM_EB_API_URL);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_ENCODING, 'UTF-8');
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$result = curl_exec($ch);
print curl_error($ch);
$info = curl_getinfo($ch);
curl_close($ch);


//Récupération du Role_id de l'utilisateur
$module_liste = $_SESSION['module_liste'];
$active_module_id = $_SESSION['module_actif'];
$module_ids = array_column($module_liste, 'module_id');
$index = array_search($active_module_id, $module_ids);
$role_id = $module_liste[$index]['role_id'];



//récupération du p_id de l'utilisateur
$sql = "SELECT * FROM moka_prestataire WHERE user_id = :user_id AND client_id= :client_id";
$stmt = $dbh->prepare($sql);
$stmt ->bindParam(':user_id', $_SESSION['user']['id']);
$stmt ->bindParam(':client_id', $_SESSION['client_actif']);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);



//Traitement des résultat reçus par EB.
$result = json_decode($result, true);
$prestation_options=[];
foreach( $result as $value ){

    if((ucfirst($value['P_NOM']) == ucfirst($_SESSION['user']['nom']) AND ucfirst($value['P_PRENOM']) == ucfirst($_SESSION['user']['prenom'])) OR $role_id == 1 OR $value['P_ID'] == $user['p_id']){
          
            $prestation_options[] = [
                'value' => $value['E_CODE'].'/'.$value['TE_CODE'].'/'.$value['P_ID'].'/'.$value['E_LIBELLE'].'/'.$value['EH_DATE_DEBUT'], 
                'text' => $value['E_LIBELLE'].'  '.$value['EH_DATE_DEBUT'].' | '.ucfirst($value['P_PRENOM']).' '.ucfirst($value['P_NOM'])
            ];
        
    }
    
}

$onsubmit = 'event.preventDefault(); node(\'moka_prestation_add\', {})';
$form = new Form(   id: 'addPrestation', 
                    name: 'addPrestation',
                    method: 'POST', 
                    action: $onsubmit, 
                    title: 'Enregistrer des heures suplémentaires'
                );

$form->addField(
    type: 'select', 
    id: 'prestation',
    name: 'prestation', 
    label: 'Prestation',
    options: $prestation_options
);


$form->addField(
    type: 'number', 
    id: 'nb',
    name: 'nb', 
    label: 'Nombre d\'heure',
    placeholder:'1'
);

$form->addField(
    type: 'text', 
    id: 'comment',
    name: 'comment', 
    label: 'Commentaire',
    placeholder:'Justification'
);

$form->setSubmitButton(id: 'buttonSubmit',name: 'submit',value: 'send',text: 'Enregistrer');
$layout = new PageLayout();
$layout->addElement($form->render(),8); 
echo $layout->render();
?>