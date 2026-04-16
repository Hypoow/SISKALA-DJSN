<?php

namespace App\Support;

use RuntimeException;

class ProductionCommandGuard
{
    public function __construct(
        private readonly array $destructiveCommands,
        private readonly bool $blockInProduction = true,
        private readonly bool $allowDestructiveCommands = false,
    ) {
    }

    public static function fromConfig(): self
    {
        return new self(
            destructiveCommands: (array) config('data_safety.destructive_commands', []),
            blockInProduction: (bool) config('data_safety.block_destructive_commands_in_production', true),
            allowDestructiveCommands: (bool) config('data_safety.allow_destructive_commands', false),
        );
    }

    public function ensureAllowed(?string $command, ?string $environment = null): void
    {
        if (!$this->shouldBlock($command, $environment)) {
            return;
        }

        $command ??= 'unknown';

        throw new RuntimeException(
            "Perintah artisan `{$command}` diblokir di environment production untuk melindungi data. " .
            "Jika memang diperlukan saat maintenance terkontrol, set `ALLOW_DESTRUCTIVE_COMMANDS=true` sementara."
        );
    }

    public function shouldBlock(?string $command, ?string $environment = null): bool
    {
        if (!$this->blockInProduction || $this->allowDestructiveCommands) {
            return false;
        }

        if (trim((string) $environment) !== 'production') {
            return false;
        }

        return in_array(trim((string) $command), $this->destructiveCommands, true);
    }
}
