import { getExampleData } from './dataLoader.js';
import { renderScheduleTable } from './render.js';

export function applyFilters(data, filters) {
    return data.filter(post => {
        const matchZone = !filters.zone || post.zone.toLowerCase() === filters.zone;
        const matchType = !filters.type || post.type.toLowerCase() === filters.type;
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

export function filterTable() {
    const filters = {
        zone: document.getElementById('filterZone')?.value.toLowerCase() || "",
        type: document.getElementById('filterType')?.value.toLowerCase() || "",
        search: document.getElementById('searchPost')?.value.toLowerCase() || "",
        association: document.getElementById('filterAssociation')?.value.toLowerCase() || "",
        days: Array.from(document.getElementById('filterDay')?.selectedOptions || []).map(option => option.value)
    };

    filters.days = filters.days.sort((a, b) => ["Jeu", "Ven", "Sam", "Dim", "Lun"].indexOf(a) - ["Jeu", "Ven", "Sam", "Dim", "Lun"].indexOf(b));

    const filteredData = applyFilters(getExampleData(), filters);
    renderScheduleTable(filteredData);
}
