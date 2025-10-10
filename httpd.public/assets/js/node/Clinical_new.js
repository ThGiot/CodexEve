import {
  resetEditorFields,
  setCurrentFicheId,
  updatePreview,
  updateTimeline,
  highlightRow,
  notify
} from '../clinical/utils.js';

export function Clinical_new() {
  setCurrentFicheId('');
  resetEditorFields();
  updatePreview(null);
  updateTimeline([]);
  highlightRow(null);

  const titleField = document.getElementById('sheetTitle');
  if (titleField) {
    titleField.focus();
  }

  notify('Nouvelle fiche prête à être complétée.');
}
