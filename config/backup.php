<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Backup Storage Path
    |--------------------------------------------------------------------------
    |
    | Relative paths will be resolved from the project root. Use an absolute
    | path in production, for example "/var/backups/siskala", if you want the
    | backup archives to live outside the deployed project directory.
    |
    */
    'path' => env('BACKUP_PATH', 'storage/app/backups'),

    /*
    |--------------------------------------------------------------------------
    | Backup Retention
    |--------------------------------------------------------------------------
    */
    'keep' => (int) env('BACKUP_KEEP', 14),

    /*
    |--------------------------------------------------------------------------
    | Scheduler Time
    |--------------------------------------------------------------------------
    */
    'schedule_time' => env('BACKUP_SCHEDULE_TIME', '01:00'),

    /*
    |--------------------------------------------------------------------------
    | Include Environment File
    |--------------------------------------------------------------------------
    */
    'include_env' => filter_var(env('BACKUP_INCLUDE_ENV', false), FILTER_VALIDATE_BOOL),

    /*
    |--------------------------------------------------------------------------
    | Database Dump Binaries
    |--------------------------------------------------------------------------
    |
    | Set these explicitly on the server if the binary is not available in PATH.
    |
    */
    'mysql_dump_binary' => env('BACKUP_MYSQLDUMP_BINARY'),
    'pgsql_dump_binary' => env('BACKUP_PG_DUMP_BINARY'),
];
