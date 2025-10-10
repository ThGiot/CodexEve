import {
  findFicheById,
  setCurrentFicheId,
  hydrateEditor,
  updatePreview,
  updateTimeline,
  highlightRow,
  notify
} from '../clinical/utils.js';

export function Clinical_edit(params = {}) {
  const { id } = params;
  const fiche = findFicheById(id);

  if (!fiche) {
    notify('Impossible de charger la fiche demandée.', true);
    return;
  }

  setCurrentFicheId(fiche.id);
  hydrateEditor(fiche);
  updatePreview(fiche);
  updateTimeline(fiche.history);
  highlightRow(fiche.id);

  notify(`Fiche « ${fiche.title} » prête pour modification.`);
}
