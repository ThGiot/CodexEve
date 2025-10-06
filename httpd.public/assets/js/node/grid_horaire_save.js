import FetchHandler from '../classes/FetchHandler.js';
import { showToast } from '../classes/toastHelper.js';

const fetcher = new FetchHandler(true);

export function grid_horaire_save(param) {
    const { horaireId } = param;
  

    const data = {
        node: "action",
        action: "periode_add",
        horaire_id: horaireId,
        nom : document.getElementById("add_periode_nom").value,
        plage_debut : document.getElementById("add_periode_debut").value,
        plage_fin : document.getElementById("add_periode_fin").value,
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
