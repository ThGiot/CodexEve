const API_ENDPOINT = '../node.php';
const screenHistory = [];
const STORAGE_KEYS = {
    hospital: 'clinical-procedure-hospital',
    favorites: 'clinical-procedure-favorites',
    dataset: 'clinical-procedure-dataset'
};

const state = {
    loggedIn: false,
    hospital: localStorage.getItem(STORAGE_KEYS.hospital) || 'H1',
    favorites: JSON.parse(localStorage.getItem(STORAGE_KEYS.favorites) || '[]'),
    procedures: [],
    hospitals: [],
    meta: null,
    currentFilter: { type: null, system: null },
    datasetLoaded: false
};

const els = {
    screens: Array.from(document.querySelectorAll('.screen')),
    drawer: document.getElementById('drawer'),
    overlay: document.getElementById('overlay'),
    feed: document.getElementById('feed'),
    procList: document.getElementById('procList'),
    smurGrid: document.getElementById('smurGrid'),
    favList: document.getElementById('favList'),
    detailTitle: document.getElementById('detailTitle'),
    detailType: document.getElementById('detailType'),
    detailSystem: document.getElementById('detailSystem'),
    detailContent: document.getElementById('detailContent'),
    detailMeta: document.getElementById('detailMeta'),
    detailTags: document.getElementById('detailTags'),
    btnHeart: document.getElementById('btnHeart'),
    toast: document.getElementById('toast'),
    btnLogin: document.getElementById('btnLogin'),
    btnDemoFill: document.getElementById('btnDemoFill'),
    btnMenu: document.getElementById('btnMenu'),
    btnSearch: document.getElementById('btnSearch'),
    btnFav: document.getElementById('btnFav'),
    btnDownload: document.getElementById('btnDownload'),
    btnClear: document.getElementById('btnClear'),
    inputSearch: document.getElementById('inputSearch'),
    email: document.getElementById('email'),
    password: document.getElementById('password'),
    selHospital: document.getElementById('selHospital'),
    aboutText: document.getElementById('aboutText'),
    headerTitle: document.getElementById('headerTitle'),
    syncIndicator: document.getElementById('syncIndicator'),
    syncText: document.getElementById('syncText'),
    drawerSystems: document.getElementById('drawerSystems'),
    pills: Array.from(document.querySelectorAll('#screen-procedures .pill'))
};

// ----------- NAVIGATION & HISTORIQUE -----------

function navigateTo(id) {
    if (screenHistory[screenHistory.length - 1] !== id) {
        screenHistory.push(id);
        window.history.pushState({ screen: id }, '', id);
    }
    showScreen(id);
}

function showScreen(id) {
    els.screens.forEach(screen => {
        if (screen.id === id.replace('#', '')) {
            screen.classList.add('active');
        } else {
            screen.classList.remove('active');
        }
    });
    const isLogin = id === '#screen-login';
    [els.btnMenu, els.btnSearch, els.btnFav].forEach(btn => {
        if (btn) btn.style.visibility = isLogin ? 'hidden' : 'visible';
    });
    if (!isLogin) {
        els.drawer.classList.remove('open');
        els.overlay.classList.remove('show');
    }
}

// ----------- OUTILS UI -----------

function $(selector) {
    return document.querySelector(selector);
}

function toast(message) {
    if (!els.toast) return;
    els.toast.textContent = message;
    els.toast.style.display = 'block';
    setTimeout(() => {
        els.toast.style.display = 'none';
    }, 2200);
}

function updateSyncIndicator(online) {
    if (!els.syncIndicator) return;
    els.syncIndicator.classList.toggle('active', online);
    els.syncText.textContent = online ? 'Synchronisé' : 'Hors ligne';
}

// ----------- FONCTIONS DE DONNÉES -----------

function isFavorite(id) {
    return state.favorites.includes(id);
}

function toggleFavorite(id) {
    const index = state.favorites.indexOf(id);
    if (index >= 0) {
        state.favorites.splice(index, 1);
    } else {
        state.favorites.push(id);
    }
    localStorage.setItem(STORAGE_KEYS.favorites, JSON.stringify(state.favorites));
}

function sortByUpdate(list) {
    return [...list].sort((a, b) => new Date(b.updatedAt) - new Date(a.updatedAt));
}

function accentInsensitiveSort(a, b) {
    return a.title.localeCompare(b.title, 'fr', { sensitivity: 'base' });
}

function ensureDatasetCached(payload) {
    localStorage.setItem(STORAGE_KEYS.dataset, JSON.stringify(payload));
    state.datasetLoaded = true;
    updateSyncIndicator(true);
}

function getCachedDataset() {
    const raw = localStorage.getItem(STORAGE_KEYS.dataset);
    if (!raw) return null;
    try {
        return JSON.parse(raw);
    } catch (err) {
        console.error('Dataset cache parsing error', err);
        return null;
    }
}

