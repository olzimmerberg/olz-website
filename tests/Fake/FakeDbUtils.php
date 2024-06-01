<?php

declare(strict_types=1);

namespace Olz\Tests\Fake;

use Doctrine\ORM\EntityManager;
use Olz\Utils\DbUtils;

class FakeDbUtils extends DbUtils {
    public function getDb(): \mysqli|FakeMysqli {
        return new FakeMysqli();
    }

    public function getEntityManager(): EntityManager {
        // TODO: Provide fake
        $this->log()->warning("Entity manager access in unit tests!");
        return parent::getEntityManager();
    }
}
