import {
  filterRows,
  getFilterControls,
  setActiveStatusButton,
  setActiveTag
} from '../clinical/utils.js';

const defaultState = {
  statut: 'all',
  system: '',
  author: '',
  tag: '',
  search: ''
};

let filterState = { ...defaultState };

function matchesFilters(fiche, marker, state) {
  const status = fiche?.status?.slug || marker?.dataset?.clinicalStatus || '';
  if (state.statut && state.statut !== 'all' && status !== state.statut) {
    return false;
  }

  const system = fiche?.system?.slug || marker?.dataset?.clinicalSystem || '';
  if (state.system && system !== state.system) {
    return false;
  }

  const author = fiche?.ownerSlug || marker?.dataset?.clinicalAuthor || '';
  if (state.author && author !== state.author) {
    return false;
  }

  if (state.tag) {
    const tags = Array.isArray(fiche?.tags)
      ? fiche.tags
      : (marker?.dataset?.clinicalTags || '').split('|');
    const hasTag = tags.some((tag) => tag && tag.toLowerCase() === state.tag.toLowerCase());
    if (!hasTag) {
      return false;
    }
  }

  if (state.search) {
    const row = marker?.closest('tr');
    const haystack = row ? row.textContent.toLowerCase() : '';
    if (!haystack.includes(state.search.toLowerCase())) {
      return false;
    }
  }

  return true;
}

function applyFilterState() {
  filterRows((fiche, marker) => matchesFilters(fiche, marker, filterState));
}

function syncControls(changedParams = {}) {
  const controls = getFilterControls();

  if (Object.prototype.hasOwnProperty.call(changedParams, 'system') && controls.systemSelect) {
    controls.systemSelect.value = filterState.system;
  }
  if (Object.prototype.hasOwnProperty.call(changedParams, 'author') && controls.authorSelect) {
    controls.authorSelect.value = filterState.author;
  }
  if (Object.prototype.hasOwnProperty.call(changedParams, 'search') && controls.searchInput) {
    controls.searchInput.value = filterState.search;
  }
  if (Object.prototype.hasOwnProperty.call(changedParams, 'statut')) {
    setActiveStatusButton(filterState.statut || 'all');
  }
  if (Object.prototype.hasOwnProperty.call(changedParams, 'tag')) {
    setActiveTag(filterState.tag || '');
  }
}

export function getFilterState() {
  return { ...filterState };
}

export function resetFilterState() {
  filterState = { ...defaultState };
  syncControls({ statut: true, tag: true, system: true, author: true, search: true });
  applyFilterState();
  return getFilterState();
}

export function applyCurrentFilters() {
  applyFilterState();
}

export function Clinical_filter(params = {}) {
  const nextState = { ...filterState };

  if (Object.prototype.hasOwnProperty.call(params, 'statut')) {
    nextState.statut = params.statut || 'all';
  }
  if (Object.prototype.hasOwnProperty.call(params, 'system')) {
    nextState.system = params.system || '';
  }
  if (Object.prototype.hasOwnProperty.call(params, 'author')) {
    nextState.author = params.author || '';
  }
  if (Object.prototype.hasOwnProperty.call(params, 'search')) {
    nextState.search = params.search || '';
  }
  if (Object.prototype.hasOwnProperty.call(params, 'tag')) {
    const incoming = params.tag || '';
    if (incoming === '') {
      nextState.tag = '';
    } else {
      nextState.tag = filterState.tag === incoming ? '' : incoming;
    }
  }

  filterState = nextState;
  syncControls(params);
  applyFilterState();
}
