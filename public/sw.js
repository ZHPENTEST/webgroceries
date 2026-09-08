/* WebGroceries service worker: app shell + images cached, pages network-first with offline fallback */
const V = 'wg-v1';
const SHELL = ['/', '/offline.html', '/assets/css/app.css', '/icons/icon-192.png'];
self.addEventListener('install', e => {
  e.waitUntil(caches.open(V).then(c => c.addAll(SHELL)).then(() => self.skipWaiting()));
});
self.addEventListener('activate', e => {
  e.waitUntil(caches.keys().then(ks => Promise.all(ks.filter(k => k !== V).map(k => caches.delete(k)))).then(() => self.clients.claim()));
});
self.addEventListener('fetch', e => {
  const u = new URL(e.request.url);
  if (e.request.method !== 'GET' || u.origin !== location.origin) return;
  if (u.pathname.match(/\.(png|jpg|jpeg|webp|css|js)$/)) {
    e.respondWith(caches.match(e.request).then(hit => hit || fetch(e.request).then(r => {
      const cp = r.clone(); caches.open(V).then(c => c.put(e.request, cp)); return r;
    }).catch(() => caches.match('/offline.html'))));
    return;
  }
  e.respondWith(fetch(e.request).catch(() => caches.match('/offline.html')));
});
