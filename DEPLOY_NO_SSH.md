# Deploy Without SSH / cPanel Terminal

This project can be deployed on shared hosting without running server commands.

## 1) Upload
- Upload the full project to your subdomain folder (for example `harun.intelsofts.com`).
- Keep the folder structure unchanged (`app`, `bootstrap`, `config`, `public`, `vendor`, ...).

## 2) `.env` required values
Set at least:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=http://harun.intelsofts.com

PUBLIC_DISK_USE_PUBLIC_PATH=true
AUTO_WEB_SETUP_ENABLED=true
WEB_SETUP_ENABLED=true
WEB_SETUP_TOKEN=put-a-long-random-secret-token-here
```

Also set your production DB and mail/payment credentials.

## 3) APP_KEY (no artisan)
Generate once on your local machine and paste into production `.env`:

```env
APP_KEY=base64:YOUR_GENERATED_KEY
```

## 4) Auto setup after upload
With `AUTO_WEB_SETUP_ENABLED=true`, first web hit will automatically run:
- `key:generate` (if APP_KEY empty)
- `migrate --force`
- storage setup handling
- cache clear/optimize

Just open the site once:

```text
http://harun.intelsofts.com
```

Optional manual fallback:

```text
http://harun.intelsofts.com/__setup/YOUR_WEB_SETUP_TOKEN
```

## 5) Security toggle after first successful load
After setup completes, set these:

```text
AUTO_WEB_SETUP_ENABLED=false
WEB_SETUP_ENABLED=false
```

## 6) Notes
- Root `.htaccess` is included so app works even when document root is project root.
- `PUBLIC_DISK_USE_PUBLIC_PATH=true` stores uploads directly in `public/storage`, so `storage:link` is not required.
- `vendor` and `public/build` must exist on server (upload from local if server cannot run composer/npm).
