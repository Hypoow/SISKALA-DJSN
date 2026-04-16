<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Block Destructive Console Commands In Production
    |--------------------------------------------------------------------------
    |
    | When enabled, dangerous artisan commands that can drop or rebuild schema
    | are blocked in production unless explicitly unlocked.
    |
    */
    'block_destructive_commands_in_production' => filter_var(
        env('DATA_SAFETY_BLOCK_DESTRUCTIVE_COMMANDS', true),
        FILTER_VALIDATE_BOOL
    ),

    /*
    |--------------------------------------------------------------------------
    | Temporary Override
    |--------------------------------------------------------------------------
    |
    | Set to true only for controlled maintenance windows when destructive
    | schema commands must be executed on production intentionally.
    |
    */
    'allow_destructive_commands' => filter_var(
        env('ALLOW_DESTRUCTIVE_COMMANDS', false),
        FILTER_VALIDATE_BOOL
    ),

    /*
    |--------------------------------------------------------------------------
    | Guarded Artisan Commands
    |--------------------------------------------------------------------------
    */
    'destructive_commands' => [
        'migrate:fresh',
        'migrate:refresh',
        'migrate:reset',
        'migrate:rollback',
        'db:wipe',
    ],
];
