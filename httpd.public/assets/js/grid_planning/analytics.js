import { getExampleData } from './dataLoader.js';
import { applyFilters } from './filters.js';

export function showTotalPosts(day, hour) {
    const filters = {
        zone: document.getElementById('filterZone')?.value.toLowerCase() || "",
        type: document.getElementById('filterType')?.value.toLowerCase() || "",
        search: document.getElementById('searchPost')?.value.toLowerCase() || "",
        association: document.getElementById('filterAssociation')?.value.toLowerCase() || "",
        days: Array.from(document.getElementById('filterDay')?.selectedOptions || []).map(option => option.value)
    };

    const total = applyFilters(getExampleData(), filters).reduce((count, post) => {
        const key = `${day}-${hour}`;
        return count + (post.heures?.[key] ? 1 : 0);
    }, 0);

    const infoElement = document.getElementById('totalPosts');
    if (infoElement) {
        infoElement.className = "alert";
        infoElement.textContent = `Total de postes présents le ${day} à ${hour}: ${total}`;
    }
}
