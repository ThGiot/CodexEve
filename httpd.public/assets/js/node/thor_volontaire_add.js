import FetchHandler from '../classes/FetchHandler.js';
import { showToast } from '../classes/toastHelper.js';
const fetcher = new FetchHandler(true);
// Fonction pour initialiser ou mettre à jour Choices
function initializeOrUpdateChoices(selectElement, options) {
    // Vérifiez si Choices a déjà été initialisé pour cet élément
    if (selectElement.choices) {
        // Mettre à jour les options si Choices est déjà initialisé
        selectElement.choices.setChoices(options, 'value', 'label', true);
    } else {
        // Initialiser Choices si ce n'est pas déjà fait
        const choices = new Choices(selectElement, {
            // ... vos options d'initialisation ici ...
        });
        choices.setChoices(options, 'value', 'label', true);
        selectElement.choices = choices; // Stocker la référence à l'instance Choices
    }
}

// Fonction pour préparer et mettre à jour les options du select
function updateSelectOptions(response) {
    const newOptions = response.map(volontaire => ({
        value: volontaire.id,
        label: (volontaire.centreSecours || '') + ' - ' + volontaire.nom + ' ' + volontaire.prenom+ ' ' + volontaire.qualification    ,
        selected: false,
        disabled: false,
    }));

    const selectElement = document.getElementById('volontaireSelect');
    initializeOrUpdateChoices(selectElement, newOptions);
}


export function thor_volontaire_add(param){
    const { moduleId, evenementId } = param;
 
    var myModal = new bootstrap.Modal(document.getElementById('volontaireAdd'));
    myModal.show();

    //Recherche des volontaire dispo pour le module 

    const data = {
        node: "action",
        action: "get_volontaire_dispo",
        evenement_id : evenementId,
        module_id : moduleId
        
      };

      // Envoi de la requête
    fetch('node.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(response => {
            console.log(response);
            updateSelectOptions(response);
            
        })
        .catch(error => {
            showToast(error, true);
        });


    document.getElementById('volontaireAddOkayButton').onclick = function(){
        const data = {
            node: "action",
            action: "volontaire_dispo_add",
            module_id : moduleId,
            volontaire_id : document.getElementById("volontaireSelect").value,
           
            
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