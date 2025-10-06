

import FetchHandler from '../classes/FetchHandler.js';
import Toast from '../classes/Toast.js';

const fetcher = new FetchHandler(true);
export function moka_facture_detail_dell(param){
    const { factureDetailId } = param;
    const data = {
        node: "action",
        action: "facture_detail_dell",
        facture_detail_id : factureDetailId
      };

      const fetchHandler = new FetchHandler(true);
      fetchHandler.sendRequest(data)
      .then(response => {
          console.log(response);
          if(response.success == true){
            if ('deleted' in response) {
                const message = response.message;
                const options = {
                position: 'top-right', 
                actionButton: false,    
                closeButton: true,
                 delay: 2000
              };
              const myToast = new Toast(message, options);
              refreshContent();
            }
              const message = response.message;
              const options = {
              position: 'top-right', 
              actionButton: false,    
              closeButton: true,     
              //backgroundColor: 'red', // Couleur de fond que vous souhaitez utiliser
              delay: 2000
              };
              const myToast = new Toast(message, options);
             
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
          }
      })
      .catch(error => {
          console.error("Erreur lors de l'envoi de la requête:", error);
      });
 
}