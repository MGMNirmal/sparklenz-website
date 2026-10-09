# Sparklenz — Beyond the Frame

The website for Sparklenz, a Sri Lankan creative studio. It is built with Laravel 13, MySQL, Blade, Tailwind CSS 4, Alpine.js and GSAP.

- **Public pages:** Home, About & Team, Services & Packages, Our Work and Contact (`/`, `/about`, `/services`, `/work`, `/contact`).
- **Screening room:** full films play from YouTube inside a dialog on the site. Projects can also have an optional MP4/WebM teaser that plays silently when a visitor hovers over the card.
- **Owner admin at `/admin`:** manages work, rates, packages, team, page copy, contact details and enquiries.

## Requirements

- PHP 8.3+ (tested on 8.5) with the `pdo_mysql`, `gd` (with WebP support), `fileinfo`, `mbstring` and `exif` extensions
- Composer 2
- Node 20+ and npm
- MySQL 8+

## Local setup

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
```

1. Create the database and a user:

   ```sql
   CREATE DATABASE sparklenz CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   CREATE USER 'sparklenz'@'localhost' IDENTIFIED BY 'choose-a-password';
   GRANT ALL ON sparklenz.* TO 'sparklenz'@'localhost';
   ```

2. Fill in `DB_*` in `.env`. Then run:

   ```bash
   php artisan migrate --seed       # rate card, contact details, page copy (+ demo work outside production)
   php artisan storage:link         # serves uploaded media from /storage
   npm run build                    # or `npm run dev` while working on the front end
   php artisan serve
   ```

`php artisan serve` uses PHP's default upload limit of 2 MB. For larger cover images and teasers, run the built-in server with higher limits (it must be started from the `public` folder):

```bash
cd public && php -d upload_max_filesize=20M -d post_max_size=40M -S 127.0.0.1:8000 \
  ../vendor/laravel/framework/src/Illuminate/Foundation/resources/server.php
