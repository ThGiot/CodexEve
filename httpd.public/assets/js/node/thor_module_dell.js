import FetchHandler from '../classes/FetchHandler.js';
import { showToast } from '../classes/toastHelper.js';
const fetcher = new FetchHandler(true);
export function thor_module_dell(param){
    const { moduleId,evenementId } = param;
    
    var myModal = new bootstrap.Modal(document.getElementById('moduleDell'));
    myModal.show();
    document.getElementById('moduleDellOkayButton').onclick = function(){
        const data = {
            node: "action",
            action: "module_dell",
            evenement_id : evenementId,
            module_id : moduleId
            
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