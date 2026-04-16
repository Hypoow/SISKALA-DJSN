<?php

namespace Tests\Unit;

use App\Support\ProductionCommandGuard;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class ProductionCommandGuardTest extends TestCase
{
    public function test_blocks_destructive_commands_in_production(): void
    {
        $guard = new ProductionCommandGuard(
            destructiveCommands: ['migrate:fresh', 'db:wipe'],
            blockInProduction: true,
            allowDestructiveCommands: false,
        );

        $this->assertTrue($guard->shouldBlock('migrate:fresh', 'production'));
        $this->assertFalse($guard->shouldBlock('migrate', 'production'));
        $this->assertFalse($guard->shouldBlock('migrate:fresh', 'local'));
    }

    public function test_override_allows_destructive_commands_temporarily(): void
    {
        $guard = new ProductionCommandGuard(
            destructiveCommands: ['migrate:fresh'],
            blockInProduction: true,
            allowDestructiveCommands: true,
        );

        $this->assertFalse($guard->shouldBlock('migrate:fresh', 'production'));
    }

    public function test_ensure_allowed_throws_helpful_message(): void
    {
        $guard = new ProductionCommandGuard(
            destructiveCommands: ['migrate:fresh'],
            blockInProduction: true,
            allowDestructiveCommands: false,
        );

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('ALLOW_DESTRUCTIVE_COMMANDS=true');

        $guard->ensureAllowed('migrate:fresh', 'production');
    }
}
