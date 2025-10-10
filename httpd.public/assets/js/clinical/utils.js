import { showToast } from '../classes/toastHelper.js';

const STATUS_STYLES = {
  draft: { label: 'Brouillon', badge: 'warning' },
  review: { label: 'En relecture', badge: 'secondary' },
  ready: { label: 'Prêt à publier', badge: 'info' },
  published: { label: 'Publié', badge: 'success' },
  archived: { label: 'Archivé', badge: 'secondary' }
};

const SYSTEM_STYLES = {
  airway: { label: 'Voies aériennes', badge: 'primary' },
  cardio: { label: 'Cardiologie', badge: 'danger' },
  neuro: { label: 'Neurologie', badge: 'info' },
  pediatrie: { label: 'Pédiatrie', badge: 'warning' },
  smur: { label: 'SMUR / Pré-hospitalier', badge: 'info' },
  urgence: { label: 'Urgence', badge: 'info' },
  allergologie: { label: 'Allergologie', badge: 'danger' }
};

const ALERT_ICONS = {
  warning: 'fa-triangle-exclamation',
  info: 'fa-circle-info',
  success: 'fa-check-circle',
  danger: 'fa-skull-crossbones',
  secondary: 'fa-circle-info'
};

const TIMELINE_VARIANTS = {
  success: { className: 'bg-success-subtle text-success', icon: 'fa-check' },
  warning: { className: 'bg-warning-subtle text-warning', icon: 'fa-pen' },
  info: { className: 'bg-info-subtle text-info', icon: 'fa-upload' },
  danger: { className: 'bg-danger-subtle text-danger', icon: 'fa-triangle-exclamation' },
  secondary: { className: 'bg-secondary-subtle text-secondary', icon: 'fa-clock' }
};

let cachedFiches = null;

export function getClinicalFiches() {
  if (cachedFiches !== null) {
    return cachedFiches;
  }

  const holder = document.getElementById('clinicalFichesData');
  if (!holder) {
    cachedFiches = [];
    return cachedFiches;
  }

  try {
    const parsed = JSON.parse(holder.textContent || '[]');
    cachedFiches = Array.isArray(parsed) ? parsed : [];
  } catch (error) {
    console.error('Clinical utils: impossible de parser les données JSON des fiches.', error);
    cachedFiches = [];
  }

  return cachedFiches;
}

export function updateDataset(fiches) {
  cachedFiches = Array.isArray(fiches) ? fiches : [];
  const holder = document.getElementById('clinicalFichesData');
  if (holder) {
    holder.textContent = JSON.stringify(cachedFiches);
  }
}

export function findFicheById(id) {
  if (id === undefined || id === null || id === '') {
    return undefined;
  }
  return getClinicalFiches().find((fiche) => String(fiche.id) === String(id));
}

export function notify(message, isError = false) {
  if (!message) {
    return;
  }
  showToast({ message }, isError);
}

export function slugify(value) {
  if (!value) {
    return '';
  }
  return value
    .toString()
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/^-+|-+$/g, '')
    .replace(/-{2,}/g, '-');
}

export function formatDisplayDate(value) {
  if (!value) {
    return '';
  }

  const parsed = new Date(value);
  if (Number.isNaN(parsed.getTime())) {
    return value;
  }

  return new Intl.DateTimeFormat('fr-FR').format(parsed);
}

export function resolveStatus(slug) {
  const key = slug || 'draft';
  const fallback = { label: key.charAt(0).toUpperCase() + key.slice(1), badge: 'secondary' };
  const descriptor = STATUS_STYLES[key] || fallback;
  return { slug: key, ...descriptor };
}

export function resolveSystem(slug) {
  const key = slug || 'airway';
  const fallback = { label: key.charAt(0).toUpperCase() + key.slice(1), badge: 'secondary' };
  const descriptor = SYSTEM_STYLES[key] || fallback;
  return { slug: key, ...descriptor };
}

export function setCurrentFicheId(id) {
  const form = document.getElementById('clinical_editor_form');
  if (!form) {
    return;
  }
  if (id === undefined || id === null || id === '') {
    delete form.dataset.currentId;
    form.dataset.mode = 'create';
  } else {
    form.dataset.currentId = String(id);
    form.dataset.mode = 'edit';
  }
}

export function getCurrentFicheId() {
  const form = document.getElementById('clinical_editor_form');
  if (!form) {
    return '';
  }
  return form.dataset.currentId || '';
}

function setFieldValue(form, selector, value) {
  const field = form.querySelector(selector);
  if (!field) {
    return;
  }
  field.value = value ?? '';
  field.dispatchEvent(new Event('change', { bubbles: true }));
}

