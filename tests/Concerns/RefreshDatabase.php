<?php

namespace Tests\Concerns;

use Illuminate\Foundation\Testing\RefreshDatabase as BaseRefreshDatabase;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Illuminate\Support\Facades\DB;

trait RefreshDatabase
{
    use BaseRefreshDatabase {
        refreshTestDatabase as baseRefreshTestDatabase;
    }

    protected function refreshTestDatabase()
    {
        if ($this->hasReadyTestingSchema()) {
            RefreshDatabaseState::$migrated = true;
            $this->beginDatabaseTransaction();

            return;
        }

        $this->baseRefreshTestDatabase();
    }

    protected function hasReadyTestingSchema(): bool
    {
        try {
            $schema = DB::connection()->getSchemaBuilder();

            return $schema->hasTable('migrations')
                && $schema->hasTable('users')
                && $schema->hasTable('activities')
                && $schema->hasTable('divisions')
                && $schema->hasTable('positions')
                && $schema->hasTable('staff');
        } catch (\Throwable) {
            return false;
        }
    }
}
