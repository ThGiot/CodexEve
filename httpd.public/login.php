<?php
require_once __DIR__ . '/../httpd.private/config.php';
require_once PRIVATE_PATH . '/sql.php';
if(!isset($classe))$classe = 'success';
if(!isset($title))$title= "Inscription effectuée";



if (isset($_GET['activation_user']) && $_GET['activation_user'] == 'true' && isset($_GET['key'])) {
  // Récupération de la clé d'activation
  $activationKey = $_GET['key'];
  try {
      // Requête pour trouver l'utilisateur avec la clé d'activation correspondante
      $stmt = $dbh->prepare("SELECT user_id FROM user_activation_key WHERE activation_key = :activationKey");
      $stmt->bindParam(':activationKey', $activationKey, PDO::PARAM_STR);
      $stmt->execute();
      $user = $stmt->fetch(PDO::FETCH_ASSOC);
      if ($user) {
          // Utilisateur trouvé, activer le compte
          $stmt = $dbh->prepare("UPDATE user SET validated = 1 WHERE id = :userId");
          $stmt->bindParam(':userId', $user['user_id'], PDO::PARAM_INT);
          $stmt->execute();
          $title = '';
          $message = "Compte activé avec succès.";
      } else {
          // Clé d'activation non valide
          $title = 'Erreur';
          $message = "La clé d'activation fournie n'est pas valide.";
          $classe = 'danger';
      }
  } catch (PDOException $e) {
      //echo 'Erreur de connexion : ' . $e->getMessage();
  }
} else {
  // Paramètres GET non définis ou incorrects
 //echo "Demande d'activation invalide.";
}
if(isset($message)){
  require_once __DIR__ . '/../httpd.private/config.php';
  require_once PRIVATE_PATH . '/classes/Modal.php';
  $modal = new Modal(
    id: "messageModal", 
    title: $title, 
    body: $message,
    headerClass: $classe,
    okayButtonClass: "primary",
   // okayButtonText : "Supprimer",
   // cancelButtonClass: "outline-secondary",
    showOkayButton: false,
    showButton : false
  );
  echo $modal->render();

}
?>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    // Cette fonction s'exécutera une fois que la page sera complètement chargée
   
    var modal = new bootstrap.Modal(document.getElementById('messageModal'));
    
    // Vérifie si l'élément modal existe
    if (modal) {
        // Votre logique pour afficher la modal
        // Si vous utilisez Bootstrap, par exemple :
       modal.show();
    }
});
</script>
<!DOCTYPE html>
<html lang="en-US" dir="ltr">

  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">


    <!-- ===============================================-->
    <!--    Document Title-->
    <!-- ===============================================-->
    <title>Eve</title>


    <!-- ===============================================-->
    <!--    Favicons-->
    <!-- ===============================================-->
    <link rel="apple-touch-icon" sizes="180x180" href=" assets/img/favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href=" assets/img/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href=" assets/img/favicons/favicon-16x16.png">
    <link rel="shortcut icon" type="image/x-icon" href=" assets/img/favicons/favicon.ico">
    <link rel="manifest" href=" assets/img/favicons/manifest.json">
    <meta name="msapplication-TileImage" content=" assets/img/favicons/mstile-150x150.png">
    <meta name="theme-color" content="#ffffff">
    <script src=" vendors/imagesloaded/imagesloaded.pkgd.min.js"></script>
    <script src=" vendors/simplebar/simplebar.min.js"></script>
    <script src=" assets/js/config.js"></script>


    <!-- ===============================================-->
    <!--    Stylesheets-->
    <!-- ===============================================-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;600;700;800;900&amp;display=swap" rel="stylesheet">
    <link href=" vendors/simplebar/simplebar.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
    <link href=" assets/css/theme-rtl.min.css" type="text/css" rel="stylesheet" id="style-rtl">
    <link href=" assets/css/theme.min.css" type="text/css" rel="stylesheet" id="style-default">
    <link href=" assets/css/user-rtl.min.css" type="text/css" rel="stylesheet" id="user-style-rtl">
    <link href=" assets/css/user.min.css" type="text/css" rel="stylesheet" id="user-style-default">
    <script>
      var phoenixIsRTL = window.config.config.phoenixIsRTL;
      if (phoenixIsRTL) {
        var linkDefault = document.getElementById('style-default');
        var userLinkDefault = document.getElementById('user-style-default');
        linkDefault.setAttribute('disabled', true);
        userLinkDefault.setAttribute('disabled', true);
        document.querySelector('html').setAttribute('dir', 'rtl');
      } else {
        var linkRTL = document.getElementById('style-rtl');
        var userLinkRTL = document.getElementById('user-style-rtl');
        linkRTL.setAttribute('disabled', true);
        userLinkRTL.setAttribute('disabled', true);
      }
    </script>
  </head>


  <body>

    <!-- ===============================================-->
    <!--    Main Content-->
    <!-- ===============================================-->
    <main class="main" id="top">
      <div class="container-fluid bg-300 dark__bg-1200">
        <div class="bg-holder bg-auth-card-overlay" style="background-image:url( assets/img/bg/37.png);">
        </div>
        <!--/.bg-holder-->

        <div class="row flex-center position-relative min-vh-100 g-0 py-5">
          <div class="col-11 col-sm-10 col-xl-8">
            <div class="card border border-200 auth-card">
              <div class="card-body pe-md-0">
                <div class="row align-items-center gx-0 gy-7">
                  <div class="col-auto bg-100 dark__bg-1100 rounded-3 position-relative overflow-hidden auth-title-box">
                    <div class="bg-holder" style="background-image:url( assets/img/bg/38.png);">
                    </div>
                    <!--/.bg-holder-->

                    <div class="position-relative px-4 px-lg-7 pt-7 pb-7 pb-sm-5 text-center text-md-start pb-lg-7 pb-md-7">
                      <h3 class="mb-3 text-black fs-1">Eve Authentication</h3>
                      <p class="text-700">Bienvenue sur <i>Eve</i>, la nouvelle application d'Hygea. </p>
                      <ul class="list-unstyled mb-0 w-max-content w-md-auto mx-auto">
                        <li class="d-flex align-items-center"><span class="uil uil-check-circle text-success me-2"></span><span class="text-700 fw-semi-bold">Rapide</span></li>
                        <li class="d-flex align-items-center"><span class="uil uil-check-circle text-success me-2"></span><span class="text-700 fw-semi-bold">Simple</span></li>
                        <li class="d-flex align-items-center"><span class="uil uil-check-circle text-success me-2"></span><span class="text-700 fw-semi-bold">Responsive</span></li>
                        <li class="d-flex align-items-center"><span class="uil uil-check-circle text-success me-2"></span><span class="text-700 fw-semi-bold">Cookie Free</span></li>
                      </ul>
                    </div>
                    <div class="position-relative z-index--1 mb-6 d-none d-md-block text-center mt-md-15"><img class="auth-title-box-img d-dark-none" src=" assets/img/spot-illustrations/auth.png" alt="" /><img class="auth-title-box-img d-light-none" src=" assets/img/spot-illustrations/auth-dark.png" alt="" /></div>
                  </div>
                  <div class="col mx-auto">
                    <form action="index.php" method ="POST">
                    <div class="auth-form-box">
                      <div class="text-center mb-7"><a class="d-flex flex-center text-decoration-none mb-4" href=" index.html">
                          <div class="d-flex align-items-center fw-bolder fs-5 d-inline-block"><img src=" assets/img/icons/logo.png" alt="phoenix" width="58" />
                          </div>
                        </a>
                        <h3 class="text-1000">Log In</h3>
                        <p class="text-700">Get access to your account</p>
                      </div>
                      <button class="btn btn-phoenix-secondary w-100 mb-3" disabled><span class="fab fa-google text-danger me-2 fs--1"></span>Sign in with google</button>
                      <button class="btn btn-phoenix-secondary w-100"  disabled><span class="fab fa-facebook text-primary me-2 fs--1"></span>Sign in with facebook</button>
                      <div class="position-relative">
                        <hr class="bg-200 mt-5 mb-4" />
                        <div class="divider-content-center bg-white">or </div>
                      </div>
                      <div class="mb-3 text-start">
                        <label class="form-label" for="email">Login</label>
                        <div class="form-icon-container">
                          <input class="form-control form-icon-input" id="email" type="text" placeholder="Login" name="login" /><span class="fas fa-user text-900 fs--1 form-icon"></span>
                        </div>
                      </div>
                      <div class="mb-3 text-start">
                        <label class="form-label" for="password">Password</label>
                        <div class="form-icon-container">
                          <input class="form-control form-icon-input" id="password" type="password" name ="password" placeholder="Password" /><span class="fas fa-key text-900 fs--1 form-icon"></span>
                        </div>
                      </div>
                      <div class="row flex-between-center mb-7">
                        <div class="col-auto">
                          
                        </div>
                       <!--  <div class="col-auto"><a class="fs--1 fw-semi-bold" href=" pages/authentication/card/forgot-password.html">Forgot Password?</a></div> !-->
                      </div>
                      <button type="submit" id="submitButton" name="action" value="connexion_send" class="btn btn-primary w-100 mb-3">Log In</button>
                       <div class="text-center"><a class="fs--1 fw-bold" href="sign_up.php">S'inscrire !</a></div>
                       <div class="text-center"><a class="fs--1 fw-bold" href="lost_password.php">Mot de passe oublié</a></div>
                    </div>
                  </div>
                     </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <script>
        var navbarTopStyle = window.config.config.phoenixNavbarTopStyle;
        var navbarTop = document.querySelector('.navbar-top');
        if (navbarTopStyle === 'darker') {
          navbarTop.classList.add('navbar-darker');
        }

        var navbarVerticalStyle = window.config.config.phoenixNavbarVerticalStyle;
        var navbarVertical = document.querySelector('.navbar-vertical');
        if (navbarVertical && navbarVerticalStyle === 'darker') {
          navbarVertical.classList.add('navbar-darker');
        }
      </script>
     
      </div>
    </main>
    <!-- ===============================================-->
    <!--    End of Main Content-->
    <!-- ===============================================-->


    
    </div><a class="card setting-toggle" href="#settings-offcanvas" data-bs-toggle="offcanvas">
      <div class="card-body d-flex align-items-center px-2 py-1">
        <div class="position-relative rounded-start" style="height:34px;width:28px">
          <div class="settings-popover"><span class="ripple"><span class="fa-spin position-absolute all-0 d-flex flex-center"><span class="icon-spin position-absolute all-0 d-flex flex-center">
                  <svg width="20" height="20" viewBox="0 0 20 20" fill="#ffffff" xmlns="http://www.w3.org/2000/svg">
                    <path d="M19.7369 12.3941L19.1989 12.1065C18.4459 11.7041 18.0843 10.8487 18.0843 9.99495C18.0843 9.14118 18.4459 8.28582 19.1989 7.88336L19.7369 7.59581C19.9474 7.47484 20.0316 7.23291 19.9474 7.03131C19.4842 5.57973 18.6843 4.28943 17.6738 3.20075C17.5053 3.03946 17.2527 2.99914 17.0422 3.12011L16.393 3.46714C15.6883 3.84379 14.8377 3.74529 14.1476 3.3427C14.0988 3.31422 14.0496 3.28621 14.0002 3.25868C13.2568 2.84453 12.7055 2.10629 12.7055 1.25525V0.70081C12.7055 0.499202 12.5371 0.297594 12.2845 0.257272C10.7266 -0.105622 9.16879 -0.0653007 7.69516 0.257272C7.44254 0.297594 7.31623 0.499202 7.31623 0.70081V1.23474C7.31623 2.09575 6.74999 2.8362 5.99824 3.25599C5.95774 3.27861 5.91747 3.30159 5.87744 3.32493C5.15643 3.74527 4.26453 3.85902 3.53534 3.45302L2.93743 3.12011C2.72691 2.99914 2.47429 3.03946 2.30587 3.20075C1.29538 4.28943 0.495411 5.57973 0.0322686 7.03131C-0.051939 7.23291 0.0322686 7.47484 0.242788 7.59581L0.784376 7.8853C1.54166 8.29007 1.92694 9.13627 1.92694 9.99495C1.92694 10.8536 1.54166 11.6998 0.784375 12.1046L0.242788 12.3941C0.0322686 12.515 -0.051939 12.757 0.0322686 12.9586C0.495411 14.4102 1.29538 15.7005 2.30587 16.7891C2.47429 16.9504 2.72691 16.9907 2.93743 16.8698L3.58669 16.5227C4.29133 16.1461 5.14131 16.2457 5.8331 16.6455C5.88713 16.6767 5.94159 16.7074 5.99648 16.7375C6.75162 17.1511 7.31623 17.8941 7.31623 18.7552V19.2891C7.31623 19.4425 7.41373 19.5959 7.55309 19.696C7.64066 19.7589 7.74815 19.7843 7.85406 19.8046C9.35884 20.0925 10.8609 20.0456 12.2845 19.7729C12.5371 19.6923 12.7055 19.4907 12.7055 19.2891V18.7346C12.7055 17.8836 13.2568 17.1454 14.0002 16.7312C14.0496 16.7037 14.0988 16.6757 14.1476 16.6472C14.8377 16.2446 15.6883 16.1461 16.393 16.5227L17.0422 16.8698C17.2527 16.9907 17.5053 16.9504 17.6738 16.7891C18.7264 15.7005 19.4842 14.4102 19.9895 12.9586C20.0316 12.757 19.9474 12.515 19.7369 12.3941ZM10.0109 13.2005C8.1162 13.2005 6.64257 11.7893 6.64257 9.97478C6.64257 8.20063 8.1162 6.74905 10.0109 6.74905C11.8634 6.74905 13.3792 8.20063 13.3792 9.97478C13.3792 11.7893 11.8634 13.2005 10.0109 13.2005Z" fill="#2A7BE4"></path>
                  </svg></span></span></span></div>
        </div>
      </div>
    </a>


    <!-- ===============================================-->
    <!--    JavaScripts-->
    <!-- ===============================================-->
    <script src=" vendors/popper/popper.min.js"></script>
    <script src=" vendors/bootstrap/bootstrap.min.js"></script>
    <script src=" vendors/anchorjs/anchor.min.js"></script>
    <script src=" vendors/is/is.min.js"></script>
    <script src=" vendors/fontawesome/all.min.js"></script>
    <script src=" vendors/lodash/lodash.min.js"></script>
    <script src="https://polyfill.io/v3/polyfill.min.js?features=window.scroll"></script>
    <script src=" vendors/list.js/list.min.js"></script>
    <script src=" vendors/feather-icons/feather.min.js"></script>
    <script src=" vendors/dayjs/dayjs.min.js"></script>
    <script src=" assets/js/phoenix.js"></script>
    <script>document.addEventListener('DOMContentLoaded', function() {
  var submitButton = document.getElementById('submitButton');
  
  document.addEventListener('keydown', function(event) {
    if (event.key === 'Enter') {
      event.preventDefault(); // Empêche le comportement par défaut
      submitButton.click(); // Clique sur le bouton de soumission
    }
  }); 
});</script>
  </body>

</html>