export function hydrateEditor(fiche) {
  const form = document.getElementById('clinical_editor_form');
  if (!form) {
    return;
  }

  const data = fiche || {};
  setFieldValue(form, '#sheetTitle', data.title || '');
  setFieldValue(form, '#sheetSlug', data.slug || slugify(data.title || ''));
  setFieldValue(form, '#sheetSystem', data.system?.slug || '');
  setFieldValue(form, '#sheetStatus', data.status?.slug || 'draft');
  setFieldValue(form, '#sheetTags', Array.isArray(data.tags) ? data.tags.join(', ') : '');
  setFieldValue(form, '#sheetSummary', data.summary || '');
  setFieldValue(form, '#sheetContent', data.content || '');
  setFieldValue(form, '#sheetOwner', data.owner || '');
  setFieldValue(form, '#sheetNextReview', data.nextReview || '');

  const attachments = form.querySelector('#sheetAttachments');
  if (attachments) {
    attachments.value = '';
  }
}

export function resetEditorFields() {
  hydrateEditor({
    status: resolveStatus('draft'),
    system: resolveSystem('airway'),
    tags: [],
    summary: '',
    content: '',
    owner: '',
    nextReview: ''
  });
}

export function highlightRow(id) {
  const rows = document.querySelectorAll('#clinicalSheetsTable tbody tr');
  rows.forEach((row) => row.classList.remove('table-active'));
  if (!id) {
    return;
  }
  const marker = document.querySelector(`#clinicalSheetsTable [data-fiche-id="${id}"]`);
  if (marker) {
    const row = marker.closest('tr');
    if (row) {
      row.classList.add('table-active');
    }
  }
}

function createBadgeElement(label, tone) {
  const badge = document.createElement('span');
  badge.className = `badge bg-${tone}-subtle text-${tone}`;
  badge.textContent = label;
  return badge;
}

function renderBadgeHtml(label, tone) {
  return `<span class="badge bg-${tone}-subtle text-${tone}">${label}</span>`;
}

export function updatePreview(fiche) {
  const previewTitle = document.getElementById('clinicalPreviewTitle');
  const tagsContainer = document.getElementById('clinicalPreviewTags');
  const summary = document.getElementById('clinicalPreviewSummary');
  const alertWrapper = document.getElementById('clinicalPreviewAlert');
  const alertIcon = document.getElementById('clinicalPreviewAlertIcon');
  const alertText = document.getElementById('clinicalPreviewAlertText');
  const version = document.getElementById('clinicalPreviewVersion');
  const published = document.getElementById('clinicalPreviewPublished');
  const validatedBy = document.getElementById('clinicalPreviewValidatedBy');
  const reviewDue = document.getElementById('clinicalPreviewReviewDue');

  const data = fiche || {};
  const system = resolveSystem(data.system?.slug || data.system);

  if (previewTitle) {
    previewTitle.textContent = data.title || 'Nouvelle fiche clinique';
  }

  if (tagsContainer) {
    tagsContainer.innerHTML = '';
    const systemBadge = createBadgeElement(system.label, system.badge);
    tagsContainer.appendChild(systemBadge);

    if (Array.isArray(data.tags)) {
      data.tags.forEach((tag) => {
        if (!tag) {
          return;
        }
        const badge = createBadgeElement(tag, 'primary');
        tagsContainer.appendChild(badge);
      });
    }
  }

  if (summary) {
    summary.textContent = data.summary || 'Renseignez le contenu principal pour afficher un aperçu ici.';
  }

  if (alertWrapper) {
    alertWrapper.className = 'alert d-flex align-items-center gap-2';
    const variant = data.alert?.variant;
    const message = data.alert?.message;

    if (variant && message) {
      alertWrapper.classList.add(`alert-${variant}`);
      alertWrapper.dataset.alertVariant = variant;
      if (alertIcon) {
        alertIcon.className = `fas ${ALERT_ICONS[variant] || ALERT_ICONS.secondary}`;
      }
      if (alertText) {
        alertText.textContent = message;
      }
    } else {
      alertWrapper.classList.add('d-none');
      if (alertText) {
        alertText.textContent = '';
      }
    }
  }

  if (version) {
    version.textContent = data.version || '1.0';
  }
  if (published) {
    published.textContent = data.lastUpdated ? formatDisplayDate(data.lastUpdated) : '-';
  }
  if (validatedBy) {
    validatedBy.textContent = data.validatedBy || 'Validation en attente';
  }
  if (reviewDue) {
    reviewDue.textContent = data.nextReview ? formatDisplayDate(data.nextReview) : 'Non planifiée';
  }
}

