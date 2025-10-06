      
       <?php
       $page_titre ='Admin';
       $page_sous_titre = 'Default';
       if (isset($page_titre)){
              echo '<h2 class="mb-2 lh-sm">'.$page_titre.'</h2>';
          }
          if (isset($page_sous_titre)){
              echo ' <p class="text-700 lead mb-2">'.$page_sous_titre.'</p>';
          }
      require_once PRIVATE_PATH . '/classes/Form.php'; 
    
      // Créer une instance de la classe Form
      $form = new Form('myForm', 'myFormName', 'POST',  PRIVATE_PATH .'/module/test/action/test.php', 'Mon Formulaire');
      
      // Ajouter des champs au formulaire
      $form->addField('text', 'login', 'login', 'login');
      $form->addField('password', 'password', 'password', 'password');
      $form->addField('nom', 'nom', 'nom', 'nom');
      $form->addField('prenom', 'prenom', 'prenom', 'prenom');
      $form->addField('email', 'email', 'email', 'email');
      
      // Ajouter un bouton de soumission
      $form->setSubmitButton('submit', 'submitButton', 'submitValue', 'Envoyer');
      
      // Générer le HTML du formulaire et l'afficher
      echo $form->render();
    
      print_r($_SESSION);

       ?>
        
                   

                    