```

### Create the first admin

There is no public registration and no default account. Create the studio owner from the command line:

```bash
php artisan sparklenz:create-admin --email=owner@example.com --name="Studio Owner"
```

The command asks for the password; it needs at least 12 characters, with letters and numbers. It is never passed as an argument, so it does not end up in shell history. For automated provisioning, set `SPARKLENZ_ADMIN_PASSWORD` in the environment instead. Running the command again with the same email resets that account's password.

## Content

| What | Where in admin | Notes |
|---|---|---|
| Portfolio work | **Work** | Order with ↑/↓. Toggle Featured and Published. The first featured project becomes the home-page hero poster. |
| Films | Work → *YouTube link* | Paste a `youtube.com/watch`, `youtu.be` or `youtube.com/shorts` link. Only the 11-character ID is stored. The video must be **public or unlisted**, with **embedding allowed**. Choose *Portrait* for Shorts/Reels so they are not cropped. |
| Hover teaser | Work → *Hover teaser* | Optional silent MP4/WebM loop, 6–10 seconds, ideally under 4 MB (maximum 15 MB). If a project has no teaser, a muted YouTube preview is tried on hover; when the browser blocks it, the poster stays. |
| Covers & galleries | Work | JPG, PNG or WebP. Covers must be at least 800 × 450 px and gallery images at least 600 × 400 px. Every image needs alt text. |
| Service rates | **Services & rates** | Prices are whole LKR numbers; units such as "per script" or "each" are stored separately. Leave the price empty for "on request". *Previous price* shows a struck-through amount. |
| Monthly packages | **Packages** | Tiers support a second price column (used by Spark Produce for "with" and "without presenting"). |
| Team | **Team** | Profiles with an optional portrait. |
| Page wording | **Page copy** | Plain text only; line breaks are kept. |
| Contact & social links | **Contact & social** | An empty social link is hidden. The Facebook URL is still empty, because the rate card gives only the page name "Sparklenz". |
| Enquiries | **Enquiries** | Opening a new enquiry marks it *Read*. You can then set *Replied* (or back to *New*). |

### Demo content

`DemoContentSeeder` adds six **concept projects**: fictional brands, using Unsplash photography by Manuel Campana, Nathan Dumlao, Master Filmmaker and AYKUT AKTAŞ, plus teaser clips generated from those stills. It also adds three **placeholder team profiles**. All of these records are flagged `is_demo` and carry a "Concept project" or "Placeholder profile" label on the site.

- `SHOW_DEMO_CONTENT` defaults to `false` in production, so these records never appear on the live site.
- `php artisan migrate --seed` skips the demo seeder when `APP_ENV=production`.
- Replace the demo records with real work, or untick **Concept / demo** once a project is approved.

## Media & storage

- Uploads go to the `public` disk (`storage/app/public`), served through `public/storage`. Only paths and dimensions are stored in MySQL.
- Images are decoded and re-encoded to WebP with GD. This removes metadata and any embedded payloads. Each image gets a large rendition (2000 px max) and a `-sm` rendition (900 px) with random UUID filenames.
- Teasers are accepted only as `video/mp4` or `video/webm`, checked by both extension and detected MIME type.
- No video files are fetched from YouTube; the site embeds YouTube's official player only.
- To serve media from a CDN or S3, configure a disk in `config/filesystems.php` and point the `public` disk's `url` at the CDN.

## YouTube playback notes

- The IFrame Player API is loaded once, only when someone previews or plays a film.
- Players are created with `enablejsapi=1`, `origin`, `playsinline=1` and `rel=0`. Note that `rel=0` limits related videos to the same channel; it does not remove them. The site sends `Referrer-Policy: strict-origin-when-cross-origin`, which YouTube needs to identify the embedding site; without it the player shows error 153.
- Titles, captions and buttons sit outside the player, and nothing covers it. Ad-free or brand-free playback is not promised.
- Private or removed videos, disabled embedding, error 153, a failed API load and blocked autoplay each show a message below the player, with a "Watch on YouTube" fallback link where it helps.

## Mail

Every enquiry is saved to the database before the visitor sees a success message. To also email new enquiries, set `ENQUIRY_NOTIFY_TO` and configure SMTP:

```dotenv
MAIL_MAILER=smtp
MAIL_HOST=smtp.your-provider.example
MAIL_PORT=587
MAIL_USERNAME=...
MAIL_PASSWORD=...
MAIL_FROM_ADDRESS="website@your-domain.example"
ENQUIRY_NOTIFY_TO=sparklenzstudio@gmail.com
```

If sending fails, the enquiry is kept and the failure is logged. Locally, `MAIL_MAILER=log` writes emails to `storage/logs/laravel.log`.

## Security summary

- `/admin` requires login and the `manage-site` gate (`users.is_admin`).
- Passwords are hashed with bcrypt.
- Login is throttled to 5 attempts per minute per email and IP.
- All forms use CSRF protection.
- The contact form has a honeypot and is rate-limited to 3 enquiries per minute and 10 per hour per IP.
- Visitor IPs are stored only as HMAC hashes.
- Output is escaped. Project data reaches JavaScript as HEX-escaped JSON and is inserted with `textContent`.
- No iframe HTML and no arbitrary URLs are accepted; YouTube links are validated against a list of allowed hosts and an ID pattern.
- Admin responses send `X-Robots-Tag: noindex` and `Cache-Control: no-store`. `robots.txt` blocks everything outside production and blocks `/admin` in production. `sitemap.xml` lists only the five public pages.

## Tests

```bash
php artisan test
```

Tests run against a MySQL database named `sparklenz_test` (see `phpunit.xml`), using the same host and credentials as `.env`. Create it before the first run.

## Deployment

1. Set up the server with PHP 8.3+ (with the extensions listed above), MySQL 8 and a web server whose document root is `public/`.
2. Use these production `.env` values:
   - `APP_ENV=production`, `APP_DEBUG=false`
   - `APP_URL=https://your-domain` (canonical URLs and the sitemap use it)
   - `SESSION_SECURE_COOKIE=true`
   - real `DB_*` and mail settings
3. Deploy:

   ```bash
   composer install --no-dev --optimize-autoloader
   npm ci && npm run build
   php artisan migrate --force
   php artisan db:seed --class=SiteContentSeeder --force
   php artisan db:seed --class=RateCardSeeder --force   # first deploy only — it resets rates to the rate card
   php artisan storage:link
   php artisan config:cache && php artisan route:cache && php artisan view:cache
   php artisan sparklenz:create-admin
   ```

4. Raise PHP's `upload_max_filesize` to 20M and `post_max_size` to 40M.
5. Serve over HTTPS.

Do **not** re-run `RateCardSeeder` after the owner has edited rates: it restores the original rate-card values. `SiteContentSeeder` is safe to re-run because it never overwrites values that already exist.

## Backups

Back up both of these, for example nightly:

- **The database:** `mysqldump --single-transaction sparklenz > sparklenz-$(date +%F).sql`
- **Uploaded media:** `storage/app/public` (covers, galleries, teasers, portraits)

Keep copies off the server, and test a restore now and then. Full films live on YouTube, so they are not part of the site backup.

## Assets & licences

- The Sparklenz logos (`public/images/brand`) were extracted unchanged from the supplied prototype. The favicon uses the "S" from that same artwork.
- Manrope and DM Sans are self-hosted as WOFF2 under the SIL Open Font License.
- The concept photography comes from Unsplash under the Unsplash licence and is used for demo content only.
