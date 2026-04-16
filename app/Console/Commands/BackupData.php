<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\Process\Process;
use ZipArchive;

class BackupData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:backup-data
                            {--include-env : Sertakan file .env ke dalam arsip backup}
                            {--keep= : Jumlah arsip backup terbaru yang dipertahankan}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Membuat backup data aplikasi (database + file upload) ke storage/app/backups';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $backupDirectory = $this->resolveBackupDirectory();
        $keep = max((int) ($this->option('keep') ?? config('backup.keep', 14)), 1);
        $includeEnv = $this->shouldIncludeEnv();

        File::ensureDirectoryExists($backupDirectory);

        $timestamp = now()->format('Ymd_His');
        $temporaryDirectory = $this->buildTemporaryDirectory($timestamp);
        File::ensureDirectoryExists($temporaryDirectory);

        $zipPath = $backupDirectory . DIRECTORY_SEPARATOR . "schedulo-backup-{$timestamp}.zip";
        $workingZipPath = $this->buildTemporaryZipPath($timestamp);

        $databaseResult = $this->dumpDatabase($temporaryDirectory);
        $manifest = [
            'generated_at' => now()->toIso8601String(),
            'application' => config('app.name'),
            'environment' => config('app.env'),
            'backup_path' => $zipPath,
            'database_connection' => config('database.default'),
            'database_backup' => [
                'status' => $databaseResult['status'],
                'file' => $databaseResult['archive_name'] ?? null,
                'message' => $databaseResult['message'] ?? null,
            ],
            'retention' => [
                'keep_archives' => $keep,
            ],
            'includes' => [
                'storage/public',
            ],
        ];

        if ($includeEnv) {
            $manifest['includes'][] = '.env';
        }

        $zip = new ZipArchive();
        if ($zip->open($workingZipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            $this->cleanupTemporaryDirectory($temporaryDirectory);
            File::delete($workingZipPath);
            $this->error('Gagal membuat file zip backup.');

            return self::FAILURE;
        }

        $this->addDirectoryToZip($zip, storage_path('app/public'), 'storage/public');

        if (($databaseResult['path'] ?? null) && File::exists($databaseResult['path'])) {
            $zip->addFile($databaseResult['path'], $databaseResult['archive_name']);
        }

        if ($includeEnv && File::exists(base_path('.env'))) {
            $zip->addFile(base_path('.env'), 'app/.env');
        }

        $zip->addFromString(
            'manifest.json',
            json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );

        if (!empty($databaseResult['warning'])) {
            $zip->addFromString('DATABASE_WARNING.txt', $databaseResult['warning']);
        }

        $zip->close();
        File::delete($zipPath);
        File::move($workingZipPath, $zipPath);

        $this->cleanupTemporaryDirectory($temporaryDirectory);
        $this->pruneOldBackups($backupDirectory, $keep);

        $this->info("Backup berhasil dibuat: {$zipPath}");

        if (!empty($databaseResult['warning'])) {
            $this->warn($databaseResult['warning']);
        }

        return self::SUCCESS;
    }

    private function dumpDatabase(string $temporaryDirectory): array
    {
        return match (config('database.default')) {
            'sqlite' => $this->dumpSqliteDatabase($temporaryDirectory),
            'mysql' => $this->dumpMysqlDatabase($temporaryDirectory),
            'pgsql' => $this->dumpPgsqlDatabase($temporaryDirectory),
            default => [
                'status' => 'skipped',
                'warning' => 'Backup database dilewati karena driver database ini belum didukung oleh command backup.',
            ],
        };
    }

    private function dumpSqliteDatabase(string $temporaryDirectory): array
    {
        $databasePath = config('database.connections.sqlite.database');

        if (!$databasePath || $databasePath === ':memory:' || !File::exists($databasePath)) {
            return [
                'status' => 'skipped',
                'warning' => 'Backup database SQLite dilewati karena file database tidak ditemukan.',
            ];
        }

        $outputPath = $temporaryDirectory . DIRECTORY_SEPARATOR . 'database.sqlite';
        File::copy($databasePath, $outputPath);

        return [
            'status' => 'ok',
            'path' => $outputPath,
            'archive_name' => 'database/database.sqlite',
            'message' => 'File database SQLite berhasil disalin.',
        ];
    }

    private function dumpMysqlDatabase(string $temporaryDirectory): array
    {
        $connection = config('database.connections.mysql');
        $outputPath = $temporaryDirectory . DIRECTORY_SEPARATOR . 'database.sql';
        $binary = $this->resolveMysqlDumpBinary();

        $process = new Process([
            $binary,
            '--host=' . ($connection['host'] ?? '127.0.0.1'),
            '--port=' . ($connection['port'] ?? '3306'),
            '--user=' . ($connection['username'] ?? ''),
            '--single-transaction',
            '--skip-lock-tables',
            '--routines',
            '--triggers',
            '--events',
            $connection['database'] ?? '',
        ], base_path(), [
            'MYSQL_PWD' => $connection['password'] ?? '',
        ]);

        $process->setTimeout(300);
        $process->run();

        if (!$process->isSuccessful()) {
            return [
                'status' => 'warning',
                'warning' => "Backup database MySQL gagal dibuat otomatis menggunakan binary `{$binary}`. Pastikan `mysqldump` tersedia di server atau isi `BACKUP_MYSQLDUMP_BINARY` di .env.",
            ];
        }

        File::put($outputPath, $process->getOutput());

        return [
            'status' => 'ok',
            'path' => $outputPath,
            'archive_name' => 'database/database.sql',
            'message' => 'Dump database MySQL berhasil dibuat.',
        ];
    }

    private function dumpPgsqlDatabase(string $temporaryDirectory): array
    {
        $connection = config('database.connections.pgsql');
        $outputPath = $temporaryDirectory . DIRECTORY_SEPARATOR . 'database.sql';
        $binary = $this->resolvePgsqlDumpBinary();

        $process = new Process([
            $binary,
            '--host=' . ($connection['host'] ?? '127.0.0.1'),
            '--port=' . ($connection['port'] ?? '5432'),
            '--username=' . ($connection['username'] ?? ''),
            '--format=plain',
            '--no-owner',
            '--no-privileges',
            $connection['database'] ?? '',
        ], base_path(), [
            'PGPASSWORD' => $connection['password'] ?? '',
        ]);

        $process->setTimeout(300);
        $process->run();

        if (!$process->isSuccessful()) {
            return [
                'status' => 'warning',
                'warning' => "Backup database PostgreSQL gagal dibuat otomatis menggunakan binary `{$binary}`. Pastikan `pg_dump` tersedia di server atau isi `BACKUP_PG_DUMP_BINARY` di .env.",
            ];
        }

        File::put($outputPath, $process->getOutput());

        return [
            'status' => 'ok',
            'path' => $outputPath,
            'archive_name' => 'database/database.sql',
            'message' => 'Dump database PostgreSQL berhasil dibuat.',
        ];
    }

    private function addDirectoryToZip(ZipArchive $zip, string $sourcePath, string $archivePrefix): void
    {
        if (!File::isDirectory($sourcePath)) {
            return;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($sourcePath, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            $realPath = $item->getRealPath();
            $relativePath = ltrim(str_replace($sourcePath, '', $realPath), DIRECTORY_SEPARATOR);
            $archivePath = $archivePrefix . ($relativePath !== '' ? '/' . str_replace(DIRECTORY_SEPARATOR, '/', $relativePath) : '');

            if ($item->isDir()) {
                $zip->addEmptyDir($archivePath);
                continue;
            }

            $zip->addFile($realPath, $archivePath);
        }
    }

    private function pruneOldBackups(string $backupDirectory, int $keep): void
    {
        $keep = max($keep, 1);

        $backupFiles = collect(File::files($backupDirectory))
            ->filter(fn ($file) => str_ends_with($file->getFilename(), '.zip'))
            ->sortByDesc(fn ($file) => $file->getMTime())
            ->values();

        $backupFiles
            ->slice($keep)
            ->each(fn ($file) => File::delete($file->getPathname()));
    }

    private function shouldIncludeEnv(): bool
    {
        return $this->option('include-env') || (bool) config('backup.include_env', false);
    }

    private function resolveBackupDirectory(): string
    {
        $configuredPath = trim((string) config('backup.path', 'storage/app/backups'));

        if ($configuredPath === '') {
            return storage_path('app/backups');
        }

        return $this->normalizePath(
            $this->isAbsolutePath($configuredPath)
                ? $configuredPath
                : base_path($configuredPath)
        );
    }

    private function resolveMysqlDumpBinary(): string
    {
        return $this->resolveBinary(
            (string) config('backup.mysql_dump_binary', ''),
            [
                'C:\\xampp\\mysql\\bin\\mysqldump.exe',
                'C:\\Program Files\\MySQL\\MySQL Server 8.0\\bin\\mysqldump.exe',
                '/usr/bin/mysqldump',
                '/usr/local/bin/mysqldump',
            ],
            'mysqldump'
        );
    }

    private function resolvePgsqlDumpBinary(): string
    {
        return $this->resolveBinary(
            (string) config('backup.pgsql_dump_binary', ''),
            [
                'C:\\Program Files\\PostgreSQL\\16\\bin\\pg_dump.exe',
                'C:\\Program Files\\PostgreSQL\\15\\bin\\pg_dump.exe',
                '/usr/bin/pg_dump',
                '/usr/local/bin/pg_dump',
            ],
            'pg_dump'
        );
    }

    private function resolveBinary(string $configured, array $candidates, string $fallback): string
    {
        $configured = trim($configured);
        if ($configured !== '') {
            return $configured;
        }

        foreach ($candidates as $candidate) {
            if (File::exists($candidate)) {
                return $candidate;
            }
        }

        return $fallback;
    }

    private function isAbsolutePath(string $path): bool
    {
        return str_starts_with($path, DIRECTORY_SEPARATOR)
            || str_starts_with($path, '\\\\')
            || preg_match('/^[A-Za-z]:[\\\\\\/]/', $path) === 1;
    }

    private function normalizePath(string $path): string
    {
        return rtrim($path, "\\/");
    }

    private function buildTemporaryZipPath(string $timestamp): string
    {
        return $this->normalizePath(sys_get_temp_dir()) . DIRECTORY_SEPARATOR . "schedulo-backup-{$timestamp}.zip";
    }

    private function buildTemporaryDirectory(string $timestamp): string
    {
        return $this->normalizePath(sys_get_temp_dir()) . DIRECTORY_SEPARATOR . "schedulo-backup-tmp-{$timestamp}";
    }

    private function cleanupTemporaryDirectory(string $temporaryDirectory): void
    {
        if (!File::exists($temporaryDirectory)) {
            return;
        }

        for ($attempt = 0; $attempt < 3; $attempt++) {
            File::deleteDirectory($temporaryDirectory);

            if (!File::exists($temporaryDirectory)) {
                return;
            }

            usleep(250000);
        }

        if (File::isDirectory($temporaryDirectory)) {
            File::cleanDirectory($temporaryDirectory);
            File::deleteDirectory($temporaryDirectory);
        }
    }
}
