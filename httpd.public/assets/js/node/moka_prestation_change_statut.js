import FetchHandler from '../classes/FetchHandler.js';
import { showToast } from '../classes/toastHelper.js';

const fetcher = new FetchHandler(true);
export function moka_prestation_change_statut(param){
    const { prestationId, statut } = param;
    const data = {
        node: "action",
        action: "prestation_change_statut",
        prestation_id : prestationId,
        statut : statut
        
        
      };

      fetcher.handleResponse(
        data,
        response => {
           if(statut == 0){
            document.getElementById("statut" + prestationId).innerHTML = `
                    <div class="btn-group" role="group" aria-label="Basic example">            
                        <button id="${prestationId}BtnAtt" class="btn btn-primary" type="button" onclick="node('moka_prestation_change_statut', {prestationId: '${prestationId}', statut: '0'})">En attente</button>            
                        <button id="${prestationId}BtnValider" class="btn btn-secondary" type="button" onclick="node('moka_prestation_change_statut', {prestationId: '${prestationId}', statut: '1'})">Valider</button>            
                        <button id="${prestationId}BtnRefuser" class="btn btn-secondary" type="button" onclick="node('moka_prestation_change_statut', {prestationId: '${prestationId}', statut: '2'})">Refuser</button>
                        <button id="${prestationId}BtnRefuser" class="btn btn-secondary" type="button" onclick="node('moka_prestation_change_statut', {prestationId: '${prestationId}', statut: '4'})">Encodé dans EB</button>
                    </div>`;
          }
          if(statut == 1){
            document.getElementById("statut" + prestationId).innerHTML = `
                    <div class="btn-group" role="group" aria-label="Basic example">            
                        <button id="${prestationId}BtnAtt" class="btn btn-secondary" type="button" onclick="node('moka_prestation_change_statut', {prestationId: '${prestationId}', statut: '0'})">En attente</button>            
                        <button id="${prestationId}BtnValider" class="btn btn-success" type="button" onclick="node('moka_prestation_change_statut', {prestationId: '${prestationId}', statut: '1'})">Valider</button>            
                        <button id="${prestationId}BtnRefuser" class="btn btn-secondary" type="button" onclick="node('moka_prestation_change_statut', {prestationId: '${prestationId}', statut: '2'})">Refuser</button>
                        <button id="${prestationId}BtnRefuser" class="btn btn-secondary" type="button" onclick="node('moka_prestation_change_statut', {prestationId: '${prestationId}', statut: '4'})">Encodé dans EB</button>
                    </div>`;
          }
          if(statut == 2){
            document.getElementById("statut" + prestationId).innerHTML = `
                    <div class="btn-group" role="group" aria-label="Basic example">            
                        <button id="${prestationId}BtnAtt" class="btn btn-secondary" type="button" onclick="node('moka_prestation_change_statut', {prestationId: '${prestationId}', statut: '0'})">En attente</button>            
                        <button id="${prestationId}BtnValider" class="btn btn-secondary" type="button" onclick="node('moka_prestation_change_statut', {prestationId: '${prestationId}', statut: '1'})">Valider</button>            
                        <button id="${prestationId}BtnRefuser" class="btn btn-danger" type="button" onclick="node('moka_prestation_change_statut', {prestationId: '${prestationId}', statut: '2'})">Refuser</button>
                        <button id="${prestationId}BtnRefuser" class="btn btn-secondary" type="button" onclick="node('moka_prestation_change_statut', {prestationId: '${prestationId}', statut: '4'})">Encodé dans EB</button>
                    </div>`;
          }
          if(statut == 4){
            document.getElementById("statut" + prestationId).innerHTML = `
                    <div class="btn-group" role="group" aria-label="Basic example">            
                        <button id="${prestationId}BtnAtt" class="btn btn-secondary" type="button" onclick="node('moka_prestation_change_statut', {prestationId: '${prestationId}', statut: '0'})">En attente</button>            
                        <button id="${prestationId}BtnValider" class="btn btn-secondary" type="button" onclick="node('moka_prestation_change_statut', {prestationId: '${prestationId}', statut: '1'})">Valider</button>            
                        <button id="${prestationId}BtnRefuser" class="btn btn-secondary" type="button" onclick="node('moka_prestation_change_statut', {prestationId: '${prestationId}', statut: '2'})">Refuser</button>
                        <button id="${prestationId}BtnRefuser" class="btn btn-warning" type="button" onclick="node('moka_prestation_change_statut', {prestationId: '${prestationId}', statut: '4'})">Encodé dans EB</button>
                    </div>`;
          }
        },
        error => showToast(error, true)
    );
   
}