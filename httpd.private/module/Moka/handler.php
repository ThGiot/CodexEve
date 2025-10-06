<?php
require 'config.php';
require_once __DIR__ . '/../../config.php';
require __DIR__ . '/../../sql.php';
require __DIR__ . '/../../module/auth.php';
if($auth == false) exit('echec auth.handler in module Moka');
require __DIR__ . '/../../module/permissions.php';


switch($permissions){
   
    case array (1,'10') : 
    case array (2,'10') : 
        require 'page/liste_prestataire.php';
    break;

    case array (1,'101') : 
    case array (2,'101') : 
        require 'page/prestataire_vieuw.php';
    break;

    case array (1,'11') : 
    case array (2,'11') : 
        require 'page/prestataire_add.php';
    break;

    case array (1,'20') : 
    case array (2,'20') : 
        require 'page/facture_import.php';
    break;

    case array (1,'21') : 
    case array (2,'21') : 
        require 'page/generer.php';
    break;

    case array (1,'22') : 
    case array (2,'22') : 
        require 'page/analytique.php';
    break;

    case array (1,'23') : 
    case array (2,'23') : 
        require 'page/activite.php';
    break;
    
    case array (1,'202') : 
    case array (2,'202') : 
        require 'page/activite_config.php';
    break;

    case array (1,'30') : 
    case array (2,'30') : 
        require 'page/prestation_liste_admin.php';
    break;

    case array (1,'31') : 
    case array (2,'31') : 
    case array (3,'31') : 
        require 'page/prestation_add.php';
    break;

    case array (1,'40') : 
    case array (2,'40') : 
        require 'page/facture_liste.php';
    break;

    case array (1,'41') : 
    case array (2,'41') : 
        require 'page/facture_add.php';
    break;

    case array (1,'42') : 
    case array (2,'42') : 
        require 'page/facture_correction.php';
    break;

    case array (1,'43') : 
    case array (2,'43') : 
        require 'action/facture_get.php';
    break;

    case array (1,'401') : 
    case array (2,'401') : 
        require 'page/facture_manage.php';
    break;

    case array (1,'43') : 
    case array (2,'43') : 
        require 'page/correction_archive.php';
    break;
    case array (1,'44') : 
    case array (2,'44') : 
        require 'page/facture_attente.php';
    break;

    case array (1,'401') : 
    case array (2,'401') : 
        require 'page/facture_get.php';
    break;
    default:
    case array (1,'1') : 
    case array (2,'1') : 
    case array (3,'1') : 
        require 'page/user_facture.php';
    break;

    case array (1,'2') : 
    case array (2,'2') : 
    case array (3,'2') : 
        require 'page/user_information.php';
    break;

    case array (3,'3') : 
        require 'page/prestation_liste_user.php';
    break;

    
 
}

?>