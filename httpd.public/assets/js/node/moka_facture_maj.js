import FetchHandler from '../classes/FetchHandler.js';
import { showToast } from '../classes/toastHelper.js';

const fetcher = new FetchHandler(true);
export function moka_facture_maj(param){
    const { factureId } = param;
    const data = {
        node: "action",
        action: "facture_maj",
        facture_id : factureId,
        designation : document.getElementById("factureDesignation").value,
        analytique_id : document.getElementById("selectAnalytique").value,

      };

      fetcher.handleResponse(
        data,
        response => showToast(response),
        error => showToast(error, true)
    );
}