import { showToast } from '../classes/toastHelper.js';
import FetchHandler from '../classes/FetchHandler.js';
import { renderScheduleTable } from './render.js';

let exampleData = [];

export async function loadScheduleData() {
    const fetchHandler = new FetchHandler();
    try {
        const response = await fetchHandler.sendRequest({ node: "action", action: "schedule_data" });
        if (response.success && response.data) {
            exampleData = response.data;
            showToast({ message: "Données chargées avec succès" });
            renderScheduleTable(exampleData);
        } else {
            showToast({ message: response.message }, true);
        }
    } catch (error) {
        showToast({ message: "Erreur lors de la récupération des données" }, true);
    }
}

export function getExampleData() {
    return exampleData;
}
