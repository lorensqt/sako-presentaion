// ML Sako Cooperative - Service Worker
const CACHE_NAME = 'mlsako-pwa-v1';
const STATIC_ASSETS = [
    '/',
    '/img/sako-logo-nobg.png',
    '/img/mlsako-logo.png',
    '/img/icons/icon-192x192.png',
    '/img/icons/icon-512x512.png',
    '/manifest.json'
];

// 1. Install Event - Precaches core app shell assets
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(STATIC_ASSETS).catch((err) => {
                console.warn('[SW] Pre-caching warning:', err);
            });
        }).then(() => self.skipWaiting())
    );
});

// 2. Activate Event - Clean up obsolete caches
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((name) => {
                    if (name !== CACHE_NAME) {
                        return caches.delete(name);
                    }
                })
            );
        }).then(() => self.clients.claim())
    );
});

// 3. Fetch Event - Network-first with cache fallback
self.addEventListener('fetch', (event) => {
    const request = event.request;

    // Only intercept GET requests, ignore POST/PUT/DELETE
    if (request.method !== 'GET') {
        return;
    }

    const url = new URL(request.url);

    // Static assets (images, fonts, scripts): Cache-First / Stale-While-Revalidate
    if (
        url.pathname.startsWith('/img/') ||
        url.pathname.startsWith('/build/') ||
        url.pathname.endsWith('.png') ||
        url.pathname.endsWith('.jpg') ||
        url.pathname.endsWith('.svg') ||
        url.pathname.endsWith('.woff2')
    ) {
        event.respondWith(
            caches.match(request).then((cachedResponse) => {
                if (cachedResponse) {
                    // Update cache in the background
                    fetch(request).then((networkResponse) => {
                        if (networkResponse && networkResponse.status === 200) {
                            caches.open(CACHE_NAME).then((cache) => cache.put(request, networkResponse));
                        }
                    }).catch(() => {});
                    return cachedResponse;
                }
                return fetch(request).then((networkResponse) => {
                    if (networkResponse && networkResponse.status === 200) {
                        const responseClone = networkResponse.clone();
                        caches.open(CACHE_NAME).then((cache) => cache.put(request, responseClone));
                    }
                    return networkResponse;
                });
            })
        );
        return;
    }

    // HTML & Dynamic Navigation: Network-First to preserve dynamic auth and database state
    event.respondWith(
        fetch(request)
            .then((networkResponse) => {
                // If successful response for HTML, optionally update shell
                if (networkResponse && networkResponse.status === 200 && request.mode === 'navigate') {
                    const responseClone = networkResponse.clone();
                    caches.open(CACHE_NAME).then((cache) => cache.put(request, responseClone));
                }
                return networkResponse;
            })
            .catch(() => {
                return caches.match(request).then((cachedResponse) => {
                    if (cachedResponse) {
                        return cachedResponse;
                    }
                    // Offline fallback
                    return caches.match('/');
                });
            })
    );
});
