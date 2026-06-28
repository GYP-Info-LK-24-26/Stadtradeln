/*
 * Service Worker für GYP-Radeln (PWA)
 *
 * Strategie:
 *  - Statische Assets (CSS/JS/Icons): cache-first mit Hintergrund-Update
 *    (stale-while-revalidate).
 *  - Navigationen (HTML-Seiten): network-first. Da die Seiten
 *    benutzerspezifisch und dynamisch sind, werden sie NICHT gecacht.
 *    Ist das Netzwerk nicht erreichbar, wird die Offline-Seite gezeigt.
 *  - Nicht-GET-Anfragen (POST etc.) werden grundsätzlich nicht angefasst.
 *
 * Bei jeder Änderung an den Assets CACHE_VERSION erhöhen, damit alte
 * Caches verworfen werden.
 */
const CACHE_VERSION = 'v4';
const CACHE_NAME = `gyp-radeln-${CACHE_VERSION}`;

// App-Shell: wird bei der Installation vorab gecacht.
const PRECACHE_URLS = [
  '/offline.html',
  '/css/main.css',
  '/favicon.svg',
  '/icon.svg',
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then((cache) => cache.addAll(PRECACHE_URLS))
      .then(() => self.skipWaiting())
  );
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys()
      .then((keys) => Promise.all(
        keys.filter((key) => key !== CACHE_NAME)
          .map((key) => caches.delete(key))
      ))
      .then(() => self.clients.claim())
  );
});

self.addEventListener('fetch', (event) => {
  const { request } = event;

  const url = new URL(request.url);

  // Fremde Domains nicht abfangen.
  if (url.origin !== self.location.origin) {
    return;
  }

  // Navigationen (HTML-Seiten UND Formular-POSTs): network-first mit
  // Offline-Fallback. Muss vor der GET-Prüfung stehen, damit auch
  // fehlgeschlagene POST-Submits die Offline-Seite zeigen statt der
  // Browser-Fehlerseite.
  if (request.mode === 'navigate') {
    event.respondWith(
      fetch(request).catch(() => caches.match('/offline.html'))
    );
    return;
  }

  // Ab hier nur GET behandeln; übrige Nicht-GET-Anfragen (z. B. fetch/XHR
  // an APIs) direkt ans Netz durchreichen.
  if (request.method !== 'GET') {
    return;
  }

  // Statische Assets: stale-while-revalidate.
  event.respondWith(
    caches.open(CACHE_NAME).then((cache) =>
      cache.match(request).then((cached) => {
        const network = fetch(request)
          .then((response) => {
            if (response && response.status === 200) {
              cache.put(request, response.clone());
            }
            return response;
          })
          .catch(() => cached);
        return cached || network;
      })
    )
  );
});
