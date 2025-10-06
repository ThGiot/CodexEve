import FetchHandler from '../classes/FetchHandler.js';
import { showToast } from '../classes/toastHelper.js';
const fetcher = new FetchHandler(true);
export function grid_association_dell(param){
    
    var myModal = new bootstrap.Modal(document.getElementById('associationDell'));
    myModal.show();
    const {associationId} = param;
    
    document.getElementById('associationDellOkayButton').onclick = function(){
        const data = {
            node: "action",
            action: "assos_dell",
            zone_id : associationId
          
    
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