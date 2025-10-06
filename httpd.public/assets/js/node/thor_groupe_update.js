import FetchHandler from '../classes/FetchHandler.js';
import { showToast } from '../classes/toastHelper.js';
const fetcher = new FetchHandler(true);
export function thor_groupe_update(param){
    const { groupe, evenementId } = param;
    document.getElementById("updateGroupenom").value = groupe;
    var myModal = new bootstrap.Modal(document.getElementById('updateGroup'));
    myModal.show();
    document.getElementById('updateGroupOkayButton').onclick = function(){
        const data = {
            node: "action",
            action: "module_update_groupe",
            old_groupe : groupe,
            groupe : document.getElementById("updateGroupenom").value,
            evenement_id : evenementId
            
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