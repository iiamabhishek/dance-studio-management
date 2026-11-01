# Production Deploy — Gymie on Vercel + External MySQL (Aiven/Railway)

## Architecture on Vercel
| Concern | Solution |
|---|---|
| PHP runtime | `vercel-php@0.7.4` (PHP 8.4) via `api/index.php` |
| Database | External MySQL (Aiven free tier or Railway MySQL) |
| Cache / Sessions | `database` driver (tables already exist) |
| Settings (Studio Info) | `SETTINGS_STORE=database` → new `settings` table |
| Uploaded logo | `FILESYSTEM_DISK=s3` (S3/static storage) |
| Scheduler + queue | Vercel Cron → `GET /v/cron` (requires `X-Cron-Secret` header) |
| Static assets | served from `public/` before app routes |

## One-time repo state (already prepared)
- `vercel.json` — runtime + cron
- `api/index.php` — serverless entry
- `app/Http/Controllers/CronController.php` + `routes/web.php /v/cron`
- `app/Services/DatabaseSettingsRepository.php` + migration `create_settings_table`
- `composer.json` + `league/flysystem-aws-s3-v3`

## Required Vercel env vars
| Key | Example / what it is |
|---|---|
| `APP_NAME` | `VR Dance Studio` |
| `APP_KEY` | `base64:...` — generate: `php artisan key:generate --show` |
| `APP_ENV` | `production` |
| `APP_URL` | your vercel app URL e.g. `https://gymie.vercel.app` |
| `APP_DEBUG` | `false` |
| `SETTINGS_STORE` | `database` |
| `CACHE_STORE` | `database` |
| `SESSION_DRIVER` | `database` |
| `QUEUE_CONNECTION` | `database` |
| `FILESYSTEM_DISK` | `s3` |
| `DB_CONNECTION` | `mysql` |
| `DB_HOST` | Aiven / Railway MySQL host |
| `DB_PORT` | `3306` |
| `DB_DATABASE` | `gymie` |
| `DB_USERNAME` | per Aiven/Railway credentials |
| `DB_PASSWORD` | per Aiven/Railway credentials |
| `CRON_SECRET` | any long random string |
| `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, `AWS_DEFAULT_REGION`, `AWS_BUCKET`, `AWS_URL` | S3 / Cloudflare R2 / Backblaze credentials |
| `MAIL_*` | transactional SMTP (Amazon SES, Resend, Brevo, etc.) |

## Deploy steps
1. Create MySQL (Aiven free tier or Railway) → copy host/user/pass/db
2. `vercel login` then `vercel link`
3. Set all env vars (CLI `vercel env add ...` or dashboard)
4. First deploy: `vercel --prod`
5. Inside Vercel shell or local with a tunnelled `.env` (matching prod values):
   `php artisan migrate --force` (creates 32 tables incl. settings)
   `php artisan shield:generate --all --panel=admin`
   then create Production admin user via seeded convention or `make:filament-user`
6. Verify Cron: dashboard → Crons → `0 0 * * *` hitting `/v/cron`
7. Login at `https://<app>.vercel.app`

## Guardrails
- `APP_DEBUG=false` must stay after first login check
- MySQL connection limits: Aiven free tier allows 20 connections; Laravel + Filament may open up to that
- `--no-dev` composer already excludes Scramble/Pail
- Exclude `storage/logs` writing on runtime errors: `LOG_CHANNEL=stderr` also possible
