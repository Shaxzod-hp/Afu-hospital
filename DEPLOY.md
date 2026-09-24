# Hostingga joylash (afu-hospital.uz)

Tuzilma: **frontend** (Vue, statik fayllar) → `https://afu-hospital.uz`,
**backend** (Laravel API) → `https://api.afu-hospital.uz`.

## 1. Backend (api.afu-hospital.uz)

Talablar: PHP 8.1+, MySQL 8, Composer. Domen `backend/public` papkasiga qarashi kerak.

```bash
composer install --no-dev --optimize-autoloader
cp .env.example .env        # keyin .env ni to'ldiring (pastga qarang)
php artisan key:generate    # faqat birinchi marta
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan telegram:test   # bot ishlashini tekshirish (--send bilan test xabar yuboradi)
```

`.env` da albatta bo'lishi kerak:

```dotenv
APP_NAME="Alfraganus University Hospital"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://api.afu-hospital.uz
LOG_LEVEL=warning

FRONTEND_URL=https://afu-hospital.uz,https://www.afu-hospital.uz

DB_DATABASE=...
DB_USERNAME=...
DB_PASSWORD=...

TELEGRAM_BOT_TOKEN=...
TELEGRAM_GROUP_CHAT_ID=...
```

Cron (davolash lavhalarini 24 soatdan keyin o'chirish uchun):

```
* * * * * cd /path/to/backend && php artisan schedule:run >> /dev/null 2>&1
```

> `.env` ni o'zgartirgandan keyin har safar `php artisan config:cache` ni qayta ishga tushiring —
> aks holda eski qiymatlar qoladi (masalan, Telegram bot ishlamaydi).

`storage/` va `bootstrap/cache/` papkalariga web-server yozish huquqi bo'lishi kerak.

Rasm yuklash limitlari `backend/public/.user.ini` (PHP-FPM) va `.htaccess` (mod_php) orqali
10 MB / 64 MB ga ko'tariladi. Agar hosting bu fayllarni e'tiborsiz qoldirsa, panelda
`upload_max_filesize=10M`, `post_max_size=64M` ni qo'lda o'rnating.

## 2. Frontend (afu-hospital.uz)

```bash
cd frontend
npm ci
npm run build
```

`frontend/dist/` ichidagi **hamma** fayllarni (shu jumladan `.htaccess`) domen papkasiga yuklang.
`.env.production` dagi manzillar:

```dotenv
VITE_API_URL=https://api.afu-hospital.uz/api
VITE_STORAGE_URL=https://api.afu-hospital.uz
```

Nginx ishlatilsa, `.htaccess` o'rniga: `try_files $uri $uri/ /index.html;`

## 3. Tekshirish

- `https://api.afu-hospital.uz/api/doctors` — JSON qaytarishi kerak
- Saytdagi har bir sahifani ochib, **yangilash (F5)** — 404 chiqmasligi kerak
- Aloqa formasidan test ariza yuboring — Telegram guruhga kelishi kerak
- Admin panel: `https://afu-hospital.uz/admin/login`

## 4. Testlar (lokal)

```bash
cd backend && php artisan config:clear && php artisan test
```

Testlar faqat in-memory SQLite'da ishlaydi, haqiqiy bazaga tegmaydi.
