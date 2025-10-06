import { days, hours } from "./grid_periode_manage.js";
import FetchHandler from '../classes/FetchHandler.js';

export function grid_periode_save(param) {
    const {horaireId} = param;
    let schedule = {};

    // 🔄 **Récupération des périodes sélectionnées**
    days.forEach(day => {
        let periodsData = {};

        hours.forEach(hour => {
            let cell = document.querySelector(`td[data-day="${day}"][data-hour="${hour}"]`);
            if (cell && cell.classList.contains("active")) {
                let periodName = cell.dataset.period;
                if (!periodsData[periodName]) {
                    periodsData[periodName] = [];
                }
                periodsData[periodName].push(hour);
            }
        });

        schedule[day] = {};
        for (const [period, times] of Object.entries(periodsData)) {
            schedule[day][period] = times;
        }
    });

    // 🔄 **Conversion au format BDD**
    const formattedData = formatScheduleForDatabase(schedule, param.horaireId || 1, param.clientId || 0);

    // 📡 **Utilisation de FetchHandler**
    const fetchHandler = new FetchHandler(true);

    fetchHandler.sendRequest({
        node: "action",
        action: "periode_update",
        periodes: formattedData,
        horaire_id: horaireId
    })
    .then(result => {
        console.log("✅ Réponse du serveur :", result);
    })
    .catch(error => {
        console.error("❌ Erreur lors de l'envoi :", error);
    });
}

// ✅ **Fonction pour formater les données pour la BDD**
function formatScheduleForDatabase(schedule, horaireId = 1, clientId = 0) {
    const dayMap = { 
        "Jeudi": "2025-06-26", 
        "Vendredi": "2025-06-27", 
        "Samedi": "2025-06-28", 
        "Dimanche": "2025-06-29", 
        "Lundi": "2025-06-30" 
    };
    let formattedData = [];

    Object.entries(schedule).forEach(([day, periods]) => {
        const date = dayMap[day]; // Récupération de la date associée au jour
        let dateObj = new Date(date); // Convertit en objet Date pour manipuler facilement

        Object.entries(periods).forEach(([periodName, hours]) => {
            let mergedRanges = mergeTimeRanges(hours);

            mergedRanges.forEach(({ debut, fin }) => {
                // Calcul de la date de fin si l'heure dépasse 24h
                let nextDateObj = new Date(dateObj);
                nextDateObj.setDate(nextDateObj.getDate() + 1); // Ajoute un jour
                let nextDate = nextDateObj.toISOString().split('T')[0]; // Format YYYY-MM-DD

                formattedData.push({
                    horaire_id: horaireId,
                    nom: periodName,
                    date_debut: `${date} ${debut}:00`,
                    date_fin: fin === 24 ? `${nextDate} 00:00` : `${date} ${fin}:00`,
                    client_id: clientId
                });
            });
        });
    });

    return formattedData;
}


// ✅ **Fusionne les heures consécutives**
// ✅ **Fusionne les heures consécutives**
function mergeTimeRanges(hours) {
    if (hours.length === 0) return [];

    let merged = [];
    let start = hours[0];
    let end = start;

    for (let i = 1; i < hours.length; i++) {
        let currentHour = hours[i];
        let prevHour = hours[i - 1];

        if (parseInt(currentHour) === parseInt(prevHour) + 1) {
            end = currentHour;
        } else {
            merged.push({ debut: start, fin: parseInt(end) + 1 }); // 🔼 Incrémente ici
            start = currentHour;
            end = start;
        }
    }

    merged.push({ debut: start, fin: parseInt(end) + 1 }); // 🔼 Incrémente ici
    return merged;
}

