console.log('Chargement de grid_periode_manage.js');
import FetchHandler from '../classes/FetchHandler.js';
// **Variables globales**
export let isDragging = false;
export let isErasing = false;
export let activePeriod = null;
export let periods = [];
export const days = ["Jeudi", "Vendredi", "Samedi", "Dimanche", "Lundi"];
export const hours = Array.from({ length: 24 }, (_, i) => `${i.toString().padStart(2, "0")}:00`);

let tableHead, tableBody, periodListDiv;


const importedData = [
    { id: 1, horaire_id: 1, nom: "A", date_debut: "2025-06-26 08:00:00", date_fin: "2025-06-26 13:00:00", client_id: 0 },
    { id: 3, horaire_id: 1, nom: "A", date_debut: "2025-06-28 00:00:00", date_fin: "2025-06-28 03:00:00", client_id: 0 },
    { id: 4, horaire_id: 1, nom: "A", date_debut: "2025-06-28 14:00:00", date_fin: "2025-06-28 17:00:00", client_id: 0 },
    { id: 5, horaire_id: 1, nom: "A", date_debut: "2025-06-28 21:00:00", date_fin: "2025-06-29 00:00:00", client_id: 0 }
];
// **Initialisation du module**
export async function grid_periode_manage(param) {
    const {horaireId} = param;
    console.log("Initialisation de grid_periode_manage");

    // Sélection des éléments DOM (à exécuter après le chargement du HTML)
    tableHead = document.querySelector("#schedule thead tr");
    tableBody = document.querySelector("#schedule tbody");
    periodListDiv = document.querySelector("#period-list");

    if (!tableHead || !tableBody || !periodListDiv) {
        console.error("Erreur : Les éléments DOM ne sont pas chargés.");
        return;
    }
    periods = [];
    createTable();
    const data = await getData(horaireId);
    importData(data);
    updatePeriodList();
}


export async function getData(horaireId) {
    try {
        if (!horaireId) {
            console.warn("⚠️ `horaireId` est invalide ou non défini.");
            showToast("Erreur : Identifiant d'horaire non valide.");
            return [];
        }

        console.log(`🔄 Récupération des données pour horaire_id=${horaireId}...`);

        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 10000); // Timeout après 10 secondes

        const response = await fetch("node.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json"
            },
            body: JSON.stringify({
                action: "periode_get_data",
                node: "action",
                horaire_id: horaireId
            }),
            signal: controller.signal
        });

        clearTimeout(timeoutId);

        if (!response.ok) {
            throw new Error(`Erreur serveur : ${response.status} - ${response.statusText}`);
        }

        // ✅ Lire la réponse brute avant parsing
        const rawResponse = await response.clone().text();
        console.log("📝 Réponse brute reçue :", rawResponse);

        // ✅ Lire la réponse JSON et vérifier la structure
        const jsonResponse = await response.json();
        console.log("✅ Données récupérées après parsing JSON :", jsonResponse);

        if (!jsonResponse.success || !Array.isArray(jsonResponse.data)) {
            throw new Error("⚠️ Données reçues invalides (structure incorrecte)");
        }

        // ✅ Transformer les données pour respecter le format attendu
        const transformedData = jsonResponse.data.map(item => ({
            id: item.periode_id, // `periode_id` devient `id`
            horaire_id: item.horaire_id,
            nom: item.periode_nom, // `periode_nom` devient `nom`
            date_debut: item.date_debut,
            date_fin: item.date_fin,
            client_id: 0 // Valeur par défaut (manquante dans l'API)
        }));

        console.log("🔄 Données après transformation :", transformedData);

        return transformedData; // ✅ Retourner le format attendu

    } catch (error) {
        if (error.name === "AbortError") {
            console.error("❌ Timeout : La requête a été annulée après 10 secondes.");
            showToast("Erreur : La requête a pris trop de temps.");
        } else {
            console.error("❌ Erreur lors de la récupération des données :", error);
            showToast("Impossible de récupérer les données.");
        }
        return []; // ✅ Retourne un tableau vide en cas d'erreur
    }
}




