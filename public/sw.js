const CACHE_VERSION = "gymsystem-v2";
const STATIC_CACHE = `${CACHE_VERSION}-static`;

const STATIC_ASSETS = [
  "/",
  "/app",
  "/offline.html",
  "/manifest.webmanifest",
  "/favicon.ico",
  "/pwa/icon.svg",
  "/pwa/icon-maskable.svg",
];

self.addEventListener("install", (event) => {
  event.waitUntil(
    caches
      .open(STATIC_CACHE)
      .then((cache) => cache.addAll(STATIC_ASSETS))
      .then(() => self.skipWaiting()),
  );
});

self.addEventListener("activate", (event) => {
  event.waitUntil(
    caches
      .keys()
      .then((keys) =>
        Promise.all(
          keys
            .filter((key) => key.startsWith("gymsystem-") && key !== STATIC_CACHE)
            .map((key) => caches.delete(key)),
        ),
      )
      .then(() => self.clients.claim()),
  );
});

function isStaticAsset(requestUrl) {
  const sameOrigin = requestUrl.origin === self.location.origin;
  if (!sameOrigin) return false;

  return (
    requestUrl.pathname.startsWith("/build/") ||
    requestUrl.pathname.startsWith("/storage/") ||
    /\.(?:js|css|ico|png|jpg|jpeg|svg|webp|woff2|woff|ttf)$/i.test(requestUrl.pathname)
  );
}

self.addEventListener("fetch", (event) => {
  if (event.request.method !== "GET") return;

  const requestUrl = new URL(event.request.url);

  if (event.request.mode === "navigate") {
    event.respondWith(
      fetch(event.request)
        .then((response) => {
          const copy = response.clone();
          caches.open(STATIC_CACHE).then((cache) => cache.put(event.request, copy));
          return response;
        })
        .catch(async () => {
          const cached = await caches.match(event.request);
          if (cached) return cached;
          return caches.match("/offline.html");
        }),
    );
    return;
  }

  if (isStaticAsset(requestUrl)) {
    event.respondWith(
      caches.match(event.request).then((cachedResponse) => {
        const networkFetch = fetch(event.request)
          .then((networkResponse) => {
            const copy = networkResponse.clone();
            caches.open(STATIC_CACHE).then((cache) => cache.put(event.request, copy));
            return networkResponse;
          })
          .catch(() => cachedResponse);

        return cachedResponse || networkFetch;
      }),
    );
  }
});

function normalizePushPayload(payload) {
  const safePayload = payload && typeof payload === "object" ? payload : {};
  const data = safePayload.data && typeof safePayload.data === "object" ? safePayload.data : {};
  const fallbackUrl = typeof safePayload.url === "string" && safePayload.url.trim() !== ""
    ? safePayload.url
    : "/app";

  return {
    title: typeof safePayload.title === "string" && safePayload.title.trim() !== ""
      ? safePayload.title
      : "GymSystem",
    body: typeof safePayload.body === "string" && safePayload.body.trim() !== ""
      ? safePayload.body
      : "Tienes una notificacion nueva.",
    icon: typeof safePayload.icon === "string" && safePayload.icon.trim() !== ""
      ? safePayload.icon
      : "/pwa/icon-maskable.png",
    badge: typeof safePayload.badge === "string" && safePayload.badge.trim() !== ""
      ? safePayload.badge
      : "/pwa/icon-maskable.png",
    tag: typeof safePayload.tag === "string" && safePayload.tag.trim() !== ""
      ? safePayload.tag
      : "gymsystem-notification",
    renotify: Boolean(safePayload.renotify),
    requireInteraction: Boolean(safePayload.requireInteraction),
    data: {
      ...data,
      url: typeof data.url === "string" && data.url.trim() !== "" ? data.url : fallbackUrl,
    },
  };
}

function broadcastPushPayload(payload) {
  return clients
    .matchAll({
      type: "window",
      includeUncontrolled: true,
    })
    .then((windowClients) => {
      windowClients.forEach((client) => {
        try {
          client.postMessage({
            type: "GYMSYSTEM_PUSH_EVENT",
            payload,
          });
        } catch (_error) {
          // Keep silent, system notification is still shown.
        }
      });
    })
    .catch(() => {
      // Keep silent.
    });
}

self.addEventListener("push", (event) => {
  const pushData = event.data
    ? (() => {
        try {
          return event.data.json();
        } catch (_error) {
          return {
            body: event.data.text(),
          };
        }
      })()
    : {};

  const payload = normalizePushPayload(pushData);
  event.waitUntil((async () => {
    await broadcastPushPayload(payload);
    try {
      await self.registration.showNotification(payload.title, {
        body: payload.body,
        icon: payload.icon,
        badge: payload.badge,
        tag: payload.tag,
        renotify: payload.renotify,
        requireInteraction: payload.requireInteraction,
        data: payload.data,
      });
    } catch (_error) {
      // Keep silent. Foreground toast already has the payload.
    }
  })());
});

self.addEventListener("notificationclick", (event) => {
  event.notification.close();
  const targetUrl = (event.notification && event.notification.data && event.notification.data.url)
    ? String(event.notification.data.url)
    : "/app";
  const targetUrlWithoutHash = targetUrl.split("#")[0];

  event.waitUntil(
    clients.matchAll({
      type: "window",
      includeUncontrolled: true,
    }).then((windowClients) => {
      for (const client of windowClients) {
        const clientUrl = String(client.url || "").split("#")[0];
        if ("focus" in client && (clientUrl === targetUrlWithoutHash || clientUrl.startsWith(targetUrlWithoutHash))) {
          return client.focus();
        }
      }
      if (clients.openWindow) {
        return clients.openWindow(targetUrl);
      }

      return null;
    }),
  );
});
