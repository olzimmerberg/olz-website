<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Doctrine\ORM\EntityManager;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\DbUtils;

class TestOnlyDbUtils extends DbUtils {
    public static function testOnlySetDb(\mysqli $new_db): void {
        self::$db = $new_db;
    }

    public static function testOnlySetEntityManager(EntityManager $new_em): void {
        self::$entityManager = $new_em;
    }
}

/**
 * @internal
 *
 * @covers \Olz\Utils\DbUtils
 */
final class DbUtilsTest extends UnitTestCase {
    public function testDbUtilsGetDb(): void {
        // There's not much to test in unit tests without an actual DB...

        $db_utils = new TestOnlyDbUtils();

        try {
            $db_utils->getEntityManager();
            $this->fail('Error expected');
        } catch (\Throwable $th) {
            $this->assertSame('DbUtils.php:*** No entityManager', $th->getMessage());
        }
        $fake_em = $this->createMock(EntityManager::class);
        TestOnlyDbUtils::testOnlySetEntityManager($fake_em);
        $this->assertSame($fake_em, $db_utils->getEntityManager());
    }
}
