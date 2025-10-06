import FetchHandler from '../classes/FetchHandler.js';
import { showToast } from '../classes/toastHelper.js';
const fetcher = new FetchHandler(true);
export function thor_volontaire_add_man(param){
    const { moduleId, evenementId } = param;
    var myModal = new bootstrap.Modal(document.getElementById('volontaireAddMan'));
    myModal.show();
    document.getElementById('volontaireAddManOkayButton').onclick = function(){
      
        const data = {
            node: "action",
            action: "volontaire_add_man",
            module_id : moduleId,
            evenement_id : evenementId,
            nom: document.getElementById("volontaireAddMannom").value,
            prenom : document.getElementById("volontaireAddManprenom").value,
            centre_secours : document.getElementById("volontaireAddManCS").value
    
          };
    
          fetcher.handleResponse(
            data,
            response => {
                showToast(response);
                myModal.hide();
                refreshContent();
            },
            error => showToast(error, true)
        );
    };
}