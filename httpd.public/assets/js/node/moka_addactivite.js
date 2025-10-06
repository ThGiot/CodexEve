import Toast from '../classes/Toast.js';
import FetchHandler from '../classes/FetchHandler.js';
export function moka_addactivite(params) {
    const{formId} = params;

    const data = {
        node : 'action',
        action : 'activite_add',
        analytique_id: document.getElementById('selectAnalytique').value,
        remuneration_type: document.getElementById('selectOption').value,
        code: document.getElementById('codeActivite').value
      };

      const fetchHandler = new FetchHandler(true);
      fetchHandler.sendRequest(data)
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
              refreshContent()();
             
          }else{
              const message = "Une erreur s'est produite : "+response.message;
              const options = {
              position: 'top-right', 
              actionButton: false,    
              closeButton: true,     
              //backgroundColor: 'red', // Couleur de fond que vous souhaitez utiliser
              delay: 4000
              };
              const myToast = new Toast(message, options);
              refreshContent()();
          }
      })
      .catch(error => {
        console.error("Erreur lors de l'envoi de la requête:", error);
    });

}