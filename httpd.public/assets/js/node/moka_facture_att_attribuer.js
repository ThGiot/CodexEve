import FetchHandler from '../classes/FetchHandler.js';
import { showToast } from '../classes/toastHelper.js';
const fetcher = new FetchHandler(true);
export function moka_facture_att_attribuer(param){
    const { factureId } = param;
    var myModal = new bootstrap.Modal(document.getElementById('factureModal'));
    myModal.show();
    document.getElementById('factureModalOkayButton').onclick = function(){

        const data = {
            node: "action",
            action: "facture_attente_attribuer",
            facture_id : factureId,
            prestataire_id : document.getElementById("prestataireSelect").value,
    
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