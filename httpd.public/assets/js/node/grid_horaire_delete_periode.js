import FetchHandler from '../classes/FetchHandler.js';
import { showToast } from '../classes/toastHelper.js';
const fetcher = new FetchHandler(true);
export function grid_horaire_delete_periode(param){
    
    var myModal = new bootstrap.Modal(document.getElementById('horairePeriodeDell'));
    myModal.show();
    const {periodeId} = param;
   
    document.getElementById('horairePeriodeDellOkayButton').onclick = function(){
        const data = {
            node: "action",
            action: "horaire_periode_dell",
            periode_id : periodeId
          
    
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