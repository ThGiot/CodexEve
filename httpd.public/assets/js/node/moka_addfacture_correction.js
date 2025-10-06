import FetchHandler from '../classes/FetchHandler.js';
import { showToast } from '../classes/toastHelper.js';

const fetcher = new FetchHandler(true);
export function moka_addfacture_correction(){
    
    const data = {
        node: "action",
        action: "facture_correction_add",
        prestataire_id :document.getElementById("selectPrestataire").value,
        analytique_id :document.getElementById("selectAnalytique").value,
        montant :document.getElementById("montant").value,
        designation :document.getElementById("designation").value,

      };

      fetcher.handleResponse(
        data,
        response => {
            showToast(response);
            refreshContent();
        },
        error => {
            showToast(error, true);
           
        }
    );
}