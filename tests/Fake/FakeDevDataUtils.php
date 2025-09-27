<?php

declare(strict_types=1);

namespace Olz\Tests\Fake;

use Olz\Utils\DevDataUtils;

class FakeDevDataUtils extends DevDataUtils {
    /** @var array<mixed> */
    public array $commands_called = [];

    public function fullResetDb(): void {
        $this->commands_called[] = 'fullResetDb';
    }

    public function resetDbStructure(): void {
        $this->commands_called[] = 'resetDbStructure';
    }

    public function resetDbContent(): void {
        $this->commands_called[] = 'resetDbContent';
    }

    public function dumpDb(): void {
        $this->commands_called[] = 'dumpDb';
    }

    public function generateMigration(): string {
        $this->commands_called[] = 'generateMigration';
        return 'fake output';
    }

    public function migrateTo(string $version = 'latest'): string {
        $this->commands_called[] = ['migrateTo', $version];
        return 'fake output';
    }

    public function printDbBackup(string $key): void {
        $this->commands_called[] = ['printDbBackup', $key];
    }
}
