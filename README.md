<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Backup SISKALA

Project ini sudah memakai command backup harian `php artisan app:backup-data`.

Yang dibackup:
- dump database (`database.sql` atau `database.sqlite`)
- file upload di `storage/app/public`
- `manifest.json`
- file `.env` hanya jika `BACKUP_INCLUDE_ENV=true` atau command dijalankan dengan `--include-env`

Lokasi file backup:
- Default lokal dan default setelah publish: `storage/app/backups` di dalam folder project Laravel
- Rekomendasi untuk server/VM production: pindahkan ke folder di luar release project, misalnya `BACKUP_PATH=/var/backups/siskala`

Konfigurasi `.env` yang bisa dipakai:

```env
BACKUP_PATH=storage/app/backups
BACKUP_KEEP=14
BACKUP_SCHEDULE_TIME=01:00
BACKUP_INCLUDE_ENV=false
BACKUP_MYSQLDUMP_BINARY=C:\xampp\mysql\bin\mysqldump.exe
```

Catatan:
- Jika `BACKUP_PATH` relatif, path akan dibaca dari root project Laravel
- Jika `BACKUP_PATH` absolut, backup akan disimpan langsung ke folder itu
- Pada Linux VM biasanya cukup isi `BACKUP_MYSQLDUMP_BINARY=/usr/bin/mysqldump` bila binary tidak ada di `PATH`

Menjalankan backup manual:

```bash
php artisan app:backup-data
```

Menjalankan backup manual dan simpan lebih banyak arsip:

```bash
php artisan app:backup-data --keep=30
```

Agar backup harian benar-benar jalan di server:
- Laravel scheduler harus dipanggil tiap menit
- Contoh cron di Linux VM:

```bash
* * * * * cd /var/www/schedulo-djsn && php artisan schedule:run >> /dev/null 2>&1
```

Kalau memakai deploy berbasis release folder, sebaiknya `BACKUP_PATH` diarahkan ke folder permanen di VM, bukan ke dalam folder release yang bisa terganti saat deploy.

## Production Safety

Project ini sekarang memblok command artisan yang destruktif saat `APP_ENV=production`.

Command yang diblok:
- `migrate:fresh`
- `migrate:refresh`
- `migrate:reset`
- `migrate:rollback`
- `db:wipe`

Konfigurasi `.env`:

```env
DATA_SAFETY_BLOCK_DESTRUCTIVE_COMMANDS=true
ALLOW_DESTRUCTIVE_COMMANDS=false
```

Kalau suatu saat maintenance memang butuh command tersebut di production, buka sementara:

```env
ALLOW_DESTRUCTIVE_COMMANDS=true
```

Lalu kembalikan lagi ke `false` setelah maintenance selesai.

Catatan penting:
- Guard ini melindungi command artisan dari aplikasi
- Guard ini tidak bisa mencegah SQL manual dari phpMyAdmin atau akses root MySQL
- Untuk production tetap disarankan memakai user database khusus aplikasi yang tidak punya izin `DROP`, `CREATE`, atau `ALTER`

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
