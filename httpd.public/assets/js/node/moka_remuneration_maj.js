import FetchHandler from '../classes/FetchHandler.js';
import { showToast } from '../classes/toastHelper.js';

const fetcher = new FetchHandler(true);
export function moka_remuneration_maj(param){
    const { remunerationId, activiteId, toMaj, inputId } = param;
    const data = {
        node: "action",
        action: "remuneration_maj",
        to_maj : toMaj,
        value : document.getElementById(inputId).value,
        remuneration_id : remunerationId,
        activite_id : activiteId,
      };

      fetcher.handleResponse(
        data,
        response => showToast(response),
        error => showToast(error, true)
    );
}