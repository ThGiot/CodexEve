import FetchHandler from '../classes/FetchHandler.js';
import { showToast } from '../classes/toastHelper.js';
const fetcher = new FetchHandler(true);
export function moka_prestation_dell(param){
    
    var myModal = new bootstrap.Modal(document.getElementById('HsDell'));
    myModal.show();
    const {prestationId} = param;
    
    document.getElementById('HsDellOkayButton').onclick = function(){
        const data = {
            node: "action",
            action: "prestation_dell",
            prestation_id : prestationId
          
    
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