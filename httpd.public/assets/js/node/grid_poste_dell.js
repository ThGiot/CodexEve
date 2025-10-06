import FetchHandler from '../classes/FetchHandler.js';
import { showToast } from '../classes/toastHelper.js';
const fetcher = new FetchHandler(true);
export function grid_poste_dell(param){
    
    var myModal = new bootstrap.Modal(document.getElementById('posteDell'));
    myModal.show();
    const {posteId} = param;
    
    document.getElementById('posteDellOkayButton').onclick = function(){
        const data = {
            node: "action",
            action: "poste_dell",
            poste_id : posteId
          
    
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