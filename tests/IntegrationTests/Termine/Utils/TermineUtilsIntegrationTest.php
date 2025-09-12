<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests\Termine\Utils;

use Olz\Termine\Utils\TermineUtils;
use Olz\Tests\IntegrationTests\Common\IntegrationTestCase;

/**
 * @internal
 *
 * @covers \Olz\Termine\Utils\TermineUtils
 */
final class TermineUtilsIntegrationTest extends IntegrationTestCase {
    public function testFromEnv(): void {
        $utils = $this->getSut()->loadTypeOptions();
        $this->assertFalse($utils->isValidFilter([
            'typ' => 'alle',
            'datum' => '2015',
        ]));
        $this->assertTrue($utils->isValidFilter([
            'typ' => 'alle',
            'datum' => '2016',
        ]));
        $this->assertTrue($utils->isValidFilter([
            'typ' => 'alle',
            'datum' => '2017',
        ]));
        $this->assertTrue($utils->isValidFilter([
            'typ' => 'alle',
            'datum' => '2018',
        ]));
        $this->assertTrue($utils->isValidFilter([
            'typ' => 'alle',
            'datum' => '2019',
        ]));
        $this->assertTrue($utils->isValidFilter([
            'typ' => 'alle',
            'datum' => '2020',
        ]));
        $this->assertTrue($utils->isValidFilter([
            'typ' => 'alle',
            'datum' => '2021',
        ]));
        $this->assertFalse($utils->isValidFilter([
            'typ' => 'alle',
            'datum' => '2022',
        ]));
    }

    protected function getSut(): TermineUtils {
        self::bootKernel();
        // @phpstan-ignore-next-line
        return self::getContainer()->get(TermineUtils::class);
    }
}
