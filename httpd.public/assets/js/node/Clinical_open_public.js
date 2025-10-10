import { findFicheById, openPublicLink } from '../clinical/utils.js';

export function Clinical_open_public(params = {}) {
  const { id } = params;
  const fiche = findFicheById(id);
  openPublicLink(fiche);
}
