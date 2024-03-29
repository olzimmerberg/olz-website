<?php

declare(strict_types=1);

namespace Olz\Tests\Fake;

use Olz\Utils\DevDataUtils;

class FakeDevDataUtils extends DevDataUtils {
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
        return 'fake output';
    }

    public function getDbBackup($key) {
        $this->commands_called[] = ['getDbBackup', $key];
    }
}
