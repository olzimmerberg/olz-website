<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Suche\Utils;

use Olz\Suche\Utils\SearchUtils;
use Olz\Suche\Utils\SearchUtilsTrait;
use Olz\Tests\UnitTests\Common\UnitTestCase;

class SearchUtilsTraitConcreteUtils {
    use SearchUtilsTrait;

    public function testOnlySearchUtils(): SearchUtils {
        return $this->SearchUtils();
    }
}

/**
 * @internal
 *
 * @covers \Olz\Suche\Utils\SearchUtilsTrait
 */
final class SearchUtilsTraitTest extends UnitTestCase {
    public function testSetGetSearchUtils(): void {
        $utils = new SearchUtilsTraitConcreteUtils();
        $fake = $this->createMock(SearchUtils::class);
        $utils->setSearchUtils($fake);
        $this->assertSame($fake, $utils->testOnlySearchUtils());
    }
}
