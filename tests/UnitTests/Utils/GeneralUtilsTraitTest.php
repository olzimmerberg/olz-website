<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\GeneralUtils;
use Olz\Utils\GeneralUtilsTrait;

class GeneralUtilsTraitConcreteUtils {
    use GeneralUtilsTrait;

    public function testOnlyGeneralUtils(): GeneralUtils {
        return $this->generalUtils();
    }
}

/**
 * @internal
 *
 * @covers \Olz\Utils\GeneralUtilsTrait
 */
final class GeneralUtilsTraitTest extends UnitTestCase {
    public function testSetGetGeneralUtils(): void {
        $utils = new GeneralUtilsTraitConcreteUtils();
        $fake = $this->createMock(GeneralUtils::class);
        $utils->setGeneralUtils($fake);
        $this->assertSame($fake, $utils->testOnlyGeneralUtils());
    }
}
