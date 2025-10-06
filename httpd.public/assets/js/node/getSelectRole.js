export function getSelectRole(params){
  const { clientId, userId, moduleId } = params;
   console.log(params);
    const dataToSend = {
        node : 'action',
        action : 'module_manage_get_select',
        user_id : userId,
        module_id : moduleId,
        client_id : clientId
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
          const element= document.getElementById('role_'+userId);
          // Actions JS à effectuer si tout s'est bien passé
         // console.log(data.message); //
          document.getElementById('role_'+userId).innerHTML = data.message;
          document.getElementById('role_'+userId).clickHandler = function() {
            
            };
            
          document.getElementById('role_'+userId).addEventListener('click', element.clickHandler);
         
          
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
    