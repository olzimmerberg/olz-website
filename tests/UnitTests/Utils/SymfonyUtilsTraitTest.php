<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\SymfonyUtils;
use Olz\Utils\SymfonyUtilsTrait;

class SymfonyUtilsTraitConcreteUtils {
    use SymfonyUtilsTrait;

    public function testOnlySymfonyUtils(): SymfonyUtils {
        return $this->symfonyUtils();
    }
}

/**
 * @internal
 *
 * @covers \Olz\Utils\SymfonyUtilsTrait
 */
final class SymfonyUtilsTraitTest extends UnitTestCase {
    public function testSetGetSymfonyUtils(): void {
        $utils = new SymfonyUtilsTraitConcreteUtils();
        $fake = $this->createMock(SymfonyUtils::class);
        $utils->setSymfonyUtils($fake);
        $this->assertSame($fake, $utils->testOnlySymfonyUtils());
    }
}
