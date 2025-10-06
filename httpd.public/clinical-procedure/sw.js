const PRECACHE = 'cp-shell-v1';
const RUNTIME = 'cp-runtime-v1';
const DATA_CACHE_KEY = './bootstrap-cache.json';
const PRECACHE_URLS = [
    './',
    './index.html',
    './styles.css',
    './app.js',
    './manifest.webmanifest',
    './icons/icon-192.png',
    './icons/icon-512.png'
];

self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(PRECACHE).then(cache => cache.addAll(PRECACHE_URLS)).then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keys =>
            Promise.all(
                keys.filter(key => ![PRECACHE, RUNTIME].includes(key)).map(key => caches.delete(key))
            )
        ).then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', event => {
    const request = event.request;
    if (request.method !== 'GET') {
        return;
    }
    const url = new URL(request.url);
    if (url.origin === location.origin) {
        event.respondWith(
            caches.match(request).then(cached =>
                    cached || fetch(request).then(response => {
                        const copy = response.clone();
                        caches.open(RUNTIME).then(cache => cache.put(request, copy));
                        return response;
                    }).catch(() => cached)
            )
        );
    }
});

self.addEventListener('message', event => {
    const { data } = event;
    if (!data || typeof data !== 'object') return;
    if (data.type === 'CLEAR_CACHES') {
        event.waitUntil(
            caches.keys().then(keys => Promise.all(keys.map(key => caches.delete(key))))
        );
    }
    if (data.type === 'PREFETCH_DATA') {
        event.waitUntil(
            fetch('../node.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ node: 'clinical-procedure', action: 'bootstrap' })
            })
                .then(response => {
                    if (!response.ok) {
                        return null;
                    }
                    return response.clone().json().then(payload =>
                        caches.open(RUNTIME).then(cache =>
                            cache.put(new Request(DATA_CACHE_KEY), new Response(JSON.stringify(payload), {
                                headers: { 'Content-Type': 'application/json' }
                            }))
                        )
                    );
                })
                .catch(() => null)
        );
    }
    if (data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});