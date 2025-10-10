import { resetFilterState } from './Clinical_filter.js';
import { notify } from '../clinical/utils.js';

export function Clinical_filter_reset() {
  resetFilterState();
  notify('Filtres réinitialisés.');
}
