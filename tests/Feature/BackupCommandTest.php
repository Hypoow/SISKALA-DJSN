<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Tests\TestCase;
use ZipArchive;

class BackupCommandTest extends TestCase
{
    public function test_backup_command_creates_zip_in_configured_directory(): void
    {
        $backupDirectory = storage_path('framework/testing/backups-' . Str::uuid());
        $sqliteDirectory = storage_path('framework/testing/database');
        $sqlitePath = $sqliteDirectory . DIRECTORY_SEPARATOR . 'backup-test.sqlite';

        File::ensureDirectoryExists($sqliteDirectory);
        File::put($sqlitePath, 'sqlite-backup-test');

        config()->set('backup.path', $backupDirectory);
        config()->set('backup.keep', 3);
        config()->set('backup.include_env', false);
        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', $sqlitePath);

        Artisan::call('app:backup-data');

        $backupFiles = collect(File::files($backupDirectory))
            ->filter(fn ($file) => str_ends_with($file->getFilename(), '.zip'))
            ->values();

        $this->assertCount(1, $backupFiles);

        $zip = new ZipArchive();
        $opened = $zip->open($backupFiles->first()->getPathname());

        $this->assertTrue($opened === true);
        $this->assertNotFalse($zip->locateName('database/database.sqlite'));
        $this->assertNotFalse($zip->locateName('manifest.json'));
        $zip->close();

        File::deleteDirectory($backupDirectory);
        File::delete($sqlitePath);
    }
}
