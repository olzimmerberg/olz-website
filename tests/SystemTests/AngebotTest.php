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
        $this->loadUrl($this->getUrl());
        $this->screenshot('angebot');

        if ($this->isInModes(['dev_rw', 'dev'])) {
            $role_mailto_link = $this->filter('#role-mailto a');
            $this->assertSame("#", $role_mailto_link->attr('href'));
            $this->assertSame(
                "return olz.initOlzRoleInfoModal(18)",
                $role_mailto_link->attr('onclick')
            );
            $this->assertSame("Ressort Karten", $role_mailto_link->text(''));

            $role_direct_link = $this->filter('#role-direct a');
            $this->assertSame("#", $role_direct_link->attr('href'));
            $this->assertSame(
                "return olz.initOlzRoleInfoModal(18)",
                $role_direct_link->attr('onclick')
            );
            $this->assertSame("Kartenverkauf", $role_direct_link->text(''));

            $user_mailto_link = $this->filter('#user-mailto a');
            $this->assertSame("#", $user_mailto_link->attr('href'));
            $this->assertMatchesRegularExpression(
                "/^return olz\\.initOlzEmailModal\\(\\/\\*test\\-flaky\\(\\*\\/\"[A-Za-z0-9]+\"\\/\\*\\)test\\-flaky\\*\\/\\)$/",
                $user_mailto_link->attr('onclick') ?? ''
            );
            $this->assertSame("Karen Karten", $user_mailto_link->text(''));

            $user_direct_link = $this->filter('#user-direct a');
            $this->assertSame("#", $user_direct_link->attr('href'));
            $this->assertMatchesRegularExpression(
                "/^return olz\\.initOlzEmailModal\\(\\/\\*test\\-flaky\\(\\*\\/\"[A-Za-z0-9]+\"\\/\\*\\)test\\-flaky\\*\\/\\)$/",
                $user_direct_link->attr('onclick') ?? ''
            );
            $this->assertSame("E-Mail", $user_direct_link->text(''));
        } else {
            // TODO: Dummy assert
            $this->assertDirectoryExists(__DIR__);
        }
    }

    protected function getUrl(): string {
        return "{$this->getTargetUrl()}/angebot";
    }
}
