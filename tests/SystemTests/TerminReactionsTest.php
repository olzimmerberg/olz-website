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
        $elem = $this->getBrowserElement('#reaction-button-undefined-👍');
        $this->assertSame('reaction', $elem?->getAttribute('class'));
        $this->assertSame('#login-dialog', $elem->getAttribute('href'));
        $this->assertNull($this->getBrowserElement('#add-reaction-button'));
        $this->click('#reaction-button-undefined-👍');
        $this->waitForModal('#login-modal');
    }

    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testTerminReactionAddButton(): void {
        $this->login('benutzer', 'b3nu723r');
        $this->loadUrl($this->getDetailUrl());
        $elem = $this->getBrowserElement('#reaction-button-benutzer-😢');
        $this->assertSame('reaction', $elem?->getAttribute('class'));
        $this->assertSame('😢 0', $elem->getText());
        $this->click('#reaction-button-benutzer-😢');
        $this->waitABit();
        $this->assertSame('reaction active', $elem->getAttribute('class'));
        $this->assertSame('😢 1', $elem->getText());
        $this->resetDb();
    }

    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testTerminReactionRemoveButton(): void {
        $this->login('benutzer', 'b3nu723r');
        $this->loadUrl($this->getDetailUrl());
        $elem = $this->getBrowserElement('#reaction-button-benutzer-🔵');
        $this->assertSame('reaction active', $elem?->getAttribute('class'));
        $this->assertSame('🔵 2', $elem->getText());
        $this->click('#reaction-button-benutzer-🔵');
        $this->waitABit();
        $this->assertSame('reaction', $elem->getAttribute('class'));
        $this->assertSame('🔵 1', $elem->getText());
        $this->resetDb();
    }

    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testTerminReactionAddCustom(): void {
        $this->login('benutzer', 'b3nu723r');
        $this->loadUrl($this->getDetailUrl());
        $this->assertNull($this->getBrowserElement('#reaction-button-benutzer-🐦‍🔥'));
        $this->click('#add-reaction-button');
        $this->waitForModal('#emoji-modal');
        $this->sendKeys('#emoji-input', '🐦‍🔥');
        $this->click('#submit-button');
        $this->waitABit();
        $elem = $this->getBrowserElement('#reaction-button-benutzer-🐦‍🔥');
        $this->assertSame('reaction active', $elem?->getAttribute('class'));
        $this->assertSame('🐦‍🔥 1', $elem->getText());
        $this->resetDb();
    }

    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testTerminReactionRemoveCustom(): void {
        $this->login('benutzer', 'b3nu723r');
        $this->loadUrl($this->getDetailUrl());
        $elem = $this->getBrowserElement('#reaction-button-benutzer-💑');
        $this->assertSame('reaction active', $elem?->getAttribute('class'));
        $this->assertSame('💑 1', $elem->getText());
        $this->click('#reaction-button-benutzer-💑');
        $this->waitABit();
        $this->assertNull($this->getBrowserElement('#reaction-button-benutzer-💑'));
        $this->resetDb();
    }

    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testTerminReactionAddInline(): void {
        $this->login('benutzer', 'b3nu723r');
        $this->loadUrl($this->getDetailUrl());
        $this->waitABit();
        $inline_elem = $this->getBrowserElement('a[href="#react-%F0%9F%9F%A2"]');
        $elem = $this->getBrowserElement('#reaction-button-benutzer-🟢');
        $this->assertFalse((bool) $inline_elem?->getAttribute('class'));
        $this->assertSame('reaction', $elem?->getAttribute('class'));
        $this->assertSame('🟢 1', $elem->getText());
        $this->click('a[href="#react-%F0%9F%9F%A2"]');
        $this->waitABit();
        $this->assertSame('active', $inline_elem?->getAttribute('class'));
        $this->assertSame('reaction active', $elem->getAttribute('class'));
        $this->assertSame('🟢 2', $elem->getText());
        $this->resetDb();
    }

    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testTerminReactionRemoveInline(): void {
        $this->login('benutzer', 'b3nu723r');
        $this->loadUrl($this->getDetailUrl());
        $inline_elem = $this->getBrowserElement('a[href="#react-%F0%9F%91%8D"]');
        $elem = $this->getBrowserElement('#reaction-button-benutzer-👍');
        $this->assertSame('active', $inline_elem?->getAttribute('class'));
        $this->assertSame('reaction active', $elem?->getAttribute('class'));
        $this->assertSame('👍 4', $elem->getText());
        $this->click('a[href="#react-%F0%9F%91%8D"]');
        $this->waitABit();
        $this->assertSame('', $inline_elem->getAttribute('class'));
        $this->assertSame('reaction', $elem->getAttribute('class'));
        $this->assertSame('👍 3', $elem->getText());
        $this->resetDb();
    }

    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testTerminReactionChildAddButton(): void {
        $this->login('parent', 'par3n7');
        $this->loadUrl($this->getDetailUrl());
        $elem = $this->getBrowserElement('#reaction-button-child2-🙏');
        $this->assertSame('reaction', $elem?->getAttribute('class'));
        $this->assertSame('🙏 0', $elem->getText());
        $this->click('#reaction-button-child2-🙏');
        $this->waitABit();
        $this->assertSame('reaction active', $elem->getAttribute('class'));
        $this->assertSame('🙏 1', $elem->getText());
        $this->resetDb();
    }

    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testTerminReactionChildRemoveButton(): void {
        $this->login('parent', 'par3n7');
        $this->loadUrl($this->getDetailUrl());
        $elem = $this->getBrowserElement('#reaction-button-child1-👍');
        $this->assertSame('reaction active', $elem?->getAttribute('class'));
        $this->assertSame('👍 4', $elem->getText());
        $this->click('#reaction-button-child1-👍');
        $this->waitABit();
        $this->assertSame('reaction', $elem->getAttribute('class'));
        $this->assertSame('👍 3', $elem->getText());
        $this->resetDb();
    }

    protected function getDetailUrl(): string {
        return "{$this->getTargetUrl()}/termine/7";
    }
}
