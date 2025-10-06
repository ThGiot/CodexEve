
import FetchHandler from '../classes/FetchHandler.js';
import { showToast } from '../classes/toastHelper.js';

const fetcher = new FetchHandler(true);
export function moka_activite_dell(param){
    const { activiteId} = param;
    const data = {
        node: "action",
        action: "activite_dell",
        activite_id : activiteId
      };

      fetcher.handleResponse(
        data,
        response => showToast(response),
        error => showToast(error, true)
    );getContent(23);
    
}