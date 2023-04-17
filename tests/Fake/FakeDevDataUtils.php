<?php

declare(strict_types=1);

namespace Olz\Tests\Fake;

class FakeDevDataUtils {
    public $commands_called = [];

    public function fullResetDb() {
        $this->commands_called[] = 'fullResetDb';
    }

    public function resetDbStructure() {
        $this->commands_called[] = 'resetDbStructure';
    }

    public function resetDbContent() {
        $this->commands_called[] = 'resetDbContent';
    }

    public function dumpDb() {
        $this->commands_called[] = 'dumpDb';
    }

    public function migrateTo($version = 'latest') {
        $this->commands_called[] = ['migrateTo', $version];
    }

    public function getDbBackup($key) {
        $this->commands_called[] = ['getDbBackup', $key];
    }
}
