# Backup VM

Project ini sekarang memiliki command backup bawaan:

```bash
php artisan app:backup-data --keep=14
```

Yang dibackup:

- file upload pada `storage/app/public`
- dump database untuk `mysql`, `pgsql`, atau salinan file untuk `sqlite`
- `manifest.json` berisi metadata backup

Opsional menyertakan `.env`:

```bash
php artisan app:backup-data --include-env --keep=14
```

Lokasi arsip backup:

```text
storage/app/backups/schedulo-backup-YYYYmmdd_HHMMSS.zip
```

## Rekomendasi di VM Linux

Jalankan scheduler Laravel:

```bash
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

Karena scheduler sudah diatur harian pukul `01:00`, backup otomatis akan dibuat setiap hari.

## Restore singkat

1. Extract file zip backup.
2. Restore folder `storage/public` ke `storage/app/public`.
3. Restore database:
   - MySQL / PostgreSQL: import file `database/database.sql`
   - SQLite: gantikan file database dengan `database/database.sqlite`
4. Jalankan:

```bash
php artisan optimize:clear
php artisan storage:link
```

## Catatan

- Untuk MySQL, `mysqldump` harus tersedia di VM.
- Untuk PostgreSQL, `pg_dump` harus tersedia di VM.
- Jika dump database gagal, arsip zip tetap dibuat dan berisi `DATABASE_WARNING.txt`.
