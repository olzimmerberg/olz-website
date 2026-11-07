<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Common\Utils;

use Olz\Common\Utils\GeneralUtils;
use Olz\Common\Utils\GeneralUtilsTrait;
use Olz\Tests\UnitTests\Common\UnitTestCase;

class GeneralUtilsTraitConcreteUtils {
    use GeneralUtilsTrait;

    public function testOnlyGeneralUtils(): GeneralUtils {
        return $this->generalUtils();
    }
}

/**
 * @internal
 *
 * @covers \Olz\Common\Utils\GeneralUtilsTrait
 */
final class GeneralUtilsTraitTest extends UnitTestCase {
    public function testSetGetGeneralUtils(): void {
        $utils = new GeneralUtilsTraitConcreteUtils();
        $fake = $this->createMock(GeneralUtils::class);
        $utils->setGeneralUtils($fake);
        $this->assertSame($fake, $utils->testOnlyGeneralUtils());
    }
}
