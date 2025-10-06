// Constantes pour les heures et les jours
const HOURS = ["00h", "01h", "02h", "03h", "04h", "05h", "06h", "07h", "08h", "09h", "10h", "11h", "12h", "13h", "14h", "15h", "16h", "17h", "18h", "19h", "20h", "21h", "22h", "23h"];
const DAYS = ["Jeu", "Ven", "Sam", "Dim", "Lun"];
const EARLY_HOURS = ["03h", "04h", "05h", "06h", "07h"];
const dayColors = {
    "Jeu": "#ffebcd", // Blanched Almond
    "Ven": "#ffe4e1", // Misty Rose
    "Sam": "#e6e6fa", // Lavender
    "Dim": "#dff0d8", // Light Green
    "Lun": "#d9edf7"  // Light Blue
};


let exampleData = [];

import { showToast } from './classes/toastHelper.js';
import FetchHandler from './classes/FetchHandler.js';

// Récupération des données
export async function loadScheduleData() {
    const fetchHandler = new FetchHandler();
    try {
        const response = await fetchHandler.sendRequest({ node: "action", action: "schedule_data" });
        if (response.success && response.data) {
            exampleData = response.data;
           console.log("Données ScheduleData chargées avec succès" );
            renderScheduleTable(exampleData);
        } else {
            showToast({ message: response.message }, true);
        }
    } catch (error) {
        showToast({ message: "Erreur lors de la récupération des données" }, true);
    }
}

// Préselectionner le jeudi lors du chargement de la page
function preselectThursday() {
    const filterDayElement = document.getElementById('filterDay');
    if (filterDayElement) {
        Array.from(filterDayElement.options).forEach(option => {
            if (option.value === 'Jeu') {
                option.selected = true;
            }
        });
    }
}

// Application des filtres
function applyFilters(data, filters) {
    return data.filter(post => {
        const matchZone = !filters.zone || post.zone.toLowerCase() === filters.zone;
        const matchType = !filters.type || post.type.toLowerCase() === filters.type;
        // Recherche sur le champ 'poste' et 'numero'
        const matchSearch = !filters.search ||
            post.poste.toLowerCase().includes(filters.search) ||
            String(post.numero).toLowerCase().includes(filters.search);
        const matchAssociation = !filters.association || post.association.toLowerCase() === filters.association;
        const matchDay = filters.days.length === 0 || filters.days.some(day =>
            Object.keys(post.heures).some(key => key.startsWith(day))
        );
        return matchZone && matchType && matchSearch && matchAssociation && matchDay;
    });
}

// Rendu du tableau
export function renderScheduleTable(data) {
    const scheduleTableElement = document.getElementById('scheduleTable');
    const selectedDays = Array.from(document.getElementById('filterDay')?.selectedOptions || []).map(option => option.value);
    const ignoreEarlyHours = document.getElementById('ignoreEarlyHours')?.checked;

    if (!scheduleTableElement) return console.warn("Élément #scheduleTable introuvable.");
    if (!Array.isArray(data)) return console.error("Données invalides:", data);

    const daysToRender = selectedDays.length > 0 ? selectedDays : DAYS;

    let html = `<table class="table table-bordered table-hover table-schedule"><thead><tr class="table-primary">
    <th>N°</th>
    <th>Poste</th>
    <th class="fixed">Zone</th>
    <th class="fixed">Association</th>
    <th class="fixed">Type</th>
    <th class="fixed">Période</th>`;

    // Entêtes des colonnes horaires
    daysToRender.forEach((day) => {
        HOURS.forEach((hour) => {
            if (!(ignoreEarlyHours && EARLY_HOURS.includes(hour))) {
                const bgColor = dayColors[day] || "#ffffff";
                html += `<th style="background-color: ${bgColor};">${day}-${hour}</th>`;
            }
        });
    });

    html += '</tr></thead><tbody>';

    // Pour chaque poste, création d'une ligne avec 6 cellules fixes
    data.forEach(post => {
        // On suppose que l'objet post possède une propriété 'poste_id'
        html += `<tr data-poste-id="${post.poste_id}">
            <td class="fixed clickable-post" data-poste-id="${post.poste_id}">${post.numero}</td>
            <td class="fixed clickable-post" data-poste-id="${post.poste_id}">${post.poste}</td>
            <td class="fixed clickable-post" data-poste-id="${post.poste_id}">${post.zone}</td>
            <td class="fixed clickable-post" data-poste-id="${post.poste_id}">${post.association}</td>
            <td class="fixed clickable-post" data-poste-id="${post.poste_id}">${post.type}</td>
            <td class="fixed clickable-post" data-poste-id="${post.poste_id}">${post.periode}</td>`;

        // Ajout des cellules pour les heures
        daysToRender.forEach((day) => {
            HOURS.forEach((hour) => {
                if (!(ignoreEarlyHours && EARLY_HOURS.includes(hour))) {
                    const key = `${day}-${hour}`;
                    const isActive = post.heures?.[key] ? '1' : '';
                    html += `<td data-day="${day}" data-hour="${hour}" class="clickable">${isActive}</td>`;
                }
            });
        });
        html += '</tr>';
    });

    html += '</tbody></table>';
    scheduleTableElement.innerHTML = html;

    // Gestionnaires d'événements pour les cellules horaires
    document.querySelectorAll('.clickable').forEach(cell => {
        cell.addEventListener('click', () => showTotalPosts(cell.dataset.day, cell.dataset.hour));
    });

    // Gestionnaires d'événements pour les 6 premières cellules fixes
    document.querySelectorAll('.clickable-post').forEach(cell => {
        cell.addEventListener('click', (e) => {
            // Empêcher la propagation si besoin (pour éviter que le clic ne déclenche d'autres événements sur la même ligne)
            e.stopPropagation();
            const posteId = cell.dataset.posteId;
            getInfoPoste(posteId);
        });
    });
}

