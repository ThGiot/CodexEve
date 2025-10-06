import FetchHandler from '../classes/FetchHandler.js';
import { showToast } from '../classes/toastHelper.js';
const fetcher = new FetchHandler(true);
export function thor_volontaire_dell(param){
    const { moduleId, volontaireId } = param;
            const data = {
            node: "action",
            action: "volontaire_dispo_dell",
            module_id : moduleId,
            volontaire_id : volontaireId
    
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