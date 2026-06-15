# Deploy Madinova (Laravel 12) to InfinityFree via GitHub Actions

This guide matches **this repository**:
- Laravel **12**
- PHP **8.2+**
- Frontend: **Vite**
- Deploy target: **InfinityFree** (FTP only, no SSH, no `artisan` on server)

---

## 1) InfinityFree folder layout

InfinityFree serves only from `htdocs/`. Laravel must be split:

```text
htdocs/                          ← FTP web root (deploy_temp/ uploaded here)
  index.php                      ← from public/index.php.deploy
  .htaccess                      ← from public/.htaccess.deploy
  build/                         ← Vite compiled assets
  assets/
  storage/                       ← public uploads (storage:link workaround)
  laravel_core/                  ← entire Laravel app (NOT web accessible)
    .htaccess                    ← deny all
    app/
    bootstrap/
    config/
    vendor/
    storage/
    .env
```

The workflow uploads a **flat** `deploy_temp/` bundle directly into your FTP web root (`FTP_SERVER_DIR` is usually `/`).

### How bootstrap works

`public/index.php.deploy` is copied to `htdocs/index.php` during CI and points to `laravel_core/`:

```php
$corePath = __DIR__.'/laravel_core';
require $corePath.'/vendor/autoload.php';
$app = require_once $corePath.'/bootstrap/app.php';
$app->usePublicPath(__DIR__);
```

`usePublicPath(__DIR__)` is required so Laravel resolves asset URLs, `public/storage`, and Vite `build/` correctly.

### Storage without `php artisan storage:link`

InfinityFree cannot run Artisan on the server. The workflow:

1. Creates `htdocs/storage/`
2. Copies `storage/app/public/*` into it on build
3. **Excludes** `htdocs/storage/**` from FTP sync so user uploads are not deleted on redeploy

New uploads go to `laravel_core/storage/app/public/` at runtime. For them to be web-visible, either:
- also write/read via `htdocs/storage` (custom disk path), or
- periodically sync uploads (not ideal)

**Recommended for this app:** keep report images under `storage/app/public/reports` and add this to `config/filesystems.php` if needed:

```php
'public' => [
    'driver' => 'local',
    'root' => storage_path('app/public'),
    'url' => rtrim(env('APP_URL', 'http://localhost'), '/').'/storage',
    ...
],
```

With `usePublicPath(htdocs)`, `asset('storage/...')` maps to `htdocs/storage/...` which is correct.

---

## 2) InfinityFree control panel setup

### A. Create site + PHP version

