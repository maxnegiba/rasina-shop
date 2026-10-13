# SEO-01–03 — production rollout for the current MTD ART Nginx configuration

**Verified from read-only server output, 2026-10-13. No server changes made.**

## Baseline

- `/etc/nginx/sites-enabled/mtdart.ro` currently has one TLS `server { server_name mtdart.ro www.mtdart.ro; }`, with root `/var/www/mtdart/public` and PHP 8.4 FPM.
- HTTP port 80 redirects to `https://$host$request_uri`, retaining the original host.
- HTTPS requests to both `mtdart.ro/` and `www.mtdart.ro/` return **200**, so apex is not redirected.
- `https://www.mtdart.ro/sitemap.xml` already responds **200** with XML; a successful HTTP request does **not** mean it was submitted in Google Search Console.
- TLS on www responds successfully using the current Certbot installation.
- `/etc/nginx/sites-enabled/mtdart-consent-test` is an authenticated preview instance, but its comments state it **shares production database**. It must NOT be used for destructive tests, migrations or normal test-suite execution.
- The `analytics.mtdart.ro` vhost is unrelated to canonical redirects and should remain unchanged.

## Hard blockers before activating redirects or merging

1. Determine production `APP_URL` without revealing secrets: `sudo grep -E '^(APP_ENV|APP_URL)=' /var/www/mtdart/.env`.
2. Check in Stripe Dashboard what exact URL is configured for `/webhook/stripe`. Update the sender to the *www* HTTPS URL **before** redirecting apex, with signature verification and actual Stripe test-event confirmation. Some webhook clients do not follow redirects.
3. The route `/proforma/{order:public_token}` uses Laravel `signed` middleware: existing host-bound apex links can fail after a redirect because the host is part of the signature. Establish an expiry/compatibility policy before enabling an apex-wide redirect; either allow a temporary apex exception which forwards the old signed GET directly to Laravel, or handle legacy links with carefully tested backward compatibility. Do not simply redirect existing signed URLs.
4. PHP/Laravel test suite has NOT been run on a fully installed copy of this branch. No rollout until green and checkout/session regression-tested.
5. Verify existing active `nginx -T` config, upstream proxy/CDN policies and cert renewals first. Do not replace the full Nginx vhost from this document.

## Target Nginx topology (implementation guide, not auto-enabled)

**Desired**: The existing full Laravel/TLS server is restricted to `server_name www.mtdart.ro;`. Add an **apex-only** TLS server and direct all normal GET/HEAD requests from apex to HTTPS www with a one-hop 301, retaining `$request_uri`. For non-GET use 308 only after the external integrations are migrated/validated (308 preserves the HTTP method/body but webhook clients may not follow it). Make an exception for legacy signed proforma URLs if their lifespan requires it.

**Important**: The existing www SSL block has `listen [::]:443 ssl ipv6only=on;`. Don't duplicate `ipv6only=on` in a new apex block. Keep Certbot's TLS paths and validate Nginx before reload.

After resolving the blockers, the apex-only redirect block (absent any required legacy signed-link exception) can be structured as:

```nginx
server {
    listen 443 ssl;
    listen [::]:443 ssl;
    server_name mtdart.ro;

    ssl_certificate /etc/letsencrypt/live/mtdart.ro/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/mtdart.ro/privkey.pem;
    include /etc/letsencrypt/options-ssl-nginx.conf;
    ssl_dhparam /etc/letsencrypt/ssl-dhparams.pem;

    if ($request_method ~ ^(GET|HEAD)$) {
        return 301 https://www.mtdart.ro$request_uri;
    }

    return 308 https://www.mtdart.ro$request_uri;
}
```

Adapt the existing port 80 block to target www directly, preserving method semantics, not `$host`. Move `server_name mtdart.ro` out of the canonical www application vhost; do not let two active `server` blocks claim the same apex host/port.

Do **not** activate the above generic apex server until the Stripe and signed-URL cases are explicitly addressed.

## Safe test plan WITHOUT an isolated staging server

A safe local or VPS worktree test must use a **different project directory**, a **fresh temporary SQLite database** (or `:memory:`), a new non-production `APP_KEY`, **no copied production `.env`**, and disabled outbound integrations. Disable network access to production PostgreSQL and external payment/email providers for that test process where practical.

The repository's `phpunit.xml` sets `APP_ENV=testing`, `DB_CONNECTION=sqlite`, `DB_DATABASE=:memory:`, `MAIL_MAILER=array` and array cache/session. Still verify the **effective runtime** configuration in the isolated directory before any migrations/tests; environment variables or a copied `.env` may override expectations. Never run tests in `/var/www/mtdart` or the preview path.

Tests to run on the isolated copy:
```bash
php artisan test --filter='SeoFoundationTest|SitemapTest|StripeWebhookIdempotencyTest|CustomRequestWorkflowTest|PurchaseTrackingTest'
php artisan test
```

Also exercise, in an isolated HTTP test context, homepage/product/category canonical, `/magazin?page=2&utm_source=test`, sitemap, and read-only GET responses from both hosts. No test payments or contact/checkout submissions on live.

## Release sequence (only after all green)

1. Record current git SHA; back up the Nginx site config and production `.env` **privately on the server**, with permissions retained. Verify a current DB backup exists; no migration is expected for SEO-01–03.
2. Merge PR only after review; update `APP_URL=https://www.mtdart.ro` in production; deploy application and cache configuration. Coordinate Nginx host switch in the same controlled release window to avoid cross-host cookie/CSRF issues.
3. Run `sudo nginx -t` and **reload only if it passes**.
4. Confirm `curl -sS -o /dev/null -w '%{http_code} %{redirect_url}\n' 'https://mtdart.ro/magazin?utm_source=test'` gives one redirect to the corresponding www URL. Verify HTTP 200 on final www page, one canonical tag, no redirect loops.
5. Confirm GET `/sitemap.xml` contains only HTTPS WWW URLs and valid XML; `robots.txt` advertises `https://www.mtdart.ro/sitemap.xml`.
6. Verify basic storefront browsing, consent handling, contact forms, guest cart, Stripe webhook and signed proforma links. Do not issue synthetic orders in production.
7. Submit the **deployed** sitemap using Google Search Console and inspect representative canonical pages. Monitor errors and indexing.

## Rollback

Restore the previous Nginx vhost and `APP_URL`, run `nginx -t` before reload, deploy the previous application commit, clear/rebuild Laravel config cache, retest cart, webhook and existing signed URLs. Never rollback only one of Laravel URL generation and Nginx routing without validating cross-host cookies/signatures.

**Current status**: this document is operational planning only; no web server configuration was modified.
