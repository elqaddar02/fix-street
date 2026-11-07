# Deploying Madinup to Production

> **Stack:** Hetzner Cloud VPS (CX22) · Ubuntu 24.04 · Coolify · Cloudflare Full Strict SSL

---

## 1. Infrastructure Overview

| Component | Choice | Notes |
|-----------|--------|-------|
| VPS | Hetzner CX22 | 2 vCPU, 4 GB RAM, 40 GB NVMe — plenty for early traffic |
| PaaS layer | Coolify (self-hosted) | Zero-downtime deploys, env management, reverse proxy |
| Reverse proxy | Caddy (bundled with Coolify) | Auto-TLS from Let's Encrypt or Cloudflare origin cert |
| CDN / DNS | Cloudflare (proxied) | Full Strict mode; hides VPS IP |
| Database | MySQL 8 container (Coolify) | Or a managed DB like Hetzner Managed MySQL |
| Queue | Laravel Horizon (Redis) | Coolify runs it as a separate process |
| Storage | Local disk + `storage:link` | Upgrade to S3-compatible later if needed |

---

## 2. Cloudflare Setup

1. Add your domain → **DNS** tab → `A` record pointing to your VPS IP, **Proxied (orange cloud)**.
2. **SSL/TLS** → Overview → set mode to **Full (strict)**.
3. Create a **Cloudflare Origin Certificate** (15-year) and download `cert.pem` + `key.pem`.
4. Upload these certs to Coolify's **SSL** section for the application.

> The app already has `trustProxies(at: '*')` in `bootstrap/app.php` so Cloudflare IPs are trusted correctly.

---

## 3. Coolify Application Setup

1. **New Resource → Dockerfile / Git repository** — point to your GitHub repo.
2. Set **Build Pack** to `nixpacks` or use a custom `Dockerfile` (see below).
3. Add all environment variables from `.env.production.example` (rename it to `.env` on the server via Coolify's secret editor).
4. Set **Port** to `8000` (or whatever your web server listens on).
5. Enable **Auto-deploy on push** for your production branch (`main`).

---

## 4. Required Environment Variables

Copy `.env.production.example` to Coolify's environment editor and fill in:

```ini
APP_NAME=Madinup
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_HOST=127.0.0.1          # or Coolify MySQL service hostname
DB_PORT=3306
DB_DATABASE=madinup
DB_USERNAME=madinup_user
DB_PASSWORD=<strong-password>

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org  # or Resend / Brevo / SES
MAIL_PORT=587
MAIL_USERNAME=<key>
MAIL_PASSWORD=<secret>
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Madinup"

SESSION_DRIVER=database
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax

QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1

CACHE_STORE=redis
```

---

## 5. Deploy Script

The file `deploy/deploy.sh` is the canonical deploy script. Coolify can call it as a post-deploy hook, or you can run it manually over SSH:

```bash
ssh user@your-vps-ip
cd /var/www/madinup
bash deploy/deploy.sh
```

What it does, in order:
1. `php artisan down` — maintenance mode
2. `composer install --no-dev --optimize-autoloader`
3. `npm ci && npm run build` — Vite production build
4. `php artisan migrate --force`
5. `php artisan storage:link`
6. Config / route / view cache
7. `php artisan queue:restart`
8. `php artisan up`

---

## 6. First-Time Server Setup (Hetzner CX22, Ubuntu 24.04)

```bash
# Install Coolify
curl -fsSL https://cdn.coollabs.io/coolify/install.sh | bash

# Coolify UI is at http://<VPS-IP>:8000 — set up via browser
# Then add your GitHub repo as a new resource
```

The Coolify installer handles: Docker, Caddy, Redis, and optional MySQL.

---

## 7. Checklist Before Going Live

- [ ] `APP_DEBUG=false` in production env
- [ ] `SESSION_SECURE_COOKIE=true`
- [ ] Cloudflare SSL mode: **Full (strict)**
- [ ] Run `php artisan key:generate` once and store `APP_KEY` safely
- [ ] `php artisan storage:link` has been executed
- [ ] Database seeded with required data (`php artisan db:seed --class=ProductionSeeder` if exists)
- [ ] Queue worker running (`php artisan queue:work` or Horizon)
- [ ] Cron job for Laravel scheduler:
  ```
  * * * * * cd /var/www/madinup && php artisan schedule:run >> /dev/null 2>&1
  ```
- [ ] Verify `php artisan test` passes on the branch being deployed

---

## 8. Rolling Back

```bash
# In Coolify UI → Deployments → click any previous deployment → Rollback
# Or via git:
git revert HEAD
git push origin main
```

---

*Last updated: November 2026 — target platform changed from InfinityFree FTP to Hetzner + Coolify + Cloudflare.*
