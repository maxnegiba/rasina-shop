# Homepage critical CSS deployment check

After this branch is merged and deployed, rebuild the Vite assets before clearing Laravel caches:

```bash
npm ci
npm run build
php artisan optimize:clear
php artisan optimize
```

The build must contain three CSS entries:

- the full storefront `app-*.css`
- the full homepage `home-*.css`
- the inline-only `home-critical-*.css`

The homepage HTML should contain the compiled critical CSS inline and preload the full homepage stylesheet instead of rendering it as a blocking stylesheet:

```bash
curl -s https://mtdart.ro | grep -E 'data-home-critical|rel="preload"|home-.*\.css'
```

Expected characteristics:

- `<style data-home-critical>...` is present.
- the `home-*.css` link uses `rel="preload"` with `as="style"`.
- there is no normal render-blocking homepage `<link rel="stylesheet" ...home-*.css>` outside the `<noscript>` fallback.

The existing Nginx immutable caching and gzip configuration for `/build/assets/` should remain enabled.
