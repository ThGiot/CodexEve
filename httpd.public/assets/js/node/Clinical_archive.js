import {
  findFicheById,
  resolveStatus,
  composeFiche,
  upsertFiche,
  refreshTableRow,
  appendTableRow,
  updatePreview,
  updateTimeline,
  highlightRow,
  buildHistoryEntry,
  mergeHistory,
  notify,
  getCurrentFicheId
} from '../clinical/utils.js';
import { applyCurrentFilters } from './Clinical_filter.js';

export function Clinical_archive(params = {}) {
  const { id } = params;
  const fiche = findFicheById(id);

  if (!fiche) {
    notify('Impossible d\'archiver cette fiche.', true);
    return;
  }

  const historyEntry = buildHistoryEntry({
    title: 'Fiche archivée',
    description: 'Archivée depuis le module de gestion.',
    variant: 'warning'
  });

  const archivedFiche = composeFiche(fiche, {
    status: resolveStatus('archived'),
    alert: { variant: 'secondary', message: 'Cette fiche est archivée et n\'est plus modifiable.' },
    history: mergeHistory(fiche, historyEntry)
  });

  archivedFiche.status = resolveStatus('archived');
  upsertFiche(archivedFiche);

  if (!refreshTableRow(archivedFiche)) {
    appendTableRow(archivedFiche);
  }

  if (String(getCurrentFicheId()) === String(archivedFiche.id)) {
    updatePreview(archivedFiche);
    updateTimeline(archivedFiche.history);
  }

  highlightRow(archivedFiche.id);
  notify(`Fiche « ${archivedFiche.title} » archivée.`);
  applyCurrentFilters();
}
