import FetchHandler from '../classes/FetchHandler.js';
import { showToast } from '../classes/toastHelper.js';

const fetcher = new FetchHandler(true);
export function moka_facture_detail_add(param){
    const { factureId } = param;
    const data = {
        node: "action",
        action: "facture_detail_add",
        facture_id : factureId,
        designation : document.getElementById("detailDesignationAdd").value,
        montant : document.getElementById("montantDesignationAdd").value,

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