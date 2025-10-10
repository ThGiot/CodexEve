import {
  getFormValues,
  getCurrentFicheId,
  setCurrentFicheId,
  findFicheById,
  composeFiche,
  upsertFiche,
  refreshTableRow,
  appendTableRow,
  updatePreview,
  updateTimeline,
  highlightRow,
  notify,
  generateFicheId,
  buildHistoryEntry,
  mergeHistory
} from '../clinical/utils.js';
import { applyCurrentFilters } from './Clinical_filter.js';

export function Clinical_save() {
  const values = getFormValues();

  if (!values) {
    notify('Formulaire introuvable.', true);
    return;
  }

  if (!values.title) {
    notify('Le titre de la fiche est requis pour enregistrer.', true);
    return;
  }

  const currentId = getCurrentFicheId();

  if (currentId) {
    const existing = findFicheById(currentId);
    if (!existing) {
      notify('Impossible de trouver la fiche à mettre à jour.', true);
      return;
    }

    const historyEntry = buildHistoryEntry({
      title: 'Mise à jour',
      description: `Modifiée par ${values.owner || "l'équipe"}.`,
      variant: 'info'
    });

    const updatedFiche = composeFiche(existing, {
      ...values,
      history: mergeHistory(existing, historyEntry)
    });

    upsertFiche(updatedFiche);
    refreshTableRow(updatedFiche);
    highlightRow(updatedFiche.id);
    updatePreview(updatedFiche);
    updateTimeline(updatedFiche.history);
    notify('Modifications enregistrées.');
  } else {
    const newId = generateFicheId();
    const historyEntry = buildHistoryEntry({
      title: 'Fiche créée',
      description: `Créée par ${values.owner || "l'équipe"}.`,
      variant: 'success'
    });

    const newFiche = composeFiche({ id: newId, version: '1.0', history: [] }, {
      ...values,
      history: [historyEntry]
    });
    newFiche.id = newId;

    upsertFiche(newFiche);
    appendTableRow(newFiche);
    setCurrentFicheId(newId);
    highlightRow(newId);
    updatePreview(newFiche);
    updateTimeline(newFiche.history);
    notify('Nouvelle fiche enregistrée.');
  }

  applyCurrentFilters();
}
