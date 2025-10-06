import FetchHandler from '../classes/FetchHandler.js';
import { showToast } from '../classes/toastHelper.js';

const fetcher = new FetchHandler(true);
export function moka_activite_maj(param){
    const { activiteId } = param;
    const data = {
        node: "action",
        action: "activite_maj",
        analytique_id: document.getElementById('selectAnalytique').value,
        remuneration_type: document.getElementById('selectOption').value,
        code: document.getElementById('codeActivite').value,
        activite_id : activiteId
      };

      fetcher.handleResponse(
        data,
        response => showToast(response),
        error => showToast(error, true)
    );
 
}