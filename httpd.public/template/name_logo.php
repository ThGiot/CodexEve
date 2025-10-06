<nav class="navbar navbar-top fixed-top navbar-expand" id="navbarDefault">
        <div class="collapse navbar-collapse justify-content-between">
          <div class="navbar-logo">

            <button class="btn navbar-toggler navbar-toggler-humburger-icon hover-bg-transparent" type="button" data-bs-toggle="collapse" data-bs-target="#navbarVerticalCollapse" aria-controls="navbarVerticalCollapse" aria-expanded="false" aria-label="Toggle Navigation"><span class="navbar-toggle-icon"><span class="toggle-line"></span></span></button>
            <a class="navbar-brand me-1 me-sm-3" href="index.php">
              <div class="d-flex align-items-center">
                <div class="d-flex align-items-center"><img src="<?php echo APPLI_LOGO; ?>" alt="Eve" width="27" />
                  <p class="logo-text ms-2 d-none d-sm-block"><?php echo APPLI_NAME; ?></p>
                </div>
              </div>
            </a>


          </div><ul class="pagination mb-0">
                  <li class="page-item">
                    <a class="page-link" onclick="goBack()">
                   <span class="fas fa-chevron-left"> </span>
                    </a>
                      </li>
                      <li class="page-item">
                    <a class="page-link" onclick="refreshContent()">
                   <span class="fa fa-refresh"> </span>
                    </a>
                      </li>
                      <li class="page-item">
                    <a class="page-link" onclick="goForward()">
                   <span class="fas fa-chevron-right"> </span>
                    </a>
                      </li>

</ul>
          <div class="col-md-2">                     

                      <select id="client" class="form-select form-select-sm" aria-label=".form-select-sm example" onchange="clientChange(this);">
                      <?php 
                      
// Récupération les Client de l'utilisateur
$query = "  SELECT uc.client_id as client_id, c.nom as nom
FROM user_client uc
JOIN client c
ON c.id = uc.client_id
WHERE user_id = :user_id";
$stmt = $dbh->prepare($query);
// Exécuter la requête en liant les paramètres
$stmt->bindParam(':user_id', $_SESSION['user']['id']);
$stmt->execute();
$client= $stmt->fetchAll(PDO::FETCH_ASSOC);

//Création des options pour la selection des clients disponibles
$option ='';
foreach ($client as $key => $value){
  $option .= '<option value="'.$value['client_id'].'"';
  if($_SESSION['client_actif'] == $value['client_id']) $option .= 'selected="selected"';
  $option .= '>'.$value['nom'].'</option>';

}



echo $option; ?>
                      </select>
</div>         
          <ul class="navbar-nav navbar-nav-icons flex-row">