export function updateTimeline(history = []) {
  const container = document.getElementById('clinicalTimeline');
  if (!container) {
    return;
  }
  container.innerHTML = '';

  if (!Array.isArray(history) || history.length === 0) {
    const emptyState = document.createElement('p');
    emptyState.className = 'text-700 mb-0';
    emptyState.textContent = 'Aucun événement enregistré pour cette fiche pour le moment.';
    container.appendChild(emptyState);
    return;
  }

  history.forEach((event) => {
    const wrapper = document.createElement('div');
    wrapper.className = 'timeline-item';

    const variant = TIMELINE_VARIANTS[event.variant] || TIMELINE_VARIANTS.secondary;

    const iconWrapper = document.createElement('div');
    iconWrapper.className = `timeline-icon ${variant.className}`;
    const icon = document.createElement('span');
    icon.className = `fas ${variant.icon}`;
    iconWrapper.appendChild(icon);

    const content = document.createElement('div');
    content.className = 'timeline-content';

    const title = document.createElement('p');
    title.className = 'fs--1 mb-1 text-700';
    title.textContent = `${formatDisplayDate(event.date)} — ${event.title || ''}`;

    const description = document.createElement('p');
    description.className = 'fs--1 mb-0';
    description.textContent = event.description || '';

    content.appendChild(title);
    content.appendChild(description);

    wrapper.appendChild(iconWrapper);
    wrapper.appendChild(content);

    container.appendChild(wrapper);
  });
}

function updateRowCells(row, fiche) {
  const cells = row.querySelectorAll('td');
  if (cells.length < 5) {
    return;
  }

  const marker = row.querySelector('.clinical-row-label');
  const status = resolveStatus(fiche.status?.slug || fiche.status);
  const system = resolveSystem(fiche.system?.slug || fiche.system);

  if (marker) {
    marker.textContent = fiche.title;
    marker.dataset.clinicalStatus = status.slug;
    marker.dataset.clinicalSystem = system.slug;
    marker.dataset.clinicalAuthor = fiche.ownerSlug || slugify(fiche.owner || '');
    marker.dataset.clinicalTags = Array.isArray(fiche.tags) ? fiche.tags.join('|') : '';
  }

  cells[1].innerHTML = renderBadgeHtml(system.label, system.badge);
  cells[2].innerHTML = renderBadgeHtml(status.label, status.badge);
  cells[3].textContent = formatDisplayDate(fiche.lastUpdated);
  cells[4].textContent = fiche.owner || '';
}

export function refreshTableRow(fiche) {
  const marker = document.querySelector(`#clinicalSheetsTable [data-fiche-id="${fiche.id}"]`);
  if (!marker) {
    return false;
  }
  const row = marker.closest('tr');
  if (!row) {
    return false;
  }
  updateRowCells(row, fiche);
  return true;
}

export function appendTableRow(fiche) {
  const tbody = document.querySelector('#clinicalSheetsTable tbody');
  if (!tbody) {
    return;
  }

  const row = document.createElement('tr');
  const status = resolveStatus(fiche.status?.slug || fiche.status);
  const system = resolveSystem(fiche.system?.slug || fiche.system);
  const ownerSlug = fiche.ownerSlug || slugify(fiche.owner || '');
  const tags = Array.isArray(fiche.tags) ? fiche.tags.join('|') : '';

  row.innerHTML = `
    <td class="align-middle ps-3 procedure">
      <span class="clinical-row-label" data-fiche-id="${fiche.id}" data-clinical-status="${status.slug}" data-clinical-system="${system.slug}" data-clinical-author="${ownerSlug}" data-clinical-tags="${tags}">${fiche.title}</span>
    </td>
    <td class="align-middle ps-3 systeme">${renderBadgeHtml(system.label, system.badge)}</td>
    <td class="align-middle ps-3 statut">${renderBadgeHtml(status.label, status.badge)}</td>
    <td class="align-middle ps-3 maj">${formatDisplayDate(fiche.lastUpdated)}</td>
    <td class="align-middle ps-3 auteur">${fiche.owner || ''}</td>
    <td class="align-middle white-space-nowrap text-end pe-0">
      <button class="btn btn-sm btn-primary" onclick="node('Clinical_edit', {id: ${fiche.id}})">Modifier</button>
      <button class="btn btn-sm btn-outline-secondary" onclick="node('Clinical_preview', {id: ${fiche.id}})">Prévisualiser</button>
      <button class="btn btn-sm btn-outline-danger" onclick="node('Clinical_archive', {id: ${fiche.id}})">Archiver</button>
    </td>
  `;

  tbody.appendChild(row);
}

export function upsertFiche(updatedFiche) {
  const fiches = getClinicalFiches();
  const index = fiches.findIndex((fiche) => String(fiche.id) === String(updatedFiche.id));
  if (index >= 0) {
    fiches[index] = updatedFiche;
  } else {
    fiches.push(updatedFiche);
  }
  updateDataset(fiches);
}