1. Log in to [InfinityFree](https://infinityfree.com)
2. Create hosting account / add site
3. **Control Panel → PHP Configuration** → select **PHP 8.2** (required for Laravel 12)

### B. Create MySQL database

1. **Control Panel → MySQL Databases → Create Database**
2. Note these values (example format):

| Field | Example | Where used |
|-------|---------|------------|
| MySQL Host | `sql123.infinityfree.com` | `DB_HOST` secret |
| Database name | `if0_41234567_madinova` | `DB_DATABASE` |
| Username | `if0_41234567` | `DB_USERNAME` |
| Password | (you choose) | `DB_PASSWORD` |
| Port | `3306` | `DB_PORT` |

> On the **server**, MySQL host is often `localhost`. In GitHub Actions migrations (from outside), use the **remote** hostname from the panel (`sqlXXX.infinityfree.com`). If remote connections are blocked on free tier, use the one-time `migrate-once.php` method below.

### C. FTP credentials

1. **Control Panel → FTP Details**
2. Note:

| Field | Example |
|-------|---------|
| FTP Host | `ftpupload.net` or `ftp.yoursite.infinityfreeapp.com` |
| FTP Username | `if0_41234567` |
| FTP Password | your FTP password |
| Port | `21` |

3. FTP root is usually `htdocs/`. Set `FTP_SERVER_DIR` to `/` if FileZilla opens directly inside `htdocs`, or `/htdocs/` if FTP opens one level above it.

---

## 3) Generate APP_KEY (one time)

On your local machine:

```bash
php artisan key:generate --show
```

Copy the output (`base64:...`) — you will store it as GitHub secret `APP_KEY`.  
**Never regenerate on every deploy** or sessions/cookies will break.

---

## 4) GitHub Secrets

Go to **GitHub repo → Settings → Secrets and variables → Actions → New repository secret**

| Secret | Required | Example / notes |
|--------|----------|-----------------|
| `FTP_SERVER` | Yes | `ftpupload.net` |
| `FTP_USERNAME` | Yes | `if0_41234567` |
| `FTP_PASSWORD` | Yes | FTP password from panel |
| `FTP_SERVER_DIR` | Yes | Must end with `/` — use `/` if FTP opens inside `htdocs`; `/htdocs/` if one level above |
| `FTP_PORT` | No | `21` (default) |
| `APP_NAME` | Yes | `Madinova` |
| `APP_KEY` | Yes | `base64:xxxx...` from `php artisan key:generate --show` |
| `APP_URL` | Yes | `https://yourname.infinityfreeapp.com` (no trailing slash) |
| `DB_HOST` | Yes | `sql123.infinityfree.com` |
| `DB_PORT` | Yes | `3306` |
| `DB_DATABASE` | Yes | `if0_41234567_madinova` |
| `DB_USERNAME` | Yes | `if0_41234567` |
| `DB_PASSWORD` | Yes | MySQL password |
| `MAIL_FROM_ADDRESS` | No | `noreply@yourdomain.com` |
| `MIGRATE_TOKEN` | For migrate script | long random string |

---

## 5) Database migrations (no SSH)

InfinityFree free tier usually **blocks remote MySQL** from GitHub Actions. Use one of these:

### Option A — One-time web migration (recommended)

1. After first deploy, upload `deploy/migrate-once.php` to `htdocs/migrate-once.php` via FTP
2. Edit the file locally and set a strong token (or use env — see file comments)
3. Visit:  
   `https://yourdomain.infinityfreeapp.com/migrate-once.php?token=YOUR_TOKEN`
4. **Delete `migrate-once.php` immediately** after success

### Option B — Migrate from local PC against remote DB

Only works if InfinityFree allows your IP for remote MySQL:

```bash
# Temporarily point local .env to InfinityFree DB_* values
php artisan migrate --force
```

### Option C — Migrate in GitHub Actions (if remote DB allowed)

Add this step **before** “Prepare InfinityFree folder layout” in `deploy.yml`:

```yaml
- name: Run migrations
  run: php artisan migrate --force
```

---

## 6) GitHub Actions workflow

File: `.github/workflows/deploy.yml` (already in this repo)

**Triggers:** push to `main`

**Pipeline:**
1. `composer install --no-dev`
2. `npm ci && npm run build`
3. Build `.env` from secrets
4. `config:cache`, `route:cache`, `view:cache`
5. Assemble `deploy_temp/htdocs` + `deploy_temp/laravel_core`
6. FTP upload via SamKirkland/FTP-Deploy-Action

### First deploy checklist

```bash
git add .
git commit -m "Configure InfinityFree deployment"
git push origin main
```

Then watch **Actions** tab in GitHub.

---

## 7) Verify deployment

1. Open `https://yourdomain.infinityfreeapp.com`
2. Check homepage loads (no 500 error)
3. Check `https://yourdomain.infinityfreeapp.com/build/manifest.json` exists (Vite build)
4. Test login / register
5. Submit a test report with image
6. Confirm image URL under `/storage/...`

### Quick debug if 500 error

Create `htdocs/health.php` temporarily:

```php
<?php phpinfo();
```

Remove after checking PHP version is 8.2+.

Enable logs: after a request, download `laravel_core/storage/logs/laravel.log` via FTP.

---

## 8) Troubleshooting: `Timeout (control socket)`

This error means GitHub Actions could not complete the FTP **control connection** (often before any files upload).

### Fix checklist (in order)

1. **`FTP_SERVER` must be the InfinityFree hostname** from Control Panel → FTP Details  
   - Good: `ftpupload.net` or `ftp.yoursite.infinityfreeapp.com`  
   - Bad: `localhost`, `127.0.0.1`, your website URL (`https://...`)

2. **`FTP_USERNAME`** is your hosting username (e.g. `if0_41234567`), not your email.

3. **`FTP_PASSWORD`** is the FTP password from the panel (reset it if unsure).

4. **`FTP_SERVER_DIR` must end with `/`**  
   - FileZilla opens inside `htdocs` → use `/`  
   - FileZilla opens above `htdocs` → use `/htdocs/`

5. **Test FTP locally with FileZilla** using the same host/user/pass. Use **Passive mode**.

6. **Re-run the workflow** — the updated pipeline includes:
   - `timeout: 300000` (web root) / `600000` (laravel_core)
   - FTP connection test step before upload
   - Split upload (web files first, then `laravel_core/`)

7. **InfinityFree + cloud CI limitation** — free hosting sometimes blocks or throttles FTP from datacenter IPs (GitHub runners). If FileZilla works from your PC but GitHub always times out:
   - Try the account-specific FTP host instead of `ftpupload.net`
   - Retry at a different time
   - As fallback: run `npm run build` + `composer install --no-dev` locally and upload via FileZilla manually

### Read the logs

- If **“Test FTP connection”** fails → secrets or hostname are wrong (or InfinityFree blocks GitHub IPs).
- If test passes but deploy fails → upload too large/slow; split deploy should help.

### `553 Can't open that file: Permission denied`

This means FTP connected but **could not write** a file or folder.

1. **`FTP_SERVER_DIR` must point inside writable `htdocs`**
   - In FileZilla, after login, note your path when you see `index.html` / site files.
   - If PWD is `/` and that is your site root → `FTP_SERVER_DIR=/`
   - If PWD is `/` but site files are in `htdocs/` subfolder → `FTP_SERVER_DIR=/htdocs/`
   - **Never upload to paths above `htdocs`** — InfinityFree returns 553.

2. **Dotfiles blocked** — InfinityFree often rejects FTP uploads of `.env` and hidden sync files.
   - This repo uploads `laravel_core/production.env` instead of `.env`
   - `index.php.deploy` copies it to `.env` on the server at runtime
   - Deploy state files use `deploy-cache/*.json` (no leading dot)

3. **Delete broken remote folders** (via FileZilla) if a previous failed deploy left partial `laravel_core/` or `htdocs/htdocs/` nesting, then re-run the workflow.

4. **Ensure `laravel_core` is a folder** on the server, not a stray file.

---

## 9) Common InfinityFree issues

| Problem | Cause | Fix |
|---------|-------|-----|
| 500 on every page | Wrong `index.php` paths | Ensure `htdocs/index.php` is from `index.php.deploy` and `laravel_core/` exists |
| 404 on all routes | `.htaccess` / mod_rewrite | Use `.htaccess.deploy`; confirm `RewriteBase /` matches your URL |
| Blank CSS/JS | Vite not built | Check `npm run build` in Actions log; verify `htdocs/build/` exists on server |
| `Vite manifest not found` | Missing build folder | Re-run workflow; confirm `build/` uploaded |
| `No application encryption key` | Missing `APP_KEY` secret | Set `APP_KEY` in GitHub secrets |
| DB connection refused | Wrong host | Use `sqlXXX.infinityfree.com` from panel, not `127.0.0.1`, when migrating remotely |
| DB works on server but not CI | Remote MySQL blocked | Use `migrate-once.php` |
| Permission denied on logs/cache | Storage not writable | Via FTP, set `laravel_core/storage` and `laravel_core/bootstrap/cache` to `755` or `775` |
| Uploaded images 404 | No storage link | Ensure `htdocs/storage/` exists; workflow excludes it from overwrite |
| `laravel_core` exposed | Missing deny rule | Confirm `laravel_core/.htaccess` deployed |
| Session issues | `APP_URL` mismatch | `APP_URL` must exactly match site URL (https, no trailing slash) |
| PHP version error | PHP 7.x selected | Set PHP 8.2 in InfinityFree panel |

---

## 10) Subfolder deployment (optional)

If your site is `https://domain.com/subfolder/` instead of domain root, edit `public/.htaccess.deploy`:

```apache
RewriteBase /subfolder/
```

And set:

```env
APP_URL=https://domain.com/subfolder
```

---

## 11) Files in this repo for deployment

| File | Purpose |
|------|---------|
| `.github/workflows/deploy.yml` | CI/CD pipeline |
| `public/index.php.deploy` | Production `index.php` for `htdocs/` |
| `public/.htaccess.deploy` | Production Apache rewrites |
| `deploy/laravel_core.htaccess` | Deny web access to core |
| `deploy/migrate-once.php` | One-time migration via browser |

---

## 12) Your placeholders (fill in)

- **GitHub repo:** `your-username/madinova` (or your repo URL)
- **InfinityFree domain:** `https://yourname.infinityfreeapp.com`
- **Laravel:** 12.x
- **PHP on InfinityFree:** 8.2
- **Build tool:** Vite 7

After secrets are configured, every push to `main` rebuilds and deploys automatically.
