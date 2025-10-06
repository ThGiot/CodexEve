// Importer uniquement ce dont on a besoin
import { setActivePeriod, updatePeriodList, showToast, periods } from "./grid_periode_manage.js";

// Fonction pour ajouter une période
export function grid_hpm_add_periode(param) {
    const name = document.querySelector("#period-name").value.trim();
    const color = document.querySelector("#period-color").value;

    if (!name) {
        return showToast("Le nom de la période est requis.");
    }

    // Vérifier si la période existe déjà
    if (periods.some(period => period.name === name)) {
        return showToast("Une période avec ce nom existe déjà.");
    }

    // Ajouter une nouvelle période
    let newPeriod = { name, color };
    periods.push(newPeriod);
    updatePeriodList();

    // Sélectionner automatiquement la nouvelle période
    setActivePeriod(periods.length - 1);
}