function setSelectValue(selectId, value) {
    const selectElement = document.getElementById(selectId);
    if (!selectElement) {
        console.error(`Élément <select> introuvable: ${selectId}`);
        return;
    }

    const valueStr = String(value).trim();
    let optionFound = false;

    // Ajouter l'option "Aucun" une seule fois si elle n'existe pas encore
    let noOption = selectElement.querySelector('option[value=""]');
    if (!noOption) {
        noOption = new Option("Aucun", "");
        selectElement.insertBefore(noOption, selectElement.firstChild);
    }

    // Vérifie si la valeur existe dans les options
    for (let option of selectElement.options) {
        if (String(option.value).trim() === valueStr) {
            option.selected = true;
            optionFound = true;
            break;
        }
    }

    // Si aucune correspondance trouvée, on sélectionne "Aucun"
    if (!optionFound) {
        noOption.selected = true;
    }

    // Déclenchement de l'événement "change" pour mettre à jour l'affichage
    selectElement.dispatchEvent(new Event('change'));

    // Si Choices.js est utilisé, met à jour l'affichage
    if (selectElement.classList.contains("choices__input") || selectElement.dataset.choices) {
        console.log("Mise à jour de Choices.js pour", selectId);
        const instance = selectElement.choicesInstance || new Choices(selectElement, {
            removeItemButton: true,
            searchEnabled: true,
            placeholder: true
        });

        instance.setValue([{ value: optionFound ? valueStr : "", label: optionFound ? `ID ${valueStr} (Donnée API)` : "Aucun", selected: true }]);
    }
}






async function getInfoPoste(posteId) {
   
        document.querySelectorAll('.searchable-select').forEach(select => {
            new Choices(select, {
                removeItemButton: true,
                searchEnabled: true,
                placeholder: true
            });
        });
    
    console.log("Poste cliqué :", posteId);
    const fetchHandler = new FetchHandler();
    try {
        const response = await fetchHandler.sendRequest({ 
            node: "action", 
            action: "grid_get_poste", 
            poste_id: posteId 
        });

        if (!response.success) {
            throw new Error(`Erreur API: ${response.message || "Réponse invalide"}`);
        }

        if (!response.data || typeof response.data !== "object") {
            throw new Error("Données du poste invalides ou absentes.");
        }

        console.log("Données reçues :", response.data);

        // Mise à jour du contenu de la modal avec les infos du poste
        document.getElementById('posteNum').value=response.data.numero;
        document.getElementById('posteNom').value=response.data.nom;
        document.getElementById('posteId').value=response.data.poste_id;
        const selectElement = document.getElementById('posteAssociation');

console.log("Options disponibles dans posteAssociation:");
selectElement.querySelectorAll("option").forEach(opt => console.log(`value="${opt.value}" text="${opt.textContent}"`));

console.log("Valeur attendue:", response.data.association_id);

        setSelectValue('posteZone', response.data.zone_id);
        setSelectValue('posteAssociation', response.data.association_id);
        setSelectValue('posteType', response.data.poste_type_id);
        setSelectValue('posteHoraire', response.data.horaire_id);
        var myModal = new bootstrap.Modal(document.getElementById('modalDetailPoste'));
        myModal.show();

    } catch (error) {
        console.error("Erreur détectée :", error);
        showToast({ message: "Erreur lors de la récupération des données: " + error.message }, true);
    }
}


