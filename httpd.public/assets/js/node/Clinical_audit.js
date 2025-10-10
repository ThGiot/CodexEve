import { notify } from '../clinical/utils.js';

export function Clinical_audit() {
  const timeline = document.getElementById('clinicalTimeline');
  if (timeline && typeof timeline.scrollIntoView === 'function') {
    timeline.scrollIntoView({ behavior: 'smooth', block: 'center' });
  }
  notify('Historique mis en évidence. Faites défiler pour consulter les évènements.');
}
