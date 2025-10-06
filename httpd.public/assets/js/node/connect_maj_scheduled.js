import FetchHandler from '../classes/FetchHandler.js';
import Toast from '../classes/Toast.js';
const fetcher = new FetchHandler(true);
export function connect_maj_scheduled(param){
    const { item, id,smsId } = param;
    const value = document.getElementById(id).value;
    const data = {
        node: "action",
        action: "connect_sms_maj",
        sms_id : smsId,
        item : item,
        value : value
      };
      const fetchHandler = new FetchHandler(true);
      fetchHandler.sendRequest(data)
      .then(response => {
          console.log(response);
          if(response.success == true){
              const message = "Information mises à jours";
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