const CACHE_VERSION = "gymsystem-v9";
const STATIC_CACHE = `${CACHE_VERSION}-static`;
const RUNTIME_CACHE_PREFIX = `${CACHE_VERSION}-runtime`;

const STATIC_ASSETS = [
  "/",
  "/app",
  "/offline.html",
  "/manifest.webmanifest",
  "/favicon.ico",
  "/pwa/favicon-brand-192.png",
  "/pwa/favicon-brand.png",
  "/pwa/icon.svg",
  "/pwa/icon-maskable.png",
];

const RESERVED_CONTEXT_SEGMENTS = new Set([
  "",
  "app",
  "public",
  "login",
  "logout",
  "demo",
  "superadmin",
  "notifications",
  "subscription",
  "client-qr",
  "nosotros",
  "contactanos",
  "politica-de-privacidad",
  "condiciones-de-servicio",
  "terminos-comerciales",
]);

const CONTEXT_DATA_PATTERNS = [
  /^\/[A-Za-z0-9\-]+\/reception\/sync\/latest$/,
  /^\/notifications\/push\/status$/,
];

const AUTH_NAVIGATION_PATTERN = /^\/[A-Za-z0-9\-]+\/(?:panel|clients|reception|cash|plans|reports|branches|staff|profile|contact|config)(?:\/|$)/;

self.addEventListener("install", (event) => {
  event.waitUntil(
    caches
      .open(STATIC_CACHE)
      .then((cache) => cache.addAll(STATIC_ASSETS))
      .catch(() => {
        // Keep silent.
      }),
  );
});

self.addEventListener("activate", (event) => {
  event.waitUntil(
    caches
      .keys()
      .then((keys) =>
        Promise.all(
          keys
            .filter((key) => key.startsWith("gymsystem-") && !key.startsWith(CACHE_VERSION))
            .map((key) => caches.delete(key)),
        ),
      )
      .then(() => self.clients.claim()),
  );
});

self.addEventListener("message", (event) => {
  const message = event && event.data ? event.data : null;
  if (!message || typeof message !== "object") return;

  if (message.type === "SKIP_WAITING") {
    self.skipWaiting();
  }
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

function resolveContextKey(requestUrl) {
  if (requestUrl.origin !== self.location.origin) {
    return "external";
  }

  const segments = requestUrl.pathname.split("/").filter(Boolean);
  if (segments.length === 0) {
    return "public";
  }

  const topSegment = String(segments[0] || "").toLowerCase();
  if (RESERVED_CONTEXT_SEGMENTS.has(topSegment)) {
    return "public";
  }

  if (/^[a-z0-9\-]+$/i.test(topSegment)) {
    return topSegment;
  }

  return "public";
}

function runtimeCacheNameFor(requestUrl) {
  return `${RUNTIME_CACHE_PREFIX}-${resolveContextKey(requestUrl)}`;
}

function isContextDataRequest(requestUrl) {
  if (requestUrl.origin !== self.location.origin) return false;
  return CONTEXT_DATA_PATTERNS.some((pattern) => pattern.test(requestUrl.pathname));
}

function isCacheableNavigation(requestUrl) {
  if (requestUrl.origin !== self.location.origin) return false;
  return !AUTH_NAVIGATION_PATTERN.test(requestUrl.pathname);
}

async function cacheNetworkResponse(cacheName, request, response) {
  if (!response || !response.ok || response.type !== "basic") {
    return response;
  }

  const cache = await caches.open(cacheName);
  await cache.put(request, response.clone());

  return response;
}

self.addEventListener("fetch", (event) => {
  if (event.request.method !== "GET") return;

  const requestUrl = new URL(event.request.url);

  if (event.request.mode === "navigate") {
    event.respondWith(
      fetch(event.request)
        .then(async (response) => {
          if (isCacheableNavigation(requestUrl)) {
            await cacheNetworkResponse(STATIC_CACHE, event.request, response);
          }
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

  if (isContextDataRequest(requestUrl)) {
    const contextCache = runtimeCacheNameFor(requestUrl);
    event.respondWith(
      fetch(event.request)
        .then((networkResponse) => cacheNetworkResponse(contextCache, event.request, networkResponse))
        .catch(async () => {
          const cached = await caches.open(contextCache).then((cache) => cache.match(event.request));
          if (cached) return cached;
          return caches.match(event.request);
        }),
    );
    return;
  }

  if (isStaticAsset(requestUrl)) {
    event.respondWith(
      caches.match(event.request).then((cachedResponse) => {
        const networkFetch = fetch(event.request)
          .then((networkResponse) => cacheNetworkResponse(STATIC_CACHE, event.request, networkResponse))
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

