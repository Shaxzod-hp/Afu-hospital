# Alfraganus University Hospital — veb-sayt

Klinika sayti va admin panel: shifokorlar, xizmatlar, operatsiyalar, statsionar paketlari,
yangiliklar va bemor murojaatlari (Telegram guruhga bildirishnoma bilan).

| Qism | Texnologiya | Papka |
|---|---|---|
| Backend (API) | Laravel 10, Sanctum, MySQL 8 | `backend/` |
| Frontend | Vue 3, Pinia, Vue Router, Vite, Bootstrap 5 | `frontend/` |

## Lokal ishga tushirish

**Backend** (PHP 8.1+, Composer, MySQL):

```bash
cd backend
composer install
cp .env.example .env          # DB_*, TELEGRAM_*, ADMIN_SEED_PASSWORD ni to'ldiring
php artisan key:generate
php artisan migrate --seed    # admin foydalanuvchini ham yaratadi
php artisan storage:link
php artisan serve             # http://localhost:8000
```

**Frontend** (Node 20+):

```bash
cd frontend
npm install
npm run dev                   # http://localhost:5173  (/api va /storage backend'ga proxy qilinadi)
```

Admin panel: `http://localhost:5173/admin/login`

## Foydali buyruqlar

```bash
php artisan test              # backend testlari (in-memory SQLite, haqiqiy bazaga tegmaydi)
php artisan telegram:test     # Telegram bot sozlamalarini tekshirish (--send — test xabar)
php artisan treatment-logs:cleanup   # 24 soatdan eski davolash lavhalarini o'chirish
```

## Hostingga joylash

[DEPLOY.md](DEPLOY.md) ga qarang.
