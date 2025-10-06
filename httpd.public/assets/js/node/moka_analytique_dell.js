import FetchHandler from '../classes/FetchHandler.js';
import { showToast } from '../classes/toastHelper.js';
const fetcher = new FetchHandler(true);
export function moka_analytique_dell(param){
    
    var myModal = new bootstrap.Modal(document.getElementById('AnaDell'));
    myModal.show();
    const {analytiqueId} = param;
    
    document.getElementById('AnaDellOkayButton').onclick = function(){
        const data = {
            node: "action",
            action: "analytique_dell",
            analytique_id : analytiqueId
          
    
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