# Undangan Digital

Aplikasi undangan digital berbasis Laravel untuk pengelolaan produk undangan, order, pembayaran, notifikasi, dan laporan admin.

## Fitur Utama

- Manajemen produk undangan
- Proses pemesanan undangan
- Detail pemesanan (termasuk nomor HP)
- Pembayaran dan riwayat order
- Notifikasi sistem
- Halaman admin dan laporan

## Tech Stack

- PHP 8+
- Laravel 11
- MySQL/MariaDB
- Blade Template
- JavaScript (frontend assets)
- Docker (opsional)

## Struktur Folder Inti

- `app/` logic aplikasi (controller, model, service)
- `config/` konfigurasi aplikasi
- `database/migrations/` skema database
- `resources/views/` blade view frontend/admin
- `routes/web.php` routing utama
- `public/` entry point dan asset publik

## Instalasi Lokal

1. Clone repository:

```bash
git clone https://github.com/USERNAME/undangan-digital.git
cd undangan-digital
```

2. Install dependency:

```bash
composer install
```

3. Salin environment file:

```bash
cp .env.example .env
```

Jika memakai Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

Jika file `.env.example` belum tersedia, gunakan file cadangan berikut:

```powershell
Copy-Item ".env copy.example" .env
```

4. Generate app key:

```bash
php artisan key:generate
```

5. Atur koneksi database di file `.env`, lalu jalankan migrasi:

```bash
php artisan migrate
```

6. (Opsional) Jalankan seeder data awal:

```bash
php artisan db:seed
```

7. Jalankan server development:

```bash
php artisan serve
```

Aplikasi dapat diakses di `http://127.0.0.1:8000`.

## Menjalankan Dengan Docker (Opsional)

Project sudah menyediakan `Dockerfile` dan `docker-compose.yml`.

```bash
docker compose up -d --build
```

Setelah container aktif, lanjutkan setup aplikasi (migrasi dan key generate) di dalam container sesuai konfigurasi service Anda.

## Testing

```bash
php artisan test
```

Atau:

```bash
vendor/bin/phpunit
```
## Catatan

- Pastikan file `.env` tidak ikut ter-push.
- Folder `vendor/` sebaiknya diabaikan via `.gitignore`.
- Sesuaikan konfigurasi mail/queue jika fitur notifikasi dipakai di production.

## License

MIT
