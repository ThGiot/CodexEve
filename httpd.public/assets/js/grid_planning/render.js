import { showTotalPosts } from './analytics.js';

const HOURS = ["00h", "01h", "02h", "03h", "04h", "05h", "06h", "07h", "08h", "09h", "10h", "11h", "12h", "13h", "14h", "15h", "16h", "17h", "18h", "19h", "20h", "21h", "22h", "23h"];
const DAYS = ["Jeu", "Ven", "Sam", "Dim", "Lun"];
const EARLY_HOURS = ["03h", "04h", "05h", "06h", "07h"];
const dayColors = {
    "Jeu": "#ffebcd",
    "Ven": "#ffe4e1",
    "Sam": "#e6e6fa",
    "Dim": "#dff0d8",
    "Lun": "#d9edf7"
};

export function renderScheduleTable(data) {
    const scheduleTableElement = document.getElementById('scheduleTable');
    const selectedDays = Array.from(document.getElementById('filterDay')?.selectedOptions || []).map(option => option.value);
    const ignoreEarlyHours = document.getElementById('ignoreEarlyHours')?.checked;

    if (!scheduleTableElement) return console.warn("Élément #scheduleTable introuvable.");
    if (!Array.isArray(data)) return console.error("Données invalides:", data);

    const daysToRender = selectedDays.length > 0 ? selectedDays : DAYS;

    let html = `<table class="table table-bordered table-hover table-schedule"><thead><tr class="table-primary">
    <th>N°</th><th>Poste</th><th class="fixed">Zone</th><th class="fixed">Association</th><th class="fixed">Type</th><th class="fixed">Période</th>`;

    daysToRender.forEach((day) => {
        HOURS.forEach((hour) => {
            if (!(ignoreEarlyHours && EARLY_HOURS.includes(hour))) {
                const bgColor = dayColors[day] || "#ffffff";
                html += `<th style="background-color: ${bgColor};">${day}-${hour}</th>`;
            }
        });
    });

    html += '</tr></thead><tbody>';

    data.forEach(post => {
        html += `<tr data-poste-id="${post.poste_id}">`;
        html += `<td class="fixed clickable-post" data-poste-id="${post.poste_id}">${post.numero}</td>`;
        html += `<td class="fixed clickable-post" data-poste-id="${post.poste_id}">${post.poste}</td>`;
        html += `<td class="fixed clickable-post" data-poste-id="${post.poste_id}">${post.zone}</td>`;
        html += `<td class="fixed clickable-post" data-poste-id="${post.poste_id}">${post.association}</td>`;
        html += `<td class="fixed clickable-post" data-poste-id="${post.poste_id}">${post.type}</td>`;
        html += `<td class="fixed clickable-post" data-poste-id="${post.poste_id}">${post.periode}</td>`;
        html += '</tr>';
    });

    html += '</tbody></table>';
    scheduleTableElement.innerHTML = html;
}
