<?php

declare(strict_types=1);

require_once __DIR__.'/../../../src/utils/date/FixedDateUtils.php';
require_once __DIR__.'/../../../src/utils/TermineUtils.php';
require_once __DIR__.'/../common/IntegrationTestCase.php';

/**
 * @internal
 * @covers \TermineUtils
 */
final class TermineUtilsIntegrationTest extends IntegrationTestCase {
    public function testFromEnv(): void {
        $termine_utils = TermineUtils::fromEnv();
        $this->assertSame(false, $termine_utils->isValidFilter([
            'typ' => 'alle',
            'datum' => '2015',
            'archiv' => 'ohne',
        ]));
        $this->assertSame(true, $termine_utils->isValidFilter([
            'typ' => 'alle',
            'datum' => '2016',
            'archiv' => 'ohne',
        ]));
        $this->assertSame(true, $termine_utils->isValidFilter([
            'typ' => 'alle',
            'datum' => '2017',
            'archiv' => 'ohne',
        ]));
        $this->assertSame(true, $termine_utils->isValidFilter([
            'typ' => 'alle',
            'datum' => '2018',
            'archiv' => 'ohne',
        ]));
        $this->assertSame(true, $termine_utils->isValidFilter([
            'typ' => 'alle',
            'datum' => '2019',
            'archiv' => 'ohne',
        ]));
        $this->assertSame(true, $termine_utils->isValidFilter([
            'typ' => 'alle',
            'datum' => '2020',
            'archiv' => 'ohne',
        ]));
        $this->assertSame(true, $termine_utils->isValidFilter([
            'typ' => 'alle',
            'datum' => '2021',
            'archiv' => 'ohne',
        ]));
        $this->assertSame(false, $termine_utils->isValidFilter([
            'typ' => 'alle',
            'datum' => '2022',
            'archiv' => 'ohne',
        ]));

        $this->assertSame(false, $termine_utils->isValidFilter([
            'typ' => 'alle',
            'datum' => '2005',
            'archiv' => 'mit',
        ]));
        $this->assertSame(true, $termine_utils->isValidFilter([
            'typ' => 'alle',
            'datum' => '2006',
            'archiv' => 'mit',
        ]));
        $this->assertSame(true, $termine_utils->isValidFilter([
            'typ' => 'alle',
            'datum' => '2021',
            'archiv' => 'mit',
        ]));
        $this->assertSame(false, $termine_utils->isValidFilter([
            'typ' => 'alle',
            'datum' => '2022',
            'archiv' => 'mit',
        ]));
    }
}
