const cacheName = 'olz-cache-v1';
// TODO: Add static shell files
const appShellFiles: string[] = [];
const contentToCache = [
    ...appShellFiles,
];

// @ts-ignore
self.addEventListener('install', (event: ExtendableEvent) => {
    console.log('[Service Worker] Install');
    event.waitUntil((async () => {
        const cache = await caches.open(cacheName);
        console.log('[Service Worker] Caching all: app shell and content');
        await cache.addAll(contentToCache);
    })());
});

// @ts-ignore
self.addEventListener('activate', (_event: ExtendableEvent) => {
    console.log('[Service Worker] Activate');
});

// @ts-ignore
self.addEventListener('fetch', (event: FetchEvent) => {
    event.respondWith((async () => {
        console.debug(`[Service Worker] Reading cache for: ${event.request.url}`);
        const cachedResponse = await caches.match(event.request);
        if (cachedResponse) {
            return cachedResponse;
        }
        console.debug(`[Service Worker] Fetching resource: ${event.request.url}`);
        const response = await fetch(event.request);
        // if (/\/_\/jsbuild\/|\/_\/icns\//.exec(event.request.url)) {
        //     const cache = await caches.open(cacheName);
        //     console.log(`[Service Worker] Caching new resource: ${event.request.url}`);
        //     cache.put(event.request, response.clone());
        // }
        return response;
    })());
});
