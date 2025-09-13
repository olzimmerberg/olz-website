<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests\News\Utils;

use Olz\News\Utils\NewsUtils;
use Olz\Tests\IntegrationTests\Common\IntegrationTestCase;

/**
 * @internal
 *
 * @covers \Olz\News\Utils\NewsUtils
 */
final class NewsUtilsIntegrationTest extends IntegrationTestCase {
    public function testFromEnv(): void {
        $utils = $this->getSut();
        $this->assertFalse($utils->isValidFilter([
            'format' => 'alle',
            'datum' => '2015',
        ]));
        $this->assertTrue($utils->isValidFilter([
            'format' => 'alle',
            'datum' => '2016',
        ]));
        $this->assertTrue($utils->isValidFilter([
            'format' => 'alle',
            'datum' => '2017',
        ]));
        $this->assertTrue($utils->isValidFilter([
            'format' => 'alle',
            'datum' => '2018',
        ]));
        $this->assertTrue($utils->isValidFilter([
            'format' => 'alle',
            'datum' => '2019',
        ]));
        $this->assertTrue($utils->isValidFilter([
            'format' => 'alle',
            'datum' => '2020',
        ]));
        $this->assertFalse($utils->isValidFilter([
            'format' => 'alle',
            'datum' => '2021',
        ]));
    }

    protected function getSut(): NewsUtils {
        self::bootKernel();
        // @phpstan-ignore-next-line
        return self::getContainer()->get(NewsUtils::class);
    }
}
