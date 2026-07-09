const CACHE_NAME = 'futbol-app-v1';
const urlsToCache = [
  '/',
  '/dashboard',
  '/offline',
  '/css/app.css',
  '/js/app.js'
];

self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => cache.addAll(urlsToCache))
  );
});

self.addEventListener('fetch', event => {
  event.respondWith(
    caches.match(event.request).then(response => {
      return response || fetch(event.request).catch(() => caches.match('/offline'));
    })
  );
});