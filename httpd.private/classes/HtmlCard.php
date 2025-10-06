<?php

class HtmlCard {
    
    public function createClientCard($clientName, $creationDate, $activeModule, $users, $imgUrl,$onchange='') {
      if(LOCAL == true)$imgUrl = PUBLIC_PATH . '' . $imgUrl;
        $html = '<div class="col-12 col-md-7 col-xxl-12 mb-xxl-3">';
        $html .= '<div class="card h-100">';
        $html .= '<div class="card-body d-flex flex-column justify-content-between pb-3">';
        $html .= '<div class="row align-items-center g-5 mb-3 text-center text-sm-start">';
        $html .= '<div class="col-12 col-sm-auto mb-sm-2">';
        $html .= '<input class="d-none" id="avatarFile" type="file" onchange="' . $onchange . '"/> ';
        $html .= '<label class="cursor-pointer avatar avatar-5xl" for="avatarFile"><img id="avatarImg" class="rounded-circle" src="' .  $imgUrl . '" alt="" /></label>';
        $html .= '</div>';
        $html .= '<div class="col-12 col-sm-auto flex-1">';
        $html .= "<h3>$clientName</h3>";
        $html .= "<p class=\"text-800\">$creationDate</p>";
        $html .= '</div></div>';
        $html .= '<div class="d-flex flex-between-center border-top border-dashed border-300 pt-4">';
        $html .= '<div>';
        $html .= "<h6>Module actif</h6>";
        $html .= "<p class=\"fs-1 text-800 mb-0\">$activeModule</p>";
        $html .= '</div>';
        $html .= '<div>';
        $html .= "<h6>Utilisateurs</h6>";
        $html .= "<p class=\"fs-1 text-800 mb-0\">$users</p>";
        $html .= '</div>';
        $html .= '<div><h6></h6></div>';
        $html .= '</div></div></div></div>';

        return $html;
    }

    public function createAddressCard($address, $email, $phone) {
        $html = '<div class="col-12 col-md-5 col-xxl-12 mb-xxl-3">';
        $html .= '<div class="card h-100">';
        $html .= '<div class="card-body pb-3">';
        $html .= '<div class="d-flex align-items-center mb-3">';
        $html .= '<h3 class="me-1">Default Address</h3>';
        $html .= '<button class="btn btn-link p-0"><span class="fas fa-pen fs-0 ms-3 text-500"></span></button>';
        $html .= '</div>';
        $html .= "<h5 class=\"text-800\">Address</h5>";
        $html .= "<p class=\"text-800\">$address</p>";
        $html .= '<div class="mb-3">';
        $html .= "<h5 class=\"text-800\">Email</h5><a href=\"mailto:$email\">$email</a>";
        $html .= '</div>';
        $html .= "<h5 class=\"text-800\">Phone</h5><a class=\"text-800\" href=\"tel:$phone\">$phone</a>";
        $html .= '</div></div></div>';

        return $html;
    }

    public function createUserProfileCard($avatarUrl, $nom, $prenom,$onchange = null) {
      if(LOCAL == true)$avatarUrl = PUBLIC_PATH . '' . $avatarUrl;

        $html = '<div class="col-12 col-md-5 col-xxl-12 mb-xxl-3">';
       
       // $html .= '<div class="col-auto"><div class="row g-2 g-sm-3"><div class="col-auto"></div></div></div>';
        $html .= '</div>';
        $html .= '<div class="row g-3 mb-6">';
        $html .= '<div class="col-12 col-lg-8">';
        $html .= '<div class="card h-100">';
        $html .= '<div class="card-body">';
        $html .= '<div class="border-bottom border-dashed border-300 pb-4">';
        $html .= '<div class="row align-items-center g-3 g-sm-5 text-center text-sm-start">';
        $html .= '<div class="col-12 col-sm-auto">';
        $html .= '<input class="d-none" id="avatarFile" type="file" onchange="' . $onchange . '"/> ';
        $html .= '<label class="cursor-pointer avatar avatar-5xl" for="avatarFile"><img id="avatarImg" class="rounded-circle" src="' . $avatarUrl . '" alt="" /></label>';
        $html .= '</div>';
        $html .= '<div class="col-12 col-sm-auto flex-1">';
        $html .= "<h3>{$nom} {$prenom}</h3>";
        $html .= '</div></div></div>';
        $html .= '<br><button class="btn btn-phoenix-secondary" type="button" data-bs-toggle="modal" data-bs-target="#tooltipModal"><span class="fas fa-key me-2"></span>Reset password</button>';
       
        $html .= '</div></div></div></div>';
        $html .= '<div class="modal fade" id="tooltipModal" tabindex="-1" aria-labelledby="tooltipModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="tooltipModalLabel">Changement de mot de passe</h5>
              <button class="btn p-1" type="button" data-bs-dismiss="modal" aria-label="Close"><span class="fas fa-times fs--1"></span></button>
            </div>
            <div class="modal-body">
              <h5>Mot de passe actuel</h5>
              <div class="invalid-feedback" id="oldPassError">Ancien mot de passe incorrect</div>
              </br> <input type="password"  class="form-control" placeholder="mot de passe actuel" name="old_password" id="old_password">
              <hr />
              <h5>Nouveau mot de passe</h5>
              <div class="invalid-feedback" id="samePass">Les mots de passes sont différents</div>
              </br> <input  type="password" class="form-control" placeholder="nouveau mot de passe" name="new_password" id="new_password">
              </br> <input  type="password" class="form-control" placeholder="nouveau mot de passe" name="new_password2" id="new_password2">
            </div>
            <div class="modal-footer">
              <button class="btn btn-primary" type="button" onclick="node(\'selfModifPass\', {})">Enregistrer</button>
              <button id="closeModal"class="btn btn-outline-primary" type="button" data-bs-dismiss="modal">Annuler</button>
            </div>
          </div>
        </div>
      </div>
      ';
        return $html;
    }

    
}
?>