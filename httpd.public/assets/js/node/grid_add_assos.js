import FetchHandler from '../classes/FetchHandler.js';
import { showToast } from '../classes/toastHelper.js';

const fetcher = new FetchHandler(true);

export function grid_add_assos(param) {
    const { formId } = param;
    const form = document.getElementById(formId);

    if (!form) {
        console.error("Formulaire introuvable avec l'ID :", formId);
        return;
    }

    // Récupération des champs du formulaire
    const formFields = fetcher.getFormFields(form);

    const data = {
        node: "action",
        action: "assos_add",
        formData: formFields // Ajout des données du formulaire
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
