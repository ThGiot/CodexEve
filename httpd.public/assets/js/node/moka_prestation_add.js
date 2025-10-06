import FetchHandler from '../classes/FetchHandler.js';
import { showToast } from '../classes/toastHelper.js';

const fetcher = new FetchHandler(true);
export function moka_prestation_add(param){

    const data = {
        node: "action",
        action: "prestation_add",
        prestation : document.getElementById("prestation").value,
        nb_hr : document.getElementById("nb").value,
        comment : document.getElementById("comment").value,
        
      };

      fetcher.handleResponse(
        data,
        response => showToast(response),
        error => showToast(error, true)
    );
    refreshContent();
}