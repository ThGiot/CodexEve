import FetchHandler from '../classes/FetchHandler.js';
import { showToast } from '../classes/toastHelper.js';

const fetcher = new FetchHandler(true);

export function grid_zone_edit(param) {
    const { zoneId, zoneNom } = param;

    const container = document.getElementById('zone_' + zoneId);
    container.innerHTML = '';

    // Création de l'input
    const input = document.createElement('input');
    input.className = 'form-control';
    input.type = 'text';
    input.style.width = '150px';
    input.value = zoneNom;
    input.id = `zoneInput_${zoneId}`;

    // Empêche la validation au simple clic pour déplacer le curseur
    input.addEventListener('mousedown', (event) => {
        event.stopPropagation(); // Évite le déclenchement du blur
    });

    // Validation avec la touche "Enter"
    input.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            input.blur(); // Simule un blur pour valider la modification
        }
    });

    // Blur uniquement si la modification est confirmée (via Enter ou perte de focus naturelle)
    input.addEventListener('blur', () => handleZoneChange(zoneId, input.value));

    // Ajout à la div
    container.appendChild(input);

    // Donne le focus et place le curseur à la fin du texte
    input.focus();
    input.setSelectionRange(input.value.length, input.value.length); // Place le curseur à la fin
}

function handleZoneChange(zoneId, newValue) {
    const data = {
        node: "action",
        action: "zone_edit",
        zone_id: zoneId,
        zone_nom: newValue
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