// Pour rendre la fonction accessible globalement si nécessaire :
window.getInfoPoste = getInfoPoste;


// Filtrage de la table
export function filterTable() {
    const filters = {
        zone: document.getElementById('filterZone')?.value.toLowerCase() || "",
        type: document.getElementById('filterType')?.value.toLowerCase() || "",
        search: document.getElementById('searchPost')?.value.toLowerCase() || "",
        association: document.getElementById('filterAssociation')?.value.toLowerCase() || "",
        days: Array.from(document.getElementById('filterDay')?.selectedOptions || []).map(option => option.value)
    };

    // Ajout : Assurez-vous que les jours sont uniques et triés
    filters.days = filters.days.sort((a, b) => DAYS.indexOf(a) - DAYS.indexOf(b));

    const filteredData = applyFilters(exampleData, filters);
    renderScheduleTable(filteredData);
}


// Affichage du total des postes
export function showTotalPosts(day, hour) {
    const filters = {
        zone: document.getElementById('filterZone')?.value.toLowerCase() || "",
        type: document.getElementById('filterType')?.value.toLowerCase() || "",
        search: document.getElementById('searchPost')?.value.toLowerCase() || "",
        association: document.getElementById('filterAssociation')?.value.toLowerCase() || "",
        days: Array.from(document.getElementById('filterDay')?.selectedOptions || []).map(option => option.value)
    };

    const filteredData = applyFilters(exampleData, filters);

    // 1. Calcul du total des postes actifs à cette heure et ce jour
    const totalAtHour = filteredData.reduce((count, post) => {
        const key = `${day}-${hour}`;
        return count + (post.heures?.[key] ? 1 : 0);
    }, 0);

    // Trouver la cellule cliquée
    const clickedCell = document.querySelector(`[data-day="${day}"][data-hour="${hour}"]`);
    if (!clickedCell) return;

    const row = clickedCell.closest('tr'); // Trouver la ligne entière
    if (!row) return;

    // 2. Calcul du total d'heures actives (1) sur la ligne du poste
    let totalActiveHoursRow = 0;
    row.querySelectorAll('[data-day][data-hour]').forEach(cell => {
        if (cell.textContent.trim() === "1") {
            totalActiveHoursRow++;
        }
    });

    // 3. Calcul du total d'heures actives sur l’ensemble du tableau filtré
    let totalActiveHoursTable = 0;
    document.querySelectorAll('[data-day][data-hour]').forEach(cell => {
        if (cell.textContent.trim() === "1") {
            totalActiveHoursTable++;
        }
    });

    // Affichage des résultats
    const infoElement = document.getElementById('totalPosts');
    if (infoElement) {
        infoElement.className = "alert";
        infoElement.innerHTML = `
            <p>Total de postes présents le <b>${day} à ${hour}</b>: <b>${totalAtHour}</b></p>
            <p>Total d'heures actives sur cette ligne (poste cliqué) : <b>${totalActiveHoursRow}</b></p>
            <p>Total d'heures actives dans le tableau filtré : <b>${totalActiveHoursTable}</b></p>
        `;
    }
}


window.showTotalPosts = showTotalPosts;
window.filterTable = filterTable;

// Chargement initial des données
document.addEventListener("DOMContentLoaded", function() {
    // Vérifier si l'élément `scheduleTable` est dans le DOM
    if (document.getElementById('scheduleTable')) {
        preselectThursday();
      //  loadScheduleData();
    } else {
        // Attendre que l'élément soit ajouté dynamiquement
        const observer = new MutationObserver(() => {
            if (document.getElementById('scheduleTable')) {
                preselectThursday();
              //  loadScheduleData();
                observer.disconnect(); // Arrêter l'observation une fois chargé
            }
        });

        observer.observe(document.body, { childList: true, subtree: true });
    }
});

