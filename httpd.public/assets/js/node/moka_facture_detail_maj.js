import FetchHandler from '../classes/FetchHandler.js';
import { showToast } from '../classes/toastHelper.js';

const fetcher = new FetchHandler(true);
export function moka_facture_detail_maj(param){
    const { factureDetailId,toMaj,inputId } = param;
    const data = {
        node: "action",
        action: "facture_detail_maj",
        facture_detail_id : factureDetailId,
        to_maj : toMaj,
        value : document.getElementById(inputId).value,

      };

      fetcher.handleResponse(
        data,
        response => {
            showToast(response);
            refreshContent();
        },
        error => {
            showToast(error, true);
            refreshContent();
        }
    );
}