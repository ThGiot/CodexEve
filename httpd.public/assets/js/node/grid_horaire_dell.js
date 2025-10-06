import FetchHandler from '../classes/FetchHandler.js';
import { showToast } from '../classes/toastHelper.js';
const fetcher = new FetchHandler(true);
export function grid_horaire_dell(param){
    
    var myModal = new bootstrap.Modal(document.getElementById('horaireDell'));
    myModal.show();
    const {horaireId} = param;
   
    document.getElementById('horaireDellOkayButton').onclick = function(){
        const data = {
            node: "action",
            action: "horaire_dell",
            horaire_id : horaireId
          
    
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