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
final class AngebotTest extends SystemTestCase {
    #[OnlyInModes(['dev_rw', 'staging_rw', 'dev', 'staging', 'prod'])]
    public function testAngebotReadOnly(): void {
        $browser = $this->getBrowser();

        $browser->get($this->getUrl());
        $this->screenshot('angebot');

        $role_mailto_link = $this->getBrowserElement('#role-mailto a');
        $this->assertNotNull($role_mailto_link);
        $this->assertSame("#", strval($role_mailto_link->getAttribute('href')));
        $this->assertSame(
            "return olz.initOlzRoleInfoModal(18)",
            strval($role_mailto_link->getAttribute('onclick'))
        );
        $this->assertSame("Ressort Karten", strval($role_mailto_link->getText()));

        $role_direct_link = $this->getBrowserElement('#role-direct a');
        $this->assertNotNull($role_direct_link);
        $this->assertSame("#", strval($role_direct_link->getAttribute('href')));
        $this->assertSame(
            "return olz.initOlzRoleInfoModal(18)",
            strval($role_direct_link->getAttribute('onclick'))
        );
        $this->assertSame("Kartenverkauf", strval($role_direct_link->getText()));

        $user_mailto_link = $this->getBrowserElement('#user-mailto a');
        $this->assertNotNull($user_mailto_link);
        $this->assertSame("#", strval($user_mailto_link->getAttribute('href')));
        $this->assertMatchesRegularExpression(
            "/^return olz\\.initOlzEmailModal\\(\"[A-Za-z0-9]+\"\\)$/",
            strval($user_mailto_link->getAttribute('onclick'))
        );
        $this->assertSame("Karen Karten", strval($user_mailto_link->getText()));

        $user_direct_link = $this->getBrowserElement('#user-direct a');
        $this->assertNotNull($user_direct_link);
        $this->assertSame("#", strval($user_direct_link->getAttribute('href')));
        $this->assertMatchesRegularExpression(
            "/^return olz\\.initOlzEmailModal\\(\"[A-Za-z0-9]+\"\\)$/",
            strval($user_direct_link->getAttribute('onclick'))
        );
        $this->assertSame("E-Mail", strval($user_direct_link->getText()));

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    protected function getUrl(): string {
        return "{$this->getTargetUrl()}/angebot";
    }
}