function setDataset(payload) {
    state.procedures = payload.procedures || [];
    state.hospitals = payload.hospitals || [];
    state.meta = payload.meta || null;
    ensureDatasetCached(payload);
    fillHospitalSelect();
    fillDrawerSystems();
    els.aboutText.textContent = payload.meta?.about || 'Prototype interne — contenu fictif.';
}

// ----------- RENDUS UI -----------

function fillHospitalSelect() {
    if (!els.selHospital) return;
    els.selHospital.innerHTML = '';
    state.hospitals.forEach(hospital => {
        const opt = document.createElement('option');
        opt.value = hospital.id;
        opt.textContent = `${hospital.id} — ${hospital.label}`;
        els.selHospital.appendChild(opt);
    });
    els.selHospital.value = state.hospital;
}

function fillDrawerSystems() {
    if (!els.drawerSystems) return;
    els.drawerSystems.innerHTML = '';
    const systems = [...new Set(state.procedures.map(proc => proc.system))].sort((a, b) => a.localeCompare(b, 'fr', { sensitivity: 'base' }));
    systems.forEach(system => {
        const link = document.createElement('a');
        link.href = '#';
        link.dataset.filterSystem = system;
        link.textContent = system;
        link.addEventListener('click', evt => {
            evt.preventDefault();
            state.currentFilter = { type: null, system };
            renderProcedures();
            navigateTo('#screen-procedures');
        });
        els.drawerSystems.appendChild(link);
    });
}

function renderHome() {
    els.feed.innerHTML = '';
    const latest = sortByUpdate(state.procedures).slice(0, 5);
    if (!latest.length) {
        els.feed.innerHTML = '<div class="empty">Aucune donnée disponible hors ligne.</div>';
        return;
    }
    latest.forEach(proc => {
        const card = document.createElement('div');
        card.className = 'card feed-item';
        card.innerHTML = `
      <div class="avatar">${proc.type === 'smur' ? '🚑' : '📄'}</div>
      <div style="flex:1">
        <div class="row" style="justify-content:space-between">
          <div style="font-weight:700">${proc.title}</div>
          <div class="badge">MAJ</div>
        </div>
        <div class="subtitle">${proc.system} • ${proc.category}</div>
        <div class="muted">Actualisé le ${formatDate(proc.updatedAt)}</div>
      </div>`;
        card.addEventListener('click', () => openDetail(proc.id));
        els.feed.appendChild(card);
    });
}

function renderProcedures() {
    const { type, system } = state.currentFilter;
    els.procList.innerHTML = '';
    els.pills.forEach(pill => {
        const match = (!type && pill.id === 'pillAll') || (type && pill.dataset.type === type);
        pill.classList.toggle('active', match);
        pill.setAttribute('aria-selected', match ? 'true' : 'false');
    });
    let list = state.procedures;
    if (type) list = list.filter(proc => proc.type === type);
    if (system) list = list.filter(proc => proc.system === system);
    if (!list.length) {
        els.procList.innerHTML = '<div class="empty">Aucun résultat</div>';
        return;
    }
    list.slice().sort(accentInsensitiveSort).forEach(proc => {
        const item = document.createElement('div');
        item.className = 'item';
        item.innerHTML = `
        <div>
          <div style="font-weight:700">${proc.title}</div>
          <div class="subtitle">${proc.type === 'smur' ? 'SMUR' : 'Général'} • ${proc.system}</div>
        </div>
        <div aria-hidden="true">›</div>`;
        item.addEventListener('click', () => openDetail(proc.id));
        els.procList.appendChild(item);
    });
}

function renderSmur() {
    els.smurGrid.innerHTML = '';
    const smur = state.procedures.filter(proc => proc.type === 'smur');
    if (!smur.length) {
        els.smurGrid.innerHTML = '<div class="empty">Pas de contenu SMUR disponible.</div>';
        return;
    }
    smur.slice().sort(accentInsensitiveSort).forEach(proc => {
        const card = document.createElement('div');
        card.className = 'card';
        card.innerHTML = `
        <div class="kicker">SMUR</div>
        <div style="font-weight:700">${proc.title}</div>
        <div class="muted" style="margin-top:6px">${proc.system}</div>`;
        card.addEventListener('click', () => openDetail(proc.id));
        els.smurGrid.appendChild(card);
    });
}

function renderFavorites() {
    els.favList.innerHTML = '';
    const favorites = state.procedures.filter(proc => isFavorite(proc.id));
    if (!favorites.length) {
        els.favList.innerHTML = '<div class="empty">Aucun favori pour le moment.</div>';
        return;
    }
    favorites.forEach(proc => {
        const item = document.createElement('div');
        item.className = 'item';
        item.innerHTML = `
      <div>
        <div style="font-weight:700">${proc.title}</div>
        <div class="subtitle">${proc.type === 'smur' ? 'SMUR' : 'Général'} • ${proc.system}</div>
      </div>
      <div aria-hidden="true">★</div>`;
        item.addEventListener('click', () => openDetail(proc.id));
        els.favList.appendChild(item);
    });
}