// **Créer le tableau des horaires**
function createTable() {
    tableHead.innerHTML = "<th>Jour</th>";
    hours.forEach(hour => {
        let th = document.createElement("th");
        th.textContent = hour;
        tableHead.appendChild(th);
    });

    tableBody.innerHTML = "";
    days.forEach(day => {
        let row = document.createElement("tr");
        let dayCell = document.createElement("td");
        dayCell.textContent = day;
        row.appendChild(dayCell);

        hours.forEach(hour => {
            let cell = document.createElement("td");
            cell.dataset.day = day;
            cell.dataset.hour = hour;
            cell.addEventListener("mousedown", startSelection);
            cell.addEventListener("mouseover", dragSelection);
            cell.addEventListener("mouseup", stopSelection);
            row.appendChild(cell);
        });

        tableBody.appendChild(row);
    });

    document.addEventListener("mouseup", stopSelection);
}

// **Importer les données depuis la BDD**
function importData(data = importedData) {
    if (!Array.isArray(data)) {
        console.error("Erreur : `data` n'est pas un tableau", data);
        return;
    }

    data.forEach(entry => {
        let start = parseDate(entry.date_debut);
        let end = parseDate(entry.date_fin);

        if (!start.day || !end.day) return;

        let existingPeriod = periods.find(p => p.name === entry.nom);
        if (!existingPeriod) {
            let newPeriod = { name: entry.nom, color: getRandomColor() };
            periods.push(newPeriod);
            updatePeriodList();
        }

        let periodColor = periods.find(p => p.name === entry.nom)?.color || "#000000";

        let startIndex = hours.indexOf(start.hour);
        let endIndex = hours.indexOf(end.hour) - 1;

        // 🔍 **Correction pour afficher la case 23:00 si la période finit à 00:00**
        if (end.hour === "00:00") {
            endIndex = hours.indexOf("23:00"); // Force à colorier 23h
        }

        if (startIndex !== -1 && endIndex !== -1) {
            for (let i = startIndex; i <= endIndex; i++) {
                let cell = document.querySelector(`td[data-day="${start.day}"][data-hour="${hours[i]}"]`);
                if (cell) {
                    cell.classList.add("active");
                    cell.style.backgroundColor = periodColor;
                    cell.dataset.period = entry.nom;
                    cell.innerText = entry.nom.charAt(0).toUpperCase();

                    // **Centrage horizontal & vertical + style texte**
                 
                    
                    cell.style.alignItems = "center";    // ✅ Centre verticalement
                    cell.style.justifyContent = "center"; // ✅ Centre horizontalement
                    cell.style.fontWeight = "bold";
                    cell.style.color = "white"; // ✅ S'assurer que le texte est visible
                    cell.style.textAlign = "center"; // ✅ Alignement du texte pour les autres cas

                }
            }
        }
    });
}




// **Afficher un toast Bootstrap**
export function showToast(message) {
    document.getElementById("toastMessage").textContent = message;
    let toast = new bootstrap.Toast(document.getElementById("errorToast"));
    toast.show();
}

// **Sélection et suppression de périodes**
function startSelection(event) {
    let cell = event.target;
    if (!cell.dataset.day) return;

    isDragging = true;
    
    if (cell.classList.contains("active")) {
        isErasing = true;
        removePeriod(cell);
    } else if (activePeriod !== null) {
        isErasing = false;
        applyPeriod(cell);
    }
}

function dragSelection(event) {
    if (isDragging) {
        let cell = event.target;
        if (!cell.dataset.day) return;
        if (isErasing) {
            removePeriod(cell);
        } else {
            applyPeriod(cell);
        }
    }
}

function stopSelection() {
    isDragging = false;
    isErasing = false;
}

