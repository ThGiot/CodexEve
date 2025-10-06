import FetchHandler from '../classes/FetchHandler.js';
import { showToast } from '../classes/toastHelper.js';
const fetcher = new FetchHandler(true);
export function moka_facture_correction_dell(param){
    
    var myModal = new bootstrap.Modal(document.getElementById('CorDell'));
    myModal.show();
    const {correctionId} = param;
    
    document.getElementById('CorDellOkayButton').onclick = function(){
        const data = {
            node: "action",
            action: "facture_correction_dell",
            correction_id : correctionId
          
    
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