function formatDate(value) {
    try {
        return new Date(value).toLocaleDateString('fr-FR', { year: 'numeric', month: 'short', day: '2-digit' });
    } catch {
        return value;
    }
}

// ----------- DÉTAIL -----------

function mergeVariant(proc) {
    const base = proc.body || '';
    const variant = proc.variants?.[state.hospital];
    if (!variant || (!variant.note && !variant.blocks?.length)) {
        return `${base}<div class="card" style="margin-top:10px">
            <div class="kicker">Variante hospitalière</div>
            <div class="muted">Aucune spécificité pour ${state.hospital}. Contenu générique.</div>
        </div>`;
    }
    let html = `${base}<div class="card" style="margin-top:10px"><div class="kicker">Variante hospitalière</div>`;
    if (variant.note) html += `<div class="muted">${variant.note}</div>`;
    variant.blocks?.forEach(block => html += `<div style="margin-top:6px">${block.html}</div>`);
    html += '</div>';
    return html;
}

function openDetail(id) {
    const proc = state.procedures.find(item => item.id === id);
    if (!proc) return toast('Contenu non disponible hors ligne');
    els.detailTitle.textContent = proc.title;
    els.detailType.textContent = proc.type === 'smur' ? 'SMUR' : 'Procédure générale';
    els.detailSystem.textContent = `${proc.system} • ${proc.category}`;
    els.detailContent.innerHTML = mergeVariant(proc);
    els.detailMeta.innerHTML = `
        <div>Créé le ${formatDate(proc.createdAt)} • Dernière mise à jour ${formatDate(proc.updatedAt)} • Version ${proc.version || 'prototype'}</div>
        <div class="muted">Prototype de conception — ne pas utiliser pour la pratique clinique réelle.</div>`;
    els.detailTags.innerHTML = '';
    proc.tags?.forEach(tag => {
        const pill = document.createElement('span');
        pill.className = 'pill';
        pill.textContent = tag;
        els.detailTags.appendChild(pill);
    });
    els.btnHeart.textContent = isFavorite(proc.id) ? '♥' : '♡';
    els.btnHeart.onclick = () => {
        toggleFavorite(proc.id);
        els.btnHeart.textContent = isFavorite(proc.id) ? '♥' : '♡';
        toast(isFavorite(proc.id) ? 'Ajouté aux favoris' : 'Retiré des favoris');
    };
    navigateTo('#screen-detail');
}

// ----------- FETCH & BOOTSTRAP -----------

async function fetchBootstrap() {
    const response = await fetch(API_ENDPOINT, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ node: 'clinical-procedure', action: 'bootstrap' })
    });
    if (!response.ok) throw new Error(`API error ${response.status}`);
    return response.json();
}

async function bootstrapDataset() {
    try {
        const data = await fetchBootstrap();
        setDataset(data);
        notifyServiceWorker({ type: 'PREFETCH_DATA' });
        toast('Contenu synchronisé');
    } catch (err) {
        console.warn('Bootstrap failed', err);
        const cached = getCachedDataset();
        if (cached) {
            setDataset(cached);
            updateSyncIndicator(false);
            toast('Mode hors ligne (cache)');
        } else throw err;
    }
}

// ----------- RECHERCHE -----------

function runSearch(query) {
    if (!query) {
        state.currentFilter = { type: null, system: null };
        renderProcedures();
        navigateTo('#screen-procedures');
        return;
    }
    const normalized = query.toLowerCase();
    const results = state.procedures.filter(proc => {
        const haystack = [proc.title, proc.summary, proc.tags?.join(' ')].join(' ').toLowerCase();
        return haystack.includes(normalized);
    });
    els.procList.innerHTML = results.length
        ? ''
        : '<div class="empty">Aucun résultat</div>';
    results.slice().sort(accentInsensitiveSort).forEach(proc => {
        const item = document.createElement('div');
        item.className = 'item';
        item.innerHTML = `
          <div>
            <div style="font-weight:700">${proc.title}</div>
            <div class="subtitle">${proc.type === 'smur' ? 'SMUR' : 'Général'} • ${proc.system}</div>
          </div>
          <div aria-hidden="true">›</div>`;
        item.addEventListener('click', () => openDetail(proc.id));
        els.procList.appendChild(item);
    });
    navigateTo('#screen-procedures');
}

// ----------- ÉVÉNEMENTS -----------

