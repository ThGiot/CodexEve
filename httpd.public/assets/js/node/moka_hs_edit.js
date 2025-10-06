import FetchHandler from '../classes/FetchHandler.js';
import { showToast } from '../classes/toastHelper.js';
const fetcher = new FetchHandler(true);
export function moka_hs_edit(param){
    
    var myModal = new bootstrap.Modal(document.getElementById('modaleHsEdit'));
    myModal.show();
    const {prestationId, nbHeure} = param;
    
    document.getElementById("hsEditHeures").value = nbHeure;

    document.getElementById('modaleHsEditOkayButton').onclick = function(){
        const data = {
            node: "action",
            action: "hs_maj",
            prestation_id : prestationId,
            nb_heure : document.getElementById("hsEditHeures").value
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