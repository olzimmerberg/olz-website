<?php

declare(strict_types=1);

namespace Olz\Tests\Fake;

use Olz\Utils\DbUtils;
use Olz\Utils\WithUtilsTrait;

class FakeDbUtils extends DbUtils {
    public function getDb() {
        return new FakeMysqli();
    }

    public function getEntityManager() {
        // TODO: Provide fake
        $this->log()->warning("Entity manager access in unit tests!");
        return parent::getEntityManager();
    }
}

class FakeMysqli {
    use WithUtilsTrait;

    public function query($sql) {
        $this->log()->info("DB: {$sql}");
        return new FakeMysqliResult();
    }
}

class FakeMysqliResult {
    public function fetch_assoc() {
        return null;
    }
}
