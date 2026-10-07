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
        $this->waitABit();
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
        $this->waitABit();
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
        $this->waitABit();
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
        $this->waitABit();
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
        $this->waitABit();
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
        $this->waitABit();
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
        $this->waitABit();
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
        $this->waitABit();
        $elem = $this->filter('#reaction-button-child1-👍');
        $this->assertSame('reaction', $elem->attr('class'));
        $this->assertSame('👍 3', $elem->text(''));
        $this->resetDb();
    }

    protected function getDetailUrl(): string {
        return "{$this->getTargetUrl()}/termine/7";
    }
}
