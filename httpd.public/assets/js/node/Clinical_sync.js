import { notify } from '../clinical/utils.js';

export function Clinical_sync() {
  const indicator = document.getElementById('syncIndicator');
  const syncText = document.getElementById('syncText');

  if (indicator) {
    indicator.classList.add('syncing');
  }
  if (syncText) {
    syncText.textContent = 'Synchronisation en cours…';
  }

  setTimeout(() => {
    if (indicator) {
      indicator.classList.remove('syncing');
      indicator.classList.add('text-success');
    }
    if (syncText) {
      syncText.textContent = 'Synchronisé';
    }
    notify('Contenu synchronisé avec succès.');
  }, 800);
}
