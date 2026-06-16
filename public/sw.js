const CACHE_NAME = 'laporan-cache-v1';

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll([
                '/',
                '/manifest.json',
                '/favicon.png'
            ]);
        })
    );
});

self.addEventListener('fetch', (event) => {
    // Basic network-first strategy to pass PWA requirements
    // without aggressively caching Laravel dynamic routes.
    event.respondWith(
        fetch(event.request).catch(() => {
            return caches.match(event.request);
        })
    );
});
