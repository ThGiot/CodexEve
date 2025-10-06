import FetchHandler from '../classes/FetchHandler.js';
import { showToast } from '../classes/toastHelper.js';
const fetcher = new FetchHandler(true);
export function thor_module_update(param){
    const { formId, evenementId,moduleId, nom, groupe, nb_volontaire, date_debut, date_fin, heure_debut, heure_fin } = param;
    document.getElementById("updateModulenom").value = nom;
    document.getElementById("updateModulegroupe").value = groupe;
    document.getElementById("updateModulevolontaire").value = nb_volontaire;
    document.getElementById("updateModuledate_debut").value = date_debut;
    document.getElementById("updateModuledate_fin").value = date_fin;
    document.getElementById("updateModuleheure_debut").value = heure_debut;
    document.getElementById("updateModuleheure_fin").value = heure_fin;
    var myModal = new bootstrap.Modal(document.getElementById('updateModule'));
    myModal.show();
    document.getElementById('updateModuleOkayButton').onclick = function(){

        var formulaire = document.getElementById(formId);

    // Fonction pour récupérer tous les champs du formulaire
        function getFormFields(form) {
            var fields = [];
            // Parcourir tous les éléments du formulaire
            for (var i = 0; i < form.elements.length; i++) {
                var field = form.elements[i];
                // Ajouter l'élément au tableau si ce n'est pas un bouton
                if (field.type !== 'submit') {
                    fields.push({
                        type: field.type,
                        name: field.name,
                        value: field.value,
                        disabled: field.disabled
                    });
                }
            }
            return fields;
        }
        var formFields = getFormFields(formulaire);

        const data = {
            node: "action",
            action: "module_update",
            module_id : moduleId,
            evenement_id : evenementId,
            form_field : formFields
    
          };
    
          fetcher.handleResponse(
            data,
            response => {
                showToast(response);
                myModal.hide();
                refreshContent();
            },
            error => showToast(error, true)
        );
    };
}