<?php

declare(strict_types=1);

require_once __DIR__.'/../../../src/utils/date/FixedDateUtils.php';
require_once __DIR__.'/../../../src/utils/NewsUtils.php';
require_once __DIR__.'/../common/IntegrationTestCase.php';

/**
 * @internal
 * @covers \NewsUtils
 */
final class NewsUtilsIntegrationTest extends IntegrationTestCase {
    public function testFromEnv(): void {
        $news_utils = NewsUtils::fromEnv();
        $this->assertSame(false, $news_utils->isValidFilter(['typ' => 'alle', 'datum' => '2015']));
        $this->assertSame(true, $news_utils->isValidFilter(['typ' => 'alle', 'datum' => '2016']));
        $this->assertSame(true, $news_utils->isValidFilter(['typ' => 'alle', 'datum' => '2017']));
        $this->assertSame(true, $news_utils->isValidFilter(['typ' => 'alle', 'datum' => '2018']));
        $this->assertSame(true, $news_utils->isValidFilter(['typ' => 'alle', 'datum' => '2019']));
        $this->assertSame(true, $news_utils->isValidFilter(['typ' => 'alle', 'datum' => '2020']));
        $this->assertSame(false, $news_utils->isValidFilter(['typ' => 'alle', 'datum' => '2021']));
    }
}
