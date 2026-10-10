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
final class TerminReactionsTest extends SystemTestCase {
    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testTerminReactionUnauthorized(): void {
        $this->loadUrl($this->getDetailUrl());
        $this->waitFor('#reaction-button-undefined-👍');
        $crawler = $this->getCrawler();
        $elem = $crawler->filter('#reaction-button-undefined-👍');
        $this->assertSame('reaction', $elem->attr('class'));
        $this->assertSame('#login-dialog', $elem->attr('href'));
        $this->assertCount(0, $crawler->filter('#add-reaction-button'));
        $this->click('#reaction-button-undefined-👍');
        $this->waitForModal('#login-modal');
    }

    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testTerminReactionAddButton(): void {
        $this->login('benutzer', 'b3nu723r');
        $this->loadUrl($this->getDetailUrl());
        $this->waitFor('#reaction-button-benutzer-😢');
        $elem = $this->filter('#reaction-button-benutzer-😢');
        $this->assertSame('reaction', $elem->attr('class'));
        $this->assertSame('😢 0', $elem->text(''));
        $this->click('#reaction-button-benutzer-😢');
        $this->waitForReactionClass('#reaction-button-benutzer-😢', 'reaction active');
        $elem = $this->filter('#reaction-button-benutzer-😢');
        $this->assertSame('reaction active', $elem->attr('class'));
        $this->assertSame('😢 1', $elem->text(''));
        $this->resetDb();
    }

    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testTerminReactionRemoveButton(): void {
        $this->login('benutzer', 'b3nu723r');
        $this->loadUrl($this->getDetailUrl());
        $this->waitFor('#reaction-button-benutzer-🔵');
        $elem = $this->filter('#reaction-button-benutzer-🔵');
        $this->assertSame('reaction active', $elem->attr('class'));
        $this->assertSame('🔵 2', $elem->text(''));
        $this->click('#reaction-button-benutzer-🔵');
        $this->waitForReactionClass('#reaction-button-benutzer-🔵', 'reaction');
        $elem = $this->filter('#reaction-button-benutzer-🔵');
        $this->assertSame('reaction', $elem->attr('class'));
        $this->assertSame('🔵 1', $elem->text(''));
        $this->resetDb();
    }

    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testTerminReactionAddCustom(): void {
        $this->login('benutzer', 'b3nu723r');
        $this->loadUrl($this->getDetailUrl());
        $this->assertCount(0, $this->filter('#reaction-button-benutzer-🐦‍🔥'));
        $this->click('#add-reaction-button');
        $this->waitForModal('#emoji-modal');
        $this->sendKeys('#emoji-input', '🐦‍🔥');
        $this->click('#submit-button');
        $this->waitForReactionClass('#reaction-button-benutzer-🐦‍🔥', 'reaction active');
        $elem = $this->filter('#reaction-button-benutzer-🐦‍🔥');
        $this->assertSame('reaction active', $elem->attr('class'));
        $this->assertSame('🐦‍🔥 1', $elem->text(''));
        $this->resetDb();
    }

    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testTerminReactionRemoveCustom(): void {
        $this->login('benutzer', 'b3nu723r');
        $this->loadUrl($this->getDetailUrl());
        $this->waitFor('#reaction-button-benutzer-💑');
        $elem = $this->filter('#reaction-button-benutzer-💑');
        $this->assertSame('reaction active', $elem->attr('class'));
        $this->assertSame('💑 1', $elem->text(''));
        $this->click('#reaction-button-benutzer-💑');
        $this->waitUntilGone('#reaction-button-benutzer-💑');
        $this->assertCount(0, $this->filter('#reaction-button-benutzer-💑'));
        $this->resetDb();
    }

    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testTerminReactionAddInline(): void {
        $this->login('benutzer', 'b3nu723r');
        $this->loadUrl($this->getDetailUrl());
        $this->waitABit();
        $this->waitFor('#reaction-button-benutzer-🟢');
        $crawler = $this->getCrawler();
        $inline_elem = $crawler->filter('a[href="#react-%F0%9F%9F%A2"]');
        $elem = $crawler->filter('#reaction-button-benutzer-🟢');
        $this->assertFalse((bool) $inline_elem->attr('class'));
        $this->assertSame('reaction', $elem->attr('class'));
        $this->assertSame('🟢 1', $elem->text(''));
        $this->click('a[href="#react-%F0%9F%9F%A2"]');
        $this->waitForReactionClass('a[href="#react-%F0%9F%9F%A2"]', 'active');
        $this->waitForReactionClass('#reaction-button-benutzer-🟢', 'reaction active');
        $crawler = $this->getCrawler();
        $inline_elem = $crawler->filter('a[href="#react-%F0%9F%9F%A2"]');
        $elem = $crawler->filter('#reaction-button-benutzer-🟢');
        $this->assertSame('active', $inline_elem->attr('class'));
        $this->assertSame('reaction active', $elem->attr('class'));
        $this->assertSame('🟢 2', $elem->text(''));
        $this->resetDb();
    }

    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testTerminReactionRemoveInline(): void {
        $this->login('benutzer', 'b3nu723r');
        $this->loadUrl($this->getDetailUrl());
        $this->waitFor('#reaction-button-benutzer-👍');
        $crawler = $this->getCrawler();
        $inline_elem = $crawler->filter('a[href="#react-%F0%9F%91%8D"]');
        $elem = $crawler->filter('#reaction-button-benutzer-👍');
        $this->assertSame('active', $inline_elem->attr('class'));
        $this->assertSame('reaction active', $elem->attr('class'));
        $this->assertSame('👍 4', $elem->text(''));
        $this->click('a[href="#react-%F0%9F%91%8D"]');
        $this->waitForReactionClass('a[href="#react-%F0%9F%91%8D"]', '');
        $this->waitForReactionClass('#reaction-button-benutzer-👍', 'reaction');
        $crawler = $this->getCrawler();
        $inline_elem = $crawler->filter('a[href="#react-%F0%9F%91%8D"]');
        $elem = $crawler->filter('#reaction-button-benutzer-👍');
        $this->assertSame('', $inline_elem->attr('class'));
        $this->assertSame('reaction', $elem->attr('class'));
        $this->assertSame('👍 3', $elem->text(''));
        $this->resetDb();
    }

    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testTerminReactionChildAddButton(): void {
        $this->login('parent', 'par3n7');
        $this->loadUrl($this->getDetailUrl());
        $this->waitFor('#reaction-button-child2-🙏');
        $elem = $this->filter('#reaction-button-child2-🙏');
        $this->assertSame('reaction', $elem->attr('class'));
        $this->assertSame('🙏 0', $elem->text(''));
        $this->click('#reaction-button-child2-🙏');
        $this->waitForReactionClass('#reaction-button-child2-🙏', 'reaction active');
        $elem = $this->filter('#reaction-button-child2-🙏');
        $this->assertSame('reaction active', $elem->attr('class'));
        $this->assertSame('🙏 1', $elem->text(''));
        $this->resetDb();
    }

    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testTerminReactionChildRemoveButton(): void {
        $this->login('parent', 'par3n7');
        $this->loadUrl($this->getDetailUrl());
        $this->waitFor('#reaction-button-child1-👍');
        $elem = $this->filter('#reaction-button-child1-👍');
        $this->assertSame('reaction active', $elem->attr('class'));
        $this->assertSame('👍 4', $elem->text(''));
        $this->click('#reaction-button-child1-👍');
        $this->waitForReactionClass('#reaction-button-child1-👍', 'reaction');
        $elem = $this->filter('#reaction-button-child1-👍');
        $this->assertSame('reaction', $elem->attr('class'));
        $this->assertSame('👍 3', $elem->text(''));
        $this->resetDb();
    }

    protected function getDetailUrl(): string {
        return "{$this->getTargetUrl()}/termine/7";
    }

    /**
     * Waits until the element matching the given CSS selector has exactly the
     * expected class attribute. A reaction toggle is sent to the server
     * asynchronously, so the DOM must be polled instead of waiting a fixed
     * amount of time, in order to avoid flaky tests.
     */
    private function waitForReactionClass(string $selector, string $expected_class): void {
        $this->waitUntil(function () use ($selector, $expected_class) {
            $elements = $this->filter($selector);
            return $elements->count() === 1 && $elements->attr('class') === $expected_class;
        });
    }
}
