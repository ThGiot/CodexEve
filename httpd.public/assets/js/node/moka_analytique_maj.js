import FetchHandler from '../classes/FetchHandler.js';
import { showToast } from '../classes/toastHelper.js';
const fetcher = new FetchHandler(true);
export function moka_analytique_maj(param){
    
    var myModal = new bootstrap.Modal(document.getElementById('modaleAnaMaj'));
    myModal.show();
    const {analytiqueId, analytique, nom,entite,code,distribution} = param;
    
    document.getElementById("anaMajnom").value = nom;
    document.getElementById("anaMajanalytique").value = analytique;
    document.getElementById("anaMajcode").value = code;
    document.getElementById("anaMajentitée").value = entite;
    document.getElementById("anaMajdistribution").value = distribution;
    document.getElementById('modaleAnaMajOkayButton').onclick = function(){
        const data = {
            node: "action",
            action: "analytique_maj",
            analytique_id : analytiqueId,
            analytique : document.getElementById("anaMajanalytique").value,
            code_centralisateur: document.getElementById("anaMajcode").value,
            entite: document.getElementById("anaMajentitée").value,
            nom : document.getElementById("anaMajnom").value,
            distribution : document.getElementById("anaMajdistribution").value
          
    
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