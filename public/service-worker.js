const CACHE = 'webwms-v3-shell-v1';
const SHELL = ['/manifest.webmanifest', '/assets/css/bootstrap.css', '/assets/css/app.css', '/assets/css/custom.css'];

self.addEventListener('install', (event) => {
  event.waitUntil(caches.open(CACHE).then((cache) => cache.addAll(SHELL)));
});

self.addEventListener('activate', (event) => {
  event.waitUntil(caches.keys().then((keys) => Promise.all(keys.filter((key) => key !== CACHE).map((key) => caches.delete(key)))));
});

self.addEventListener('fetch', (event) => {
  if (event.request.method !== 'GET' || new URL(event.request.url).origin !== self.location.origin) {
    return;
  }
  const url = new URL(event.request.url);
  if (!url.pathname.startsWith('/assets/') && url.pathname !== '/manifest.webmanifest') {
    return;
  }
  event.respondWith(caches.match(event.request).then((response) => response || fetch(event.request)));
});
