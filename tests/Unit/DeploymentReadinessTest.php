<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class DeploymentReadinessTest extends TestCase
{
    public function test_production_composer_dependencies_are_bounded(): void
    {
        $composer = $this->readJson('composer.json');

        foreach ($composer['require'] ?? [] as $package => $constraint) {
            if ($package === 'php') {
                continue;
            }

            $this->assertIsString($constraint, "Constraint for {$package} must be explicit.");
            $this->assertNotSame('*', trim($constraint), "{$package} must not use a wildcard constraint.");
            $this->assertStringNotContainsString('@dev', $constraint, "{$package} must not allow dev stability.");
            $this->assertFalse(
                str_starts_with(trim($constraint), 'dev-'),
                "{$package} must not point directly at a dev branch."
            );
        }
    }

    public function test_deploy_check_script_covers_validation_tests_build_and_cache_smoke_tests(): void
    {
        $composer = $this->readJson('composer.json');
        $deployCheck = $composer['scripts']['deploy:check'] ?? null;

        $this->assertIsArray($deployCheck, 'composer deploy:check must be defined as a multi-step script.');

        foreach ([
            'composer validate --strict',
            'php artisan test',
            'npm run build',
            'php artisan config:cache',
            'php artisan route:cache',
            'php artisan view:cache',
            'php artisan optimize:clear',
        ] as $expectedCommand) {
            $this->assertTrue(
                $this->scriptContains($deployCheck, $expectedCommand),
                "composer deploy:check must run `{$expectedCommand}`."
            );
        }
    }

    public function test_frontend_build_script_is_available(): void
    {
        $package = $this->readJson('package.json');

        $this->assertSame('vite build', $package['scripts']['build'] ?? null);
    }

    /**
     * @return array<string, mixed>
     */
    private function readJson(string $path): array
    {
        $contents = file_get_contents(dirname(__DIR__, 2).DIRECTORY_SEPARATOR.$path);

        $this->assertIsString($contents, "{$path} must be readable.");

        $decoded = json_decode($contents, true);

        $this->assertIsArray($decoded, "{$path} must contain valid JSON.");

        return $decoded;
    }

    /**
     * @param array<int, string> $script
     */
    private function scriptContains(array $script, string $expectedCommand): bool
    {
        foreach ($script as $command) {
            $normalizedCommand = ltrim($command, '@');

            if (str_contains($normalizedCommand, $expectedCommand)) {
                return true;
            }
        }

        return false;
    }
}
