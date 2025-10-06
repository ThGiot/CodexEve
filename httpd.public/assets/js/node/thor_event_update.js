import FetchHandler from '../classes/FetchHandler.js';
import { showToast } from '../classes/toastHelper.js';
const fetcher = new FetchHandler(true);
export function thor_event_update(param){
    const {evenementId,nom,date,infos } = param;
    document.getElementById("updateEventnom").value = nom;
    document.getElementById("updateEventdate").value = date;
    document.getElementById("updateEventinfos").value = infos;
    var myModal = new bootstrap.Modal(document.getElementById('updateEvent'));
    myModal.show();
    document.getElementById('updateEventOkayButton').onclick = function(){
        const data = {
            node: "action",
            action: "event_update",
            nom :  document.getElementById("updateEventnom").value,
            date :  document.getElementById("updateEventdate").value,
            infos : document.getElementById("updateEventinfos").value,
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