// **Appliquer une période à une cellule**
function applyPeriod(cell) {
    if (!cell.dataset.day) return;
    if (activePeriod === null || periods[activePeriod] === undefined) {
        showToast("Sélectionnez d'abord une période.");
        return;
    }

    const { name, color } = periods[activePeriod];

    cell.classList.add("active");
    cell.style.backgroundColor = color;
    cell.dataset.period = name;
    cell.innerText = name.charAt(0).toUpperCase(); // ✅ Ajoute la première lettre

    //  **Centrage du texte et amélioration de la visibilité**
   
    cell.style.alignItems = "center";    // ✅ Centre verticalement
    cell.style.justifyContent = "center"; // ✅ Centre horizontalement
    cell.style.fontWeight = "bold";
    cell.style.color = "white"; // ✅ Assurer la lisibilité
    cell.style.textAlign = "center"; // ✅ Alignement du texte
}


// **Définir la période active**
export function setActivePeriod(index) {
    if (periods[index] === undefined) return;
    activePeriod = index;
    updatePeriodList();
}

// **Supprimer une période d'une cellule**
export function removePeriod(cell) {
    if (!cell.dataset.day) return;
    cell.classList.remove("active");
    cell.style.backgroundColor = "";
    delete cell.dataset.period;
}

const predefinedColors = [

    "#5dade2", // Bleu pastel foncé
    "#48c9b0", // Vert d'eau foncé

    "#f39c12", // Orange doux foncé
    "#17a589", // Turquoise profond
    "#e74c3c"  // Rouge corail adouci
];



let colorIndex = 0;

export function getRandomColor() {
    const color = predefinedColors[colorIndex % predefinedColors.length]; // Sélectionne une couleur en boucle
    colorIndex++; // Incrémente l'index
    return color;
}



export function parseDate(dateStr) {
    const dateObj = new Date(dateStr);
    const dayIndex = dateObj.getDay();
    const hour = dateObj.getHours().toString().padStart(2, "0") + ":00";

    const dayMap = { 1: "Lundi", 4: "Jeudi", 5: "Vendredi", 6: "Samedi", 0: "Dimanche" };
    const day = dayMap[dayIndex] || null;

    console.log(`📅 Parsing date ${dateStr} -> day: ${day}, hour: ${hour}`);

    return { day, hour };
}

// **Mettre à jour la liste des périodes**
export function updatePeriodList() {
    periodListDiv.innerHTML = "";
    periods.forEach((period, index) => {
        if (!period.name) return;
        let periodItem = document.createElement("div");
        periodItem.classList.add("period-item", "p-2", "d-flex", "align-items-center", "gap-2");
        periodItem.style.backgroundColor = period.color;
        periodItem.dataset.index = index;

        let nameInput = document.createElement("input");
        nameInput.type = "text";
        nameInput.value = period.name;
        nameInput.classList.add("form-control", "form-control-sm");
        nameInput.onchange = (e) => editPeriodName(index, e.target.value);

        let selectBtn = document.createElement("button");
        selectBtn.textContent = "✔";
        selectBtn.classList.add("btn", "btn-sm", "btn-success");
        selectBtn.onclick = () => setActivePeriod(index);

        let deleteBtn = document.createElement("button");
        deleteBtn.innerHTML = "❌";
        deleteBtn.classList.add("btn", "btn-sm", "btn-danger");
        deleteBtn.onclick = () => deletePeriod(index, period.name);

        periodItem.appendChild(nameInput);
        periodItem.appendChild(selectBtn);
        periodItem.appendChild(deleteBtn);

        if (activePeriod === index) {
            periodItem.classList.add("active");
        }

        periodListDiv.appendChild(periodItem);
    });
}

// **Supprimer une période**
export function deletePeriod(index, periodName) {
    periods.splice(index, 1);
    activePeriod = null;
    document.querySelectorAll(`td[data-period="${periodName}"]`).forEach(cell => removePeriod(cell));
    updatePeriodList();
}

// **Modifier le nom d'une période**
export function editPeriodName(index, newName) {
    if (!newName.trim()) return showToast("Le nom de la période ne peut pas être vide.");
    periods[index].name = newName;
    updatePeriodList();
}

// **Attacher `grid_periode_manage` à `window`**
window.grid_periode_manage = grid_periode_manage;
