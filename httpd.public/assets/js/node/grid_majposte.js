import FetchHandler from '../classes/FetchHandler.js';
import { showToast } from '../classes/toastHelper.js';
import { loadScheduleData } from '../grid_planning.js'; // Correction du chemin
import { filterTable } from '../grid_planning.js'; // Correction du chemin

const fetcher = new FetchHandler(true);

export async function grid_majposte(param) { // Ajout de async ici
    const { activiteId } = param;
    const data = {
        node: "action",
        action: "poste_maj",
        poste_numero : document.getElementById('posteNum').value,
        poste_nom : document.getElementById('posteNom').value,
        poste_id : document.getElementById('posteId').value,
        poste_association_id : document.getElementById('posteAssociation').value,
        poste_zone_id : document.getElementById('posteZone').value,
        poste_type_id : document.getElementById('posteType').value,
        horaire_id : document.getElementById('posteHoraire').value,
    };

    await fetcher.handleResponse(
        data,
        async response => { // Ajout de `async` ici pour que `await` fonctionne
            var modalElement = document.getElementById('modalDetailPoste');
            var modalInstance = bootstrap.Modal.getInstance(modalElement);

            if (modalInstance) {
                modalInstance.hide();
            } else {
                console.warn("L'instance Bootstrap de la modal n'a pas été trouvée, tentative de fermeture manuelle.");
                modalElement.classList.remove('show'); // Cache visuellement la modal
                modalElement.style.display = 'none';   // Retire l'affichage
                document.body.classList.remove('modal-open'); // Retire la classe du body
                var backdrop = document.querySelector('.modal-backdrop');
                if (backdrop) {
                    backdrop.remove(); // Supprime le fond noir
                }
            }

            await loadScheduleData(); // Correction : Ajout de await ici
            await filterTable(); // Correction : Ajout de await ici
            showToast(response);
        },
        error => showToast(error, true)
    );
}
