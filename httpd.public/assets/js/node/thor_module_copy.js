import FetchHandler from '../classes/FetchHandler.js';
import { showToast } from '../classes/toastHelper.js';
const fetcher = new FetchHandler(true);
export function thor_module_copy(param){
    const { moduleId,evenementId } = param;
    

        const data = {
            node: "action",
            action: "module_copy",
            evenement_id : evenementId,
            module_id : moduleId
            
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