<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests\Utils;

use Olz\Entity\Users\User;
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
    public function testDbUtilsGetDb(): void {
        $utils = $this->getSut();
        $db = $utils->getDb();

        $result = $db->query('SELECT username FROM users WHERE id=1');
        assert(!is_bool($result));
        $row = $result->fetch_assoc();
        $this->assertNull($db->connect_error);
        $this->assertSame(0, $db->connect_errno);
        $this->assertSame('admin', $row['username'] ?? null);
    }

    public function testDbUtilsGetEntityManager(): void {
        $utils = $this->getSut();
        $entityManager = $utils->getEntityManager();

        $user_repo = $entityManager->getRepository(User::class);
        $user1 = $user_repo->findOneBy(['id' => 1]);
        $this->assertSame('admin', $user1?->getUsername());
    }

    protected function getSut(): FakeIntegrationTestDbUtils {
        self::bootKernel();
        // @phpstan-ignore-next-line
        return self::getContainer()->get(FakeIntegrationTestDbUtils::class);
    }
}
