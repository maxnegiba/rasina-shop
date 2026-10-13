# MTD ART production Nginx snippets

The repository contains two snippets intended to be included inside the existing HTTPS `server { ... }` block for `mtdart.ro`:

- `mtdart-performance.conf.example` — compression and static-asset cache policy.
- `mtdart-security.conf.example` — TLS/session hardening, security headers for static responses, request-size limits, and sensitive-file denial.

Neither file becomes active simply by deploying the repository.

## Install

```bash
sudo cp deploy/nginx/mtdart-performance.conf.example /etc/nginx/snippets/mtdart-performance.conf
sudo cp deploy/nginx/mtdart-security.conf.example /etc/nginx/snippets/mtdart-security.conf
```

Inside the existing `server { ... }` block that serves `mtdart.ro` over HTTPS:

```nginx
include /etc/nginx/snippets/mtdart-performance.conf;
include /etc/nginx/snippets/mtdart-security.conf;
```

If Plesk or another hosting layer already declares `ssl_protocols`, `ssl_session_cache`, `ssl_session_timeout`, or `ssl_session_tickets`, keep the stricter effective values in one place rather than defining conflicting duplicates.

Laravel deliberately remains responsible for `Content-Security-Policy`: the public site/Stripe checkout and the Filament admin panel use different script requirements, so a single static Nginx CSP would either weaken the public site or break admin functionality.

## Validate before reload

```bash
sudo nginx -t
sudo systemctl reload nginx
```

Do **not** reload Nginx if `nginx -t` fails.

## Production environment

Use `.env.production.example` as a checklist, never as a place to store real credentials. Generate the application key on the server and configure real database, mail and Stripe credentials.

The application now fails closed during production boot when critical security invariants are missing, including HTTPS `APP_URL`, disabled debug mode, encrypted/secure/HttpOnly sessions, an acceptable SameSite mode, a real mail transport for admin MFA, and required Stripe credentials.

After changing production environment values:

```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

If configuration is unsafe, `config:cache`/application boot will fail with an `Unsafe production configuration` exception rather than starting insecurely.

## Verify headers and cache policy

```bash
curl -I https://mtdart.ro/
curl -I https://mtdart.ro/build/assets/<current-app-css-file>.css
curl -I https://mtdart.ro/build/assets/<current-app-js-file>.js
```

Expected security headers include HSTS on HTTPS, `X-Content-Type-Options: nosniff`, `X-Frame-Options: SAMEORIGIN`, `Referrer-Policy`, and `Permissions-Policy`. Dynamic Laravel pages additionally receive the route-aware CSP from `SecurityHeaders` middleware.

Fingerprint Vite assets should return approximately:

```text
Cache-Control: public, max-age=31536000, immutable
```

The `/img/` and `/storage/` paths intentionally retain a shorter 30-day cache because those files may be replaced without a fingerprinted filename.

## Canonical WWW migration (SEO-01–03)

See `mtdart-canonical-redirect.conf.example` for a **manual** apex-only
redirect example. This is not a drop-in snippet for the canonical www block.

1. Inventory the active Nginx/Plesk/CDN rules and certs on apex + www.
2. Update all webhook endpoints, signed-link producers and external integrations
   to the intended host; test Stripe and signed proforma URLs before redirecting.
3. Change the production `APP_URL` to `https://www.mtdart.ro`, clear/config-cache
   Laravel, and ensure forwarded HTTPS headers are trusted as appropriate.
4. Apply apex redirects only after `nginx -t` passes and the configuration
   has been reviewed. Avoid double redirects from upstream CDN rules.
5. Confirm HTTP 200 self-canonical on www; apex GET/HEAD 301; POST methods
   are not coerced to GET; no redirect loops. Check `/sitemap.xml` and
   `/robots.txt` on www, then submit sitemap in Search Console.
6. Rollback: restore prior Nginx/CDN config and prior production APP_URL,
   clear/cache Laravel config and retest checkout + webhooks.

The application code in this branch does **not** enable any edge redirects.
