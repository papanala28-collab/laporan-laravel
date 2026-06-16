# Laporan Harian Laravel

Aplikasi Laravel + Livewire + Breeze untuk mencatat laporan harian proyek.

## Fitur

- Login wajib dengan Laravel Breeze
- Master proyek
- Form laporan harian proyek
- Daftar laporan dengan filter proyek dan tanggal
- Halaman detail laporan
- Halaman print laporan

## Stack

- Laravel 13
- Livewire 3
- Laravel Breeze
- PostgreSQL
- Redis
- Vite + Tailwind CSS

## Konfigurasi

Environment default sudah diarahkan ke:

- `APP_URL=https://harian.pladen62.online`
- `DB_CONNECTION=pgsql`
- `SESSION_DRIVER=redis`
- `CACHE_STORE=redis`
- `QUEUE_CONNECTION=redis`

Isi kredensial database PostgreSQL di file `.env` sebelum menjalankan migration.

## Menjalankan Aplikasi

```bash
composer install
npm install
php artisan key:generate
php artisan migrate
npm run build
php artisan serve
```

## Catatan Testing

Server ini tidak memiliki driver `sqlite`, jadi konfigurasi test diarahkan ke PostgreSQL.
Pastikan database `laporan_db_test` sudah dibuat sebelum menjalankan `php artisan test`.
