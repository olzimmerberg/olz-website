<?php

declare(strict_types=1);

namespace Olz\Tests\SystemTests;

use Olz\Tests\SystemTests\Common\OnlyInModes;
use Olz\Tests\SystemTests\Common\SystemTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class NewsTest extends SystemTestCase {
    #[OnlyInModes(['prod'])]
    public function testNewsProd(): void {
        $this->loadUrl($this->getUrl());

        $this->click('#filter-format-aktuell');
        $last_year = intval(date('Y')) - 1;
        $this->click("#filter-date-{$last_year}");
        $this->click('.olz-posting-list-item:nth-of-type(2) .link');
        $this->waitFor('#news-reactions .olz-editable-reactions');
        $this->waitABit();
        $this->waitFor('#reaction-button-undefined-👍');
        $this->assertMatchesRegularExpression("/Aktuell/", $this->filter('#format-info')->getText());
    }

    #[OnlyInModes(['dev_rw', 'staging_rw', 'dev', 'staging'])]
    public function testNewsReadOnly(): void {
        $this->loadUrl($this->getUrl());
        $this->screenshot('news');
        $this->loadUrl("{$this->getUrl()}/3");
        $this->waitFor('#news-reactions .olz-editable-reactions');
        $this->screenshot('news_id_3');

        // Reactions (read-only)
        $this->waitABit();
        $this->waitFor('#reaction-button-undefined-👍');
        $crawler = $this->getCrawler();
        $inline_elem = $crawler->filter('a[href="#react-%F0%9F%91%8D"]');
        $elem = $crawler->filter('#reaction-button-undefined-👍');
        $this->assertNull($inline_elem->attr('class'));
        $this->assertSame('Anmeldung👍 4', $inline_elem->text(''));
        $this->assertSame('reaction', $elem->attr('class'));
        $this->assertSame('👍 4', $elem->text(''));
        $this->assertCount(0, $crawler->filter('#add-reaction-button'));
    }

    protected function getUrl(): string {
        return "{$this->getTargetUrl()}/news";
    }
}
