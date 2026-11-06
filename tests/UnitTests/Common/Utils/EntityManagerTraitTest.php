<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Common\Utils;

use Doctrine\ORM\EntityManagerInterface;
use Olz\Common\Utils\EntityManagerTrait;
use Olz\Tests\UnitTests\Common\UnitTestCase;

class EntityManagerTraitConcreteUtils {
    use EntityManagerTrait;

    public function testOnlyEntityManager(): EntityManagerInterface {
        return $this->entityManager();
    }
}

/**
 * @internal
 *
 * @covers \Olz\Common\Utils\EntityManagerTrait
 */
final class EntityManagerTraitTest extends UnitTestCase {
    public function testSetGetEntityManager(): void {
        $utils = new EntityManagerTraitConcreteUtils();
        $fake = $this->createMock(EntityManagerInterface::class);
        $utils->setEntityManager($fake);
        $this->assertSame($fake, $utils->testOnlyEntityManager());
    }
}
