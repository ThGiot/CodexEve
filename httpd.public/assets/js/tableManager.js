export function initializeTables() {
    const tables = document.querySelectorAll('.iamtable');
    if (!tables.length) {
        console.info("Aucune table trouvée avec la classe 'iamtable'.");
        return;
    }

    tables.forEach(table => {
        try {
            const id = table.id;
            const dataList = JSON.parse(table.getAttribute('data-list'));
            const options = {
                valueNames: dataList.valueNames,
                page: dataList.page,
                pagination: dataList.pagination
            };
            new List(id, options); // Initialise la table
        } catch (error) {
            console.error("Erreur lors de l'initialisation des tables :", error);
        }
    });
}
