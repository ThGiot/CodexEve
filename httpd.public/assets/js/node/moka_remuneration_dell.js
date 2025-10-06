
import FetchHandler from '../classes/FetchHandler.js';
import { showToast } from '../classes/toastHelper.js';

const fetcher = new FetchHandler(true);
export function moka_remuneration_dell(param){
    const { remunerationId,activiteId} = param;
    const data = {
        node: "action",
        action: "remuneration_dell",
        remuneration_id : remunerationId
      };

      fetcher.handleResponse(
        data,
        response => showToast(response),
        error => showToast(error, true)
    );getContent(202,{activite_id : activiteId});
    
}