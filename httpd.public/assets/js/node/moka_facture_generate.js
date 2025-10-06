import FetchHandler from '../classes/FetchHandler.js';
import { showToast } from '../classes/toastHelper.js';

const fetcher = new FetchHandler(true);
export function moka_facture_generate(param){
    const { factureId } = param;
    const buttonSubmit = document.getElementById('buttonSubmit');
    buttonSubmit.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"> </span> Envoyer ...';
    const data = {
        node: "action",
        action: "facture_create_pdf",
        date_debut : document.getElementById("dateDebut").value,
        date_fin : document.getElementById("dateFin").value,

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