<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests\Utils;

use Olz\Entity\User;
use Olz\Tests\IntegrationTests\Common\IntegrationTestCase;
use Olz\Utils\DbUtils;

class FakeIntegrationTestDbUtils extends DbUtils {
    public static function fromEnv(): DbUtils {
        // For this test, clear the "cache" always
        parent::$db = null;
        parent::$entityManager = null;
        return parent::fromEnv();
    }
}

/**
 * @internal
 *
 * @covers \Olz\Utils\DbUtils
 */
final class DbUtilsIntegrationTest extends IntegrationTestCase {
    public function testDbUtilsFromEnv(): void {
        $db_utils = DbUtils::fromEnv();

        $this->assertSame(false, !$db_utils);
    }

    public function testDbUtilsGetDb(): void {
        $db_utils = FakeIntegrationTestDbUtils::fromEnv();

        $db = $db_utils->getDb();

        $result = $db->query('SELECT username FROM users WHERE id=1');
        $row = $result->fetch_assoc();
        $this->assertSame(null, $db->connect_error);
        $this->assertSame(0, $db->connect_errno);
        $this->assertSame('admin', $row['username']);
    }

    public function testDbUtilsGetEntityManager(): void {
        $db_utils = FakeIntegrationTestDbUtils::fromEnv();

        $entityManager = $db_utils->getEntityManager();

        $user_repo = $entityManager->getRepository(User::class);
        $user1 = $user_repo->findOneBy(['id' => 1]);
        $this->assertSame('admin', $user1->getUsername());
    }
}
