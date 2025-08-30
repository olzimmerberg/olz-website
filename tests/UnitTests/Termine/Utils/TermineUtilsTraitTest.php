<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Termine\Utils;

use Olz\Termine\Utils\TermineUtils;
use Olz\Termine\Utils\TermineUtilsTrait;
use Olz\Tests\UnitTests\Common\UnitTestCase;

class TermineUtilsTraitConcreteUtils {
    use TermineUtilsTrait;

    public function testOnlyTermineUtils(): TermineUtils {
        return $this->TermineUtils();
    }
}

/**
 * @internal
 *
 * @covers \Olz\Termine\Utils\TermineUtilsTrait
 */
final class TermineUtilsTraitTest extends UnitTestCase {
    public function testSetGetTermineUtils(): void {
        $utils = new TermineUtilsTraitConcreteUtils();
        $fake = $this->createMock(TermineUtils::class);
        $utils->setTermineUtils($fake);
        $this->assertSame($fake, $utils->testOnlyTermineUtils());
    }
}
