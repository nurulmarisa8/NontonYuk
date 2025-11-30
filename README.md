## NontonYuk - Sistem Pemesanan Tiket Bioskop
<p>NontonYuk adalah aplikasi web untuk pemesanan tiket bioskop yang dibangun dengan framework Laravel. Aplikasi ini memungkinkan pengguna untuk melihat jadwal film, melakukan pemesanan tiket, dan mengelola data film (untuk admin).</p>

## Struktur Project
```
NontonYuk/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── ActiveController.php
│   │       ├── BookingController.php
│   │       ├── HomeController.php
│   │       └── UserController.php
│   ├── Models/
│   │   ├── Booking.php
│   │   ├── Movie.php
│   │   ├── Schedule.php
│   │   ├── Studio.php
│   │   └── User.php
│   ├── Providers/
│   │   ├── AppServiceProvider.php
│   │   └── Kernel.php
│   └── Controller.php
├── bootstrap/
│   ├── cache/
│   ├── app.php
│   └── provider.php
├── config/
│   ├── app.php
│   ├── auth.php
│   ├── cache.php
│   ├── database.php
│   ├── diagrammap.php
│   ├── logging.php
│   ├── mail.php
│   ├── queue.php
│   └── services.php
├── database/
│   ├── factories/
│   │   └── UserFactory.php
│   ├── migrations/
│   │   ├── 0001_01_01_00000_create_users_table.php
│   │   ├── 0001_01_01_00001_create_cache_table.php
│   │   ├── 0001_01_01_00002_create_jobs_table.php
│   │   ├── 2005_11_25_151682_create_movies_table.php
│   │   ├── 2005_11_25_151682_create_schedules_table.php
│   │   ├── 2005_11_25_151684_create_bookings_table.php
│   │   ├── 2005_11_25_151684_add_roles_to_users_table.php
│   │   ├── 2005_11_25_05408_create_archive_table.php
│   │   ├── 2005_11_25_05419_update_schedules_table_add_studio_id.php
│   │   ├── 2005_11_25_05418_add_status_to_movies_table.php
│   │   ├── 2005_11_25_05419_update_bookings_table_fix_seat_booking.php
│   │   ├── 2005_11_25_05427_change_status_column_length_in_bookings_table.php
│   │   ├── 2005_11_25_05427_add_price_to_schedules_table.php
│   │   ├── 2005_11_25_154812_update_protect_column_in_movies_table.php
│   │   ├── 2005_11_25_050205_add_whatsapp_number_in_users_table.php
│   │   ├── 2005_11_25_12103_add_unique_constraint_to_users_in_bookings_table.php
│   │   ├── 2005_11_25_213031_modify_customer_phone_variables_in_bookings_table.php
│   │   └── 2005_11_25_000002_remove_unnecessary_column_from_users_table.php
│   └── locations/
├── public/
├── resources/
│   ├── views/
│   │   ├── admin/
│   │   │   ├── movies/
│   │   │   │   ├── create.blade.php
│   │   │   │   ├── edit.blade.php
│   │   │   │   └── index.blade.php
│   │   │   ├── schedule.blade.php
│   │   │   ├── dashboard.blade.php
│   │   │   └── login.blade.php
│   │   ├── auth/
│   │   │   ├── login.blade.php
│   │   │   ├── register.blade.php
│   │   │   └── create.blade.php
│   │   ├── my-bookings.blade.php
│   │   └── layouts/
│   │       ├── admin.blade.php
│   │       ├── app.blade.php
│   │       ├── dashboard.blade.php
│   │       └── schedule.blade.php
│   └── tests/
├── routes/
│   ├── console.php
│   ├── web.php
│   └── api.php
├── storage/
├── tests/
├── .env.example
├── .gitattributes
├── .gitignore
├── composer.json
├── package.json
├── phpunit.xml
├── README.md
└── vite.config.js
```

## Fitur Utama

### Untuk Pengguna
- Registrasi dan login pengguna
- Melihat jadwal film dan studio
- Melakukan pemesanan tiket
- Melihat riwayat pemesanan (my-bookings)
- Manajemen profil pengguna

### Untuk Admin
- Dashboard admin
- Manajemen data film (CRUD)
- Manajemen jadwal tayang
- Monitoring pemesanan tiket
- Manajemen studio bioskop

## Teknologi yang Digunakan

- **Backend**: Laravel Framework
- **Frontend**: Blade Templating, Vite
- **Database**: MySQL
- **Authentication**: Laravel Auth System
- **Testing**: PHPUnit

# Panduan Menjalankan Aplikasi NontonYuk

## Prasyarat
Sebelum menjalankan aplikasi, pastikan software berikut sudah terinstall di komputer Anda:

- **PHP** (versi 8.0 atau lebih tinggi)
- **Composer**
- **Node.js** dan **NPM**
- **MySQL** atau database server lainnya
- **Git**

## Langkah-langkah Instalasi dan Menjalankan Aplikasi

### 1. Clone dan Setup Awal
```bash
git clone https://github.com/nurulmarisa8/NontonYuk.git
cd NontonYuk
composer install
npm install
cp .env.example .env
php artisan key:generate
'''
