# GymSystem Upgrade Checklist (Applied + Operational)

## 1) Commercial site vs operational app
- Landing page moved to `/` with commercial structure (hero, value blocks, CTA).
- Operational app entrypoint moved to `/app` and redirects by role/context.
- Legacy `/public` kept as redirect to `/`.
- Demo shortcut available at `/demo` (redirect to login).

## 2) UI consistency (panel + responsive)
- Sidebar/logo behavior normalized for open and collapsed states.
- Header mobile alignment improved (title, status badge, user menu).
- Menu toggle wording standardized to `menu`.
- Global modal behavior hardened:
  - viewport-safe max height
  - internal content scroll
  - sticky action footer support

## 3) PWA baseline
- Added `public/manifest.webmanifest`.
- Added `public/sw.js` service worker.
- Added offline fallback page `public/offline.html`.
- Registered SW in production from `resources/js/app.js`.
- Added manifest/meta tags in login and panel layouts.

## 4) Tenant isolation safeguards
- Existing scoped routes kept under `/{contextGym}`.
- Added feature test to ensure document duplicate check does not leak across gyms.
- Added landing behavior tests for guest, gym admin, and superadmin redirection.

## 5) Production hardening tasks (run on VM)
- Enable VM deletion protection.
- Enable automatic DB backups and restore test.
- Enable resource and log alerts:
  - CPU, memory, disk
  - Apache/PHP/Laravel error rates
- Keep deployment post-check:
  - `curl -I https://<domain>/login` must return `200` or `302`.

## 6) Next scale step (when concurrent gyms increase)
- Move MySQL from local VM to managed DB instance.
- Add Redis health alerting + worker supervision audit.
- Add read model/report cache for dashboard heavy cards.
