import FetchHandler from '../classes/FetchHandler.js';
import { showToast } from '../classes/toastHelper.js';

const fetcher = new FetchHandler(true);
export function moka_remuneration_add(param){
    const { activiteId } = param;
    const data = {
        node: "action",
        action: "remuneration_add",
        grade : document.getElementById("grade").value,
        tarif_perm : document.getElementById("tarif_perm").value,
        tarif_garde : document.getElementById("tarif_garde").value,
        activite_id : activiteId
      };

      fetcher.handleResponse(
        data,
        response => showToast(response),
        error => showToast(error, true)
    );
    refreshContent();
}