import FetchHandler from '../classes/FetchHandler.js';
import { showToast } from '../classes/toastHelper.js';

const fetcher = new FetchHandler(true);
export function moka_addfacture(){
    
    const data = {
        node: "action",
        action: "facture_add",
        designation_facture : document.getElementById("designationFact").value,
        prestataire_id : document.getElementById("selectPrestataire").value,
        analytique_id : document.getElementById("selectAnalytique").value,
        designation_detail : document.getElementById("designationDetail").value,
        montant : document.getElementById("montant").value
      };

      fetcher.handleResponse(
        data,
        response => {
            showToast(response);
            refreshContent();
        },
        error => showToast(error, true)
    );
}