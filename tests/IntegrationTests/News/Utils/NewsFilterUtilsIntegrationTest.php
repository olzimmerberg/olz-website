<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests\News\Utils;

use Olz\News\Utils\NewsFilterUtils;
use Olz\Tests\IntegrationTests\Common\IntegrationTestCase;

/**
 * @internal
 *
 * @covers \Olz\News\Utils\NewsFilterUtils
 */
final class NewsFilterUtilsIntegrationTest extends IntegrationTestCase {
    public function testFromEnv(): void {
        $news_utils = NewsFilterUtils::fromEnv();
        $this->assertSame(false, $news_utils->isValidFilter([
            'format' => 'alle',
            'datum' => '2015',
            'archiv' => 'ohne',
        ]));
        $this->assertSame(true, $news_utils->isValidFilter([
            'format' => 'alle',
            'datum' => '2016',
            'archiv' => 'ohne',
        ]));
        $this->assertSame(true, $news_utils->isValidFilter([
            'format' => 'alle',
            'datum' => '2017',
            'archiv' => 'ohne',
        ]));
        $this->assertSame(true, $news_utils->isValidFilter([
            'format' => 'alle',
            'datum' => '2018',
            'archiv' => 'ohne',
        ]));
        $this->assertSame(true, $news_utils->isValidFilter([
            'format' => 'alle',
            'datum' => '2019',
            'archiv' => 'ohne',
        ]));
        $this->assertSame(true, $news_utils->isValidFilter([
            'format' => 'alle',
            'datum' => '2020',
            'archiv' => 'ohne',
        ]));
        $this->assertSame(false, $news_utils->isValidFilter([
            'format' => 'alle',
            'datum' => '2021',
            'archiv' => 'ohne',
        ]));

        $this->assertSame(false, $news_utils->isValidFilter([
            'format' => 'alle',
            'datum' => '2005',
            'archiv' => 'mit',
        ]));
        $this->assertSame(true, $news_utils->isValidFilter([
            'format' => 'alle',
            'datum' => '2006',
            'archiv' => 'mit',
        ]));
        $this->assertSame(true, $news_utils->isValidFilter([
            'format' => 'alle',
            'datum' => '2020',
            'archiv' => 'mit',
        ]));
        $this->assertSame(false, $news_utils->isValidFilter([
            'format' => 'alle',
            'datum' => '2021',
            'archiv' => 'mit',
        ]));
    }
}
