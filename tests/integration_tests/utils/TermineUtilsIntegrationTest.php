<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../../../src/utils/date/FixedDateUtils.php';
require_once __DIR__.'/../../../src/utils/TermineUtils.php';

/**
 * @internal
 * @covers \TermineUtils
 */
final class TermineUtilsIntegrationTest extends TestCase {
    public function testFromEnv(): void {
        $termine_utils = TermineUtils::fromEnv();
        $this->assertSame(false, $termine_utils->isValidFilter(['typ' => 'alle', 'datum' => '2018']));
        $this->assertSame(true, $termine_utils->isValidFilter(['typ' => 'alle', 'datum' => '2019']));
        $this->assertSame(true, $termine_utils->isValidFilter(['typ' => 'alle', 'datum' => '2020']));
        $this->assertSame(true, $termine_utils->isValidFilter(['typ' => 'alle', 'datum' => '2021']));
        $this->assertSame(false, $termine_utils->isValidFilter(['typ' => 'alle', 'datum' => '2022']));
    }
}
