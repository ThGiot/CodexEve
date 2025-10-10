import {
  findFicheById,
  setCurrentFicheId,
  updatePreview,
  updateTimeline,
  highlightRow,
  notify
} from '../clinical/utils.js';

export function Clinical_preview(params = {}) {
  const { id } = params;
  const fiche = findFicheById(id);

  if (!fiche) {
    notify('Impossible de prévisualiser cette fiche.', true);
    return;
  }

  setCurrentFicheId(fiche.id);
  updatePreview(fiche);
  updateTimeline(fiche.history);
  highlightRow(fiche.id);

  notify(`Prévisualisation mise à jour pour « ${fiche.title} ».`);
}
