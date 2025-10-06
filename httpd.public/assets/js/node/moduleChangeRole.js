export function moduleChangeRole(params, cancel = false) {
    const { clientId, userId, moduleId } = params;
    var selectElement = document.getElementById('selectRole_'+userId);
    var roleId = selectElement.value;
    var roleNom = selectElement.options[selectElement.selectedIndex].text;

    if (cancel === true) {
        document.getElementById('role_'+userId).innerHTML = roleNom;
        return;
    }

    
    
   
    
   
    //On va sauvergarder le nouveau role_id

    const dataToSend = {
        node : 'action',
        action : 'module_update_user_role',
        user_id : userId,
        module_id : moduleId,
        client_id : clientId,
        role_id : roleId,
        cancel : cancel
      };
    
      // Envoi de la requête POST à node.php
      fetch('node.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(dataToSend) // Conversion de l'objet en JSON
      })
      .then(response => response.text()) // Récupération de la réponse brute en tant que chaîne
      .then(rawResponse => {
      //console.log('Réponse brute:', rawResponse); // DEBOGAGE SI NECESSAIRE => Enregistrement de la réponse brute dans la console
    
        // Conversion de la réponse brute en objet JSON pour continuer à la traiter
        return JSON.parse(rawResponse);
      })
      .then(data => {
        // Vérification de la réponse
        
        if (data.success) {
            document.getElementById('role_'+userId).innerHTML = roleNom;
            //alert('Success : ' + data.message);
          
          // Autres actions ici...
        } else {
          // Gestion des erreurs si quelque chose ne s'est pas bien passé
          alert('Erreur : ' + data.message);
        }
      })
      .catch(error => {
        // Gestion des erreurs de réseau ou autres problèmes
        console.error('Une erreur est survenue :', error);
      });
    }