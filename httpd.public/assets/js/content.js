import { fetchContent, reloadScripts } from './utils.js';
import { initFlatpickr } from './flatpickrManager.js';
import { initializeTables } from './tableManager.js';
import { renderScheduleTable, filterTable, loadScheduleData } from './grid_planning.js';

if ('serviceWorker' in navigator) {
    navigator.serviceWorker.getRegistrations().then(registrations => {
        registrations.forEach(registration => registration.unregister());
    });
}
const ContentManager = {
    historyStack: [],
    forwardStack: [],
    currentPageParams: { page: 0, extraParams: {} },

    async getContent(page = 0, extraParams = {}, isGoingBack = false) {
        const { currentPageParams, historyStack, forwardStack } = ContentManager;

        // Mise à jour de l'historique
        if (!isGoingBack) {
            forwardStack.length = 0; // Réinitialise le forwardStack
            historyStack.push(currentPageParams);
            window.history.pushState({ ...currentPageParams, direction: 'forward' }, null);
        } else {
            window.history.pushState({ ...currentPageParams, direction: 'backward' }, null);
        }

        ContentManager.currentPageParams = { page, extraParams };

        const content = document.getElementById('contentPage');
        content.innerHTML = '<div class="loading-spinner"></div>';

        try {
            const html = await fetchContent(page, extraParams);
            content.innerHTML = html;

            // Recharger les scripts nécessaires
            reloadScripts('assets/js/grid_planning.js');
            // Initialiser les composants spécifiques
            initFlatpickr();
            initializeTables();

            // Initialiser la planification uniquement si les éléments nécessaires sont présents
            if (document.getElementById('scheduleTable')) {
              
                renderScheduleTable();
                loadScheduleData();
            }
            
        } catch (error) {
            console.error('Erreur lors du chargement du contenu :', error);
        }
    }
};

export default ContentManager;

// Pour rétrocompatibilité
window.getContent = ContentManager.getContent.bind(ContentManager);
window.goBack = () => {
    const previousPageParams = ContentManager.historyStack.pop();
    if (previousPageParams) {
        ContentManager.forwardStack.push(ContentManager.currentPageParams);
        ContentManager.getContent(previousPageParams.page, previousPageParams.extraParams, true);
    }
};
window.goForward = () => {
    const nextPageParams = ContentManager.forwardStack.pop();
    if (nextPageParams) {
        ContentManager.historyStack.push(ContentManager.currentPageParams);
        ContentManager.getContent(nextPageParams.page, nextPageParams.extraParams, false);
    }
};
window.refreshContent = () => {
    ContentManager.getContent(
        ContentManager.currentPageParams.page,
        ContentManager.currentPageParams.extraParams
    );
};
