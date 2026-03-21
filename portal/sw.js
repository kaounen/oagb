self.addEventListener('install', (e) => {
  e.waitUntil(
    caches.open('oagb-v1').then((cache) => cache.addAll([
      '/oagb/portal/login.php',
      '/oagb/portal/index.php'
    ])),
  );
});

self.addEventListener('fetch', (e) => {
  e.respondWith(
    caches.match(e.request).then((response) => response || fetch(e.request)),
  );
});
