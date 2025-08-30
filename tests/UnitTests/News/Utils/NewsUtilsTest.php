<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\News\Utils;

use Olz\News\Utils\NewsUtils;
use Olz\Tests\UnitTests\Common\UnitTestCase;

/**
 * @internal
 *
 * @covers \Olz\News\Utils\NewsUtils
 */
final class NewsUtilsTest extends UnitTestCase {
    public function testGetValidNewsFormatIcon(): void {
        $news_utils = new NewsUtils();
        $this->assertSame('/_/assets/icns/entry_type_aktuell_20.svg', $news_utils->getNewsFormatIcon('aktuell'));
        $this->assertSame('/_/assets/icns/entry_type_forum_20.svg', $news_utils->getNewsFormatIcon('forum'));
        $this->assertSame('/_/assets/icns/entry_type_gallery_20.svg', $news_utils->getNewsFormatIcon('galerie'));
        $this->assertSame('/_/assets/icns/entry_type_kaderblog_20.svg', $news_utils->getNewsFormatIcon('kaderblog'));
        $this->assertSame('/_/assets/icns/entry_type_movie_20.svg', $news_utils->getNewsFormatIcon('video'));
    }

    public function testGetInvalidNewsFormatIcon(): void {
        $news_utils = new NewsUtils();
        $this->assertNull($news_utils->getNewsFormatIcon(''));
        $this->assertNull($news_utils->getNewsFormatIcon('invalid'));
    }
}
