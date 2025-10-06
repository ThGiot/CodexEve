// On va avoir besoin de la class toast
import Toast from '../classes/Toast.js';
export function clientUserStatut(params) {
    const { clientId, userId, statut } = params;
  
    // Création de l'objet contenant les données à envoyer
    const dataToSend = {
      client_id: clientId,
      user_id: userId,
      statut: statut,
      node : 'action',
      action : 'client_user_statut'
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
    // console.log('Réponse brute:', rawResponse); // DEBOGAGE SI NECESSAIRE => Enregistrement de la réponse brute dans la console
  
      // Conversion de la réponse brute en objet JSON pour continuer à la traiter
      return JSON.parse(rawResponse);
    })
    .then(data => {
      // Vérification de la réponse
      
      if (data.success) {
        // Actions JS à effectuer si tout s'est bien passé
        if(statut == 'add'){
            const message = "utilisateur ajouté";
            const options = {
                position: 'top-right', // Par exemple, 'top-right', 'bottom-center', etc.
                actionButton: false,    // Si vous voulez afficher le bouton "Take action"
                closeButton: true,     // Si vous voulez afficher le bouton "Close"
                //backgroundColor: 'red', // Couleur de fond que vous souhaitez utiliser
                delay: 2000
                };
                const myToast = new Toast(message, options);
                document.getElementById('statut_'+userId).innerHTML = 'Activé';
                document.getElementById('statut_'+userId).className = 'badge badge-phoenix fs--2 badge-phoenix-success';
                var element = document.getElementById('switch_' + userId);
                element.innerHTML = 'Remove';
                element.className = 'dropdown-item text-danger';
                element.removeEventListener('click', element.clickHandler); // Supprimer l'ancien gestionnaire d'événements si nécessaire
                element.clickHandler = function() {
                node('clientUserStatut', {clientId: clientId, userId: userId, statut: 'remove'});
                };
                element.addEventListener('click', element.clickHandler);
        }
        if(statut == 'remove'){
            const message = "utilisateur enlevé";
            const options = {
                position: 'top-right', // Par exemple, 'top-right', 'bottom-center', etc.
                actionButton: false,    // Si vous voulez afficher le bouton "Take action"
                closeButton: true,     // Si vous voulez afficher le bouton "Close"
                //backgroundColor: 'red', // Couleur de fond que vous souhaitez utiliser
                delay: 2000
                };
                const myToast = new Toast(message, options);
                document.getElementById('statut_'+userId).innerHTML = 'Désactivé';
                document.getElementById('statut_'+userId).className = 'badge badge-phoenix fs--2 badge-phoenix-danger';
                var element = document.getElementById('switch_' + userId);
                document.getElementById('statut_' + userId).innerHTML = 'Désactivé';
                document.getElementById('statut_' + userId).className = 'badge badge-phoenix fs--2 badge-phoenix-danger';
                element.innerHTML = 'Activer';
                element.className = 'dropdown-item text-success';
                element.removeEventListener('click', element.clickHandler); // Supprimer l'ancien gestionnaire d'événements si nécessaire
                element.clickHandler = function() {
                node('clientUserStatut', {clientId: clientId, userId: userId, statut: 'add'});
                };
                element.addEventListener('click', element.clickHandler);
        }
       
        
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
  