export function generateFicheId() {
  const fiches = getClinicalFiches();
  if (fiches.length === 0) {
    return 1;
  }
  const ids = fiches.map((fiche) => Number(fiche.id) || 0);
  return Math.max(...ids) + 1;
}

export function buildHistoryEntry({ title, description, variant = 'info' }) {
  return {
    date: new Date().toISOString().slice(0, 10),
    title: title || 'Mise à jour',
    description: description || '',
    variant
  };
}

export function mergeHistory(fiche, entry) {
  const history = Array.isArray(fiche.history) ? fiche.history.slice() : [];
  history.unshift(entry);
  return history;
}

export function ensureOwnerSlug(fiche) {
  if (fiche.ownerSlug) {
    return fiche.ownerSlug;
  }
  const slug = slugify(fiche.owner || '');
  return slug || `owner-${fiche.id}`;
}

export function openPublicLink(fiche) {
  if (!fiche?.publicUrl) {
    notify('Aucun lien public disponible pour cette fiche.', true);
    return;
  }
  window.open(fiche.publicUrl, '_blank', 'noopener');
}

export function filterRows(predicate) {
  const rows = document.querySelectorAll('#clinicalSheetsTable tbody tr');
  rows.forEach((row) => {
    const marker = row.querySelector('.clinical-row-label');
    if (!marker) {
      row.style.display = '';
      return;
    }
    const ficheId = marker.dataset.ficheId;
    const fiche = findFicheById(ficheId);
    row.style.display = predicate(fiche, marker, row) ? '' : 'none';
  });
}

export function getFilterControls() {
  return {
    statusButtons: document.querySelectorAll('#clinicalStatusFilters [data-clinical-status]'),
    tagButtons: document.querySelectorAll('[data-clinical-tag]'),
    systemSelect: document.getElementById('filterSystem'),
    authorSelect: document.getElementById('filterAuthor'),
    searchInput: document.getElementById('filterSearch')
  };
}

export function setActiveStatusButton(slug) {
  const { statusButtons } = getFilterControls();
  statusButtons.forEach((button) => {
    if (button.dataset.clinicalStatus === slug) {
      button.classList.add('active');
    } else {
      button.classList.remove('active');
    }
  });
}

export function setActiveTag(tag) {
  const { tagButtons } = getFilterControls();
  tagButtons.forEach((button) => {
    if (button.dataset.clinicalTag === tag && tag) {
      button.classList.add('active');
    } else {
      button.classList.remove('active');
    }
  });
}

export function getFormValues() {
  const form = document.getElementById('clinical_editor_form');
  if (!form) {
    return null;
  }

  const value = (selector) => {
    const field = form.querySelector(selector);
    return field ? field.value.trim() : '';
  };

  const title = value('#sheetTitle');
  const slug = value('#sheetSlug') || slugify(title);
  const system = resolveSystem(value('#sheetSystem'));
  const status = resolveStatus(value('#sheetStatus'));
  const tags = value('#sheetTags')
    .split(',')
    .map((tag) => tag.trim())
    .filter(Boolean);

  return {
    title,
    slug,
    system,
    status,
    tags,
    summary: value('#sheetSummary'),
    content: value('#sheetContent'),
    owner: value('#sheetOwner'),
    nextReview: value('#sheetNextReview'),
    lastUpdated: new Date().toISOString().slice(0, 10),
    validatedBy: value('#sheetOwner') ? `Validée par ${value('#sheetOwner')}` : 'Validation en attente',
    publicUrl: `/procedures/${slug}`
  };
}

export function composeFiche(existing, updates) {
  const base = existing ? { ...existing } : {};
  const system = resolveSystem(updates.system?.slug || updates.system);
  const status = resolveStatus(updates.status?.slug || updates.status);
  const owner = updates.owner || base.owner || '';
  const ownerSlug = slugify(owner) || base.ownerSlug || '';

  return {
    id: base.id,
    title: updates.title || base.title || 'Nouvelle fiche',
    slug: updates.slug || base.slug || slugify(updates.title || base.title || ''),
    system,
    status,
    tags: Array.isArray(updates.tags) ? updates.tags : base.tags || [],
    summary: updates.summary ?? base.summary ?? '',
    content: updates.content ?? base.content ?? '',
    owner,
    ownerSlug: ownerSlug || `owner-${base.id || ''}`,
    nextReview: updates.nextReview || base.nextReview || '',
    lastUpdated: updates.lastUpdated || base.lastUpdated || new Date().toISOString().slice(0, 10),
    version: base.version || '1.0',
    validatedBy: updates.validatedBy || base.validatedBy || 'Validation en attente',
    publicUrl: updates.publicUrl || base.publicUrl || '',
    alert: updates.alert || base.alert || null,
    history: Array.isArray(updates.history) ? updates.history : base.history || []
  };
}
