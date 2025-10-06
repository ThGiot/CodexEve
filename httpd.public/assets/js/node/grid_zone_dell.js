import FetchHandler from '../classes/FetchHandler.js';
import { showToast } from '../classes/toastHelper.js';
const fetcher = new FetchHandler(true);
export function grid_zone_dell(param){
    
    var myModal = new bootstrap.Modal(document.getElementById('zoneDell'));
    myModal.show();
    const {zoneId} = param;
    
    document.getElementById('zoneDellOkayButton').onclick = function(){
        const data = {
            node: "action",
            action: "zone_dell",
            zone_id : zoneId
          
    
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