function attachEventHandlers() {
    els.btnMenu?.addEventListener('click', () => {
        els.drawer.classList.add('open');
        els.overlay.classList.add('show');
    });
    els.overlay?.addEventListener('click', () => {
        els.drawer.classList.remove('open');
        els.overlay.classList.remove('show');
    });
    els.btnFav?.addEventListener('click', () => {
        renderFavorites();
        navigateTo('#screen-favorites');
    });
    els.btnSearch?.addEventListener('click', () => {
        navigateTo('#screen-home');
        setTimeout(() => els.inputSearch?.focus(), 150);
    });

    document.querySelectorAll('[data-nav]').forEach(link => {
        link.addEventListener('click', evt => {
            evt.preventDefault();
            const nav = link.dataset.nav;
            switch (nav) {
                case 'home':
                    renderHome();
                    navigateTo('#screen-home');
                    break;
                case 'procedures':
                    state.currentFilter = { type: null, system: null };
                    renderProcedures();
                    navigateTo('#screen-procedures');
                    break;
                case 'smur':
                    renderSmur();
                    navigateTo('#screen-smur');
                    break;
                case 'favorites':
                    renderFavorites();
                    navigateTo('#screen-favorites');
                    break;
                case 'settings':
                    fillHospitalSelect();
                    navigateTo('#screen-settings');
                    break;
            }
        });
    });

    els.pills.forEach(pill => {
        pill.addEventListener('click', () => {
            state.currentFilter = pill.id === 'pillAll'
                ? { type: null, system: null }
                : { type: pill.dataset.type, system: null };
            renderProcedures();
        });
    });

    els.inputSearch?.addEventListener('keydown', evt => {
        if (evt.key === 'Enter') runSearch(evt.target.value);
    });

    els.selHospital?.addEventListener('change', evt => {
        state.hospital = evt.target.value;
        localStorage.setItem(STORAGE_KEYS.hospital, state.hospital);
        toast(`Contexte ${state.hospital} sélectionné`);
        if (els.detailContent.innerHTML.trim()) {
            const currentTitle = els.detailTitle.textContent;
            const current = state.procedures.find(proc => proc.title === currentTitle);
            if (current) els.detailContent.innerHTML = mergeVariant(current);
        }
    });

    els.btnDownload?.addEventListener('click', () => {
        if (state.datasetLoaded) toast('Contenu déjà en cache');
        else bootstrapDataset().catch(() => toast('Impossible de synchroniser hors ligne'));
        notifyServiceWorker({ type: 'PREFETCH_DATA' });
    });

    els.btnClear?.addEventListener('click', () => {
        localStorage.removeItem(STORAGE_KEYS.dataset);
        localStorage.removeItem(STORAGE_KEYS.favorites);
        state.favorites = [];
        state.datasetLoaded = false;
        state.procedures = [];
        toast('Cache vidé');
        notifyServiceWorker({ type: 'CLEAR_CACHES' });
    });

    els.btnDemoFill?.addEventListener('click', () => {
        els.email.value = 'demo@hopital.org';
        els.password.value = 'password';
    });

    els.btnLogin?.addEventListener('click', async () => {
        if (!els.email.value || !els.password.value) {
            toast('Renseignez vos identifiants');
            return;
        }
        els.btnLogin.disabled = true;
        els.btnLogin.textContent = 'Connexion…';
        try {
            await bootstrapDataset();
            state.loggedIn = true;
            renderHome();
            navigateTo('#screen-home');
            toast('Bienvenue');
        } catch {
            toast('Connexion impossible');
        } finally {
            els.btnLogin.disabled = false;
            els.btnLogin.textContent = 'Se connecter';
        }
    });
}

// ----------- INITIALISATION -----------

function initFromCache() {
    const cached = getCachedDataset();
    if (cached) {
        setDataset(cached);
        updateSyncIndicator(navigator.onLine);
    } else {
        updateSyncIndicator(navigator.onLine);
    }
}

function handleConnectivityChange() {
    updateSyncIndicator(navigator.onLine);
}

function notifyServiceWorker(message) {
    if (!navigator.serviceWorker?.controller) return;
    navigator.serviceWorker.controller.postMessage(message);
}

async function registerServiceWorker() {
    if ('serviceWorker' in navigator) {
        try {
            const registration = await navigator.serviceWorker.register('./sw.js');
            if (registration.waiting) registration.waiting.postMessage({ type: 'SKIP_WAITING' });
        } catch (err) {
            console.warn('Service worker registration failed', err);
        }
    }
}

function init() {
    initFromCache();
    attachEventHandlers();
    registerServiceWorker();
    window.addEventListener('online', handleConnectivityChange);
    window.addEventListener('offline', handleConnectivityChange);
    navigateTo('#screen-login');

    window.addEventListener('popstate', evt => {
        const screen = evt.state?.screen;
        if (screen) showScreen(screen);
        else showScreen('#screen-home');
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
} else {
    init();
}
