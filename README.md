# Tewarach Portfolio CMS

A bilingual Laravel 12 portfolio CMS for Tewarach. The project extends an existing production-minded Laravel starter with portfolio content management, admin authentication, SEO settings, and a public developer portfolio for job applications, freelance clients, project showcases, starter/template showcases, and cloud deployment case studies.

## Stack

| Layer | Technology |
| --- | --- |
| Backend | Laravel 12, PHP 8.2+ |
| Frontend | Blade, Tailwind CSS 4, Alpine.js, Vite |
| Auth | Laravel session auth, Sanctum API auth |
| Authorization | Spatie Laravel Permission |
| Database | MySQL / MariaDB, SQLite supported for tests |
| Testing | PHPUnit |

## Features

- Public bilingual portfolio page for TH/EN visitors
- Hero, about, featured projects, starters, skills, services, experience, deployment case study, and contact sections
- Admin login, logout, login rate limiting, roles, permissions, users, translations, audit logs, and settings reused from the starter
- Portfolio CMS modules for site settings, hero/about/deployment content, projects, starters, skills, services, experience, contact links, and SEO
- SEO metadata, Open Graph tags, Twitter card tags, JSON-LD Person schema, `robots.txt`, and `sitemap.xml`
- Dark mode and large-text support from the starter shell
- Seeded Tewarach portfolio content and sample project/starter data

## Requirements

- PHP 8.2+
- Composer
- Node.js and npm
- MySQL or MariaDB for normal development
- SQLite is usable for tests

## Installation

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm run build
php artisan serve
```

Default seeded admin:

```text
Email: admin@example.com
Password: password
```

Set these before using the app outside local development:

```env
ADMIN_EMAIL=your-admin@example.com
ADMIN_PASSWORD=change-this-password
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.example
```

## Development

Run the Laravel server, queue listener, log tailing, and Vite together:

```bash
composer run dev
```

Or run only the frontend watcher:

```bash
npm run dev
```

## Admin Content Workflow

1. Log in at `/login`.
2. Open `/admin/portfolio`.
3. Update site-wide settings in Portfolio CMS > Site Settings.
4. Update public page copy in Hero Content, About Content, and Deployment Case Study.
5. Manage Projects, Starters, Skills, Services, Experience, and Contact Links from their CMS list pages.
6. Manage search/share metadata in SEO Settings.

Content edited by admin uses explicit TH/EN fields such as `title_th`, `title_en`, `description_th`, and `description_en`. UI labels still live in Laravel language files and database translation overrides.

## Bulk Project Import

For many portfolio projects, ask an AI assistant to inspect each project and output JSON using:

```text
docs/portfolio-projects.import.template.json
```

Then validate the file without writing records:

```bash
php artisan portfolio:import-projects storage/imports/projects.json --dry-run
```

Import or update projects by `slug`:

```bash
php artisan portfolio:import-projects storage/imports/projects.json
```

Production imports require an explicit guard:

```bash
php artisan portfolio:import-projects storage/imports/projects.json --dry-run
php artisan portfolio:import-projects storage/imports/projects.json --force
```

Imported projects default to `status: draft`, `is_public: false`, and `is_featured: false` unless the JSON sets them. This keeps AI-generated drafts out of the public portfolio until reviewed.

## Validation

```bash
composer validate --strict
php artisan test
npm run build
php artisan route:list --except-vendor
```

## Important Routes

Public:

```text
GET /              Portfolio homepage
GET /lang/{locale} Language switch
GET /robots.txt
GET /sitemap.xml
GET /health
```

Admin:

```text
GET /login
POST /login
POST /logout
GET /admin/portfolio
GET /admin/portfolio/settings
GET /admin/portfolio/content/{hero|about|deployment}
GET /admin/portfolio/projects
GET /admin/portfolio/starters
GET /admin/portfolio/skills
GET /admin/portfolio/services
GET /admin/portfolio/experiences
GET /admin/portfolio/links
GET /admin/portfolio/seo
```

## Permissions

The starter permissions are preserved and these portfolio permissions are added:

| Permission | Purpose |
| --- | --- |
| `portfolio.view` | View portfolio CMS dashboard |
| `portfolio.manage` | Manage portfolio content |
| `seo.manage` | Manage SEO settings |

## Azure VM Deployment Notes

Recommended production shape:

```text
Cloudflare DNS
  -> Azure VM
  -> Nginx reverse proxy
  -> Laravel apps via PHP-FPM
  -> Next.js demos via PM2
  -> MySQL/MariaDB single server with separate databases
  -> SSL / Certbot
```

Laravel deployment commands:

```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan queue:restart
```

Nginx notes:

- Point the Laravel site root to `public`.
- Send PHP requests to the correct PHP-FPM socket or host/port.
- Add separate server blocks for each portfolio subdomain or demo app.
- Reverse proxy Next.js demo apps to their PM2 ports.
- Enable gzip or Brotli where available.

PHP-FPM notes:

- Use a supported PHP 8.2+ FPM pool.
- Ensure the web user can read the project and write to `storage` and `bootstrap/cache`.
- Reload PHP-FPM after deployment.

MySQL/MariaDB notes:

- Do not use the root database user for the app.
- Create one database/user for the portfolio CMS.
- Use separate databases for demo projects hosted on the same VM.
- Schedule database backups and test restore procedures.

PM2 notes for Next.js demos:

- Build each Next.js project before running it.
- Use an `ecosystem.config.js` per demo or one shared PM2 ecosystem file.
- Configure Nginx to proxy each demo subdomain to the correct PM2 port.

SSL / Certbot notes:

- Issue certificates after DNS points to the Azure VM.
- Enable auto-renewal.
- Check renewal with `certbot renew --dry-run`.

Cloudflare notes:

- Create A records for the main portfolio domain and project subdomains.
- Use proxied records when appropriate.
- Keep SSL mode compatible with the VM certificate setup.

## Production Checklist

- Change default admin credentials.
- Set `APP_ENV=production` and `APP_DEBUG=false`.
- Set a real `APP_URL`.
- Configure mail before enabling password resets in production.
- Set durable database/session/cache/queue drivers.
- Run `php artisan storage:link`.
- Confirm `/`, `/login`, `/admin/portfolio`, `/health`, `/robots.txt`, and `/sitemap.xml`.
- Review Content Security Policy when adding external asset domains.
- Configure backups, uptime monitoring, log rotation, and SSL renewal.
