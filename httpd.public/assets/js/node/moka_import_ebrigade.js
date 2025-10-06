import FetchHandler from '../classes/FetchHandler.js';
import Toast from '../classes/Toast.js';
export function moka_import_ebrigade(params) {
    const fetcher = new FetchHandler(true);
    const{formId} = params;
    const buttonSubmit = document.getElementById('buttonSubmit');
    buttonSubmit.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Chargement...';
    const data = {
        node : 'action',
        action : 'import_ebrigade',
        dateStart: document.getElementById('dateStart').value,
        dateFin: document.getElementById('dateFin').value,
        designation : document.getElementById('designation').value
      };
      const fetchHandler = new FetchHandler(true);
      fetchHandler.sendRequest(data)
      .then(response => {
          console.log(response);
          if(response.success == true){
              document.getElementById('dateStart').value ='';
              document.getElementById('dateFin').value ='';
              document.getElementById('designation').value ='';
              buttonSubmit.innerHTML = 'Enregistrer';
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

