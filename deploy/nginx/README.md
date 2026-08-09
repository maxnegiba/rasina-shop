# Apply the MTD ART performance snippet

The repository already contains `deploy/nginx/mtdart-performance.conf.example` with the cache/compression directives required by the PageSpeed static-asset audit.

The snippet does **not** become active simply by deploying the repository. It must be included inside the existing `server { ... }` block that serves `mtdart.ro`.

## Recommended deployment

1. Copy the snippet to the Nginx snippets directory:

```bash
sudo cp deploy/nginx/mtdart-performance.conf.example /etc/nginx/snippets/mtdart-performance.conf
```

2. In the existing `server { ... }` block for `mtdart.ro`, add:

```nginx
include /etc/nginx/snippets/mtdart-performance.conf;
```

3. Validate and reload:

```bash
sudo nginx -t
sudo systemctl reload nginx
```

4. Verify the fingerprinted Vite assets now return a long cache lifetime:

```bash
curl -I https://mtdart.ro/build/assets/<current-app-css-file>.css
curl -I https://mtdart.ro/build/assets/<current-app-js-file>.js
```

Expected response headers include approximately:

```text
Cache-Control: public, max-age=31536000, immutable
Expires: ...
```

The `/img/` and `/storage/` paths intentionally use a shorter 30-day cache because those files may be replaced without a fingerprinted filename.
