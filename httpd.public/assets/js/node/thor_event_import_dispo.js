

import FetchHandler from '../classes/FetchHandler.js';
import Toast from '../classes/Toast.js';
const fetcher = new FetchHandler(true);

export function thor_event_import_dispo(params) {
  const {formId,evenementId } = params;
  const buttonSubmit = document.getElementById('dispoImportOkayButton');
  buttonSubmit.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Chargement...';
 
    const fileInput = document.getElementById('dispofichier'); // Utilisez l'ID pour sélectionner l'élément img


   
    const fetchHandler = new FetchHandler(true);

  
    // Créer un objet FormData à partir de l'élément form
    const formData = new FormData();
    formData.append("file", fileInput.files[0]);
  
    // Spécifiez des paramètres supplémentaires si nécessaire
    formData.append("node", "action");
    formData.append("action", "event_import_dispo");
    formData.append("evenement_id", evenementId);
  
    
    fetchHandler.sendRequest(formData)
        .then(response => {
            console.log(response);
            if(response.success == true){
                const message = response.message;
                const options = {
                position: 'top-right', 
                actionButton: false,    
                closeButton: true,     
                //backgroundColor: 'red', // Couleur de fond que vous souhaitez utiliser
                delay: 2000
                };
                const myToast = new Toast(message, options);
                buttonSubmit.innerHTML = "Enregistrer";

            }else{
                const message = "Une erreur s'est produite "+response.message;
                const options = {
                position: 'top-right', 
                actionButton: false,    
                closeButton: true,     
                //backgroundColor: 'red', // Couleur de fond que vous souhaitez utiliser
                delay: 4000
                };
                const myToast = new Toast(message, options);
                buttonSubmit.innerHTML = "Enregistrer";
            }
        })
        .catch(error => {
            console.error("Erreur lors de l'envoi de la requête:", error);
        });

}


