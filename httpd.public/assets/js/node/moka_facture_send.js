import FetchHandler from '../classes/FetchHandler.js';
import { showToast } from '../classes/toastHelper.js';
const fetcher = new FetchHandler(true);
export function moka_facture_send(){
   
    
    var myModal = new bootstrap.Modal(document.getElementById('modalSend'));
    myModal.show();
    document.getElementById('modalSendOkayButton').onclick = function(){
        myModal.hide();
        const buttonSubmit = document.getElementById('buttonSubmitSend');
        buttonSubmit.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"> </span> Envoyer ...';
        const data = {
            node: "action",
            action: "facture_send"
            
          };
    
          fetcher.handleResponse(
            data,
            response => {
                buttonSubmit.innerHTML = 'Envoyer';
                showToast(response);
             
                
            },
            error => showToast(error, true)
        );
    };
}