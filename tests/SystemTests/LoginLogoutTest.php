<?php

declare(strict_types=1);

namespace Olz\Tests\SystemTests;

use Olz\Common\Utils\AuthUtils;
use Olz\Tests\SystemTests\Common\OnlyInModes;
use Olz\Tests\SystemTests\Common\SystemTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class LoginLogoutTest extends SystemTestCase {
    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testLoginLogoutScreenshots(): void {
        $this->loadUrl($this->getUrl());

        $this->click('#account-menu-link');
        $this->click('#login-menu-item');
        $this->waitForModal('#login-modal');

        $this->screenshot('login_modal');

        $this->sendKeys('#login-modal #username', 'admin');
        $this->sendKeys('#login-modal #current-password', 'adm1n');
        $this->click('#login-modal #submit-button');
        $this->waitUntilGone('#login-modal');

        $this->loadUrl($this->getUrl());

        $this->click('#account-menu-link');
        $this->screenshot('logout_account_menu');

        $this->click('#logout-menu-item');
        $this->waitABit();

        $this->loadUrl($this->getUrl());

        $this->click('#account-menu-link');
        $this->screenshot('login_account_menu');
        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testSessionRefreshOnPageLoad(): void {
        // Log in through the UI. This sets both the session cookie and the
        // (long-lived) refresh token cookie.
        $this->loadUrl($this->getUrl());
        $this->click('#account-menu-link');
        $this->click('#login-menu-item');
        $this->waitForModal('#login-modal');
        $this->sendKeys('#login-modal #username', 'admin');
        $this->sendKeys('#login-modal #current-password', 'adm1n');
        $this->click('#login-modal #submit-button');
        $this->waitUntilGone('#login-modal');
        $this->loadUrl($this->getUrl());
        $this->assertSame('admin', $this->getRenderedUsername());

        // Simulate an expired / lost session by removing the session cookie,
        // while keeping the refresh token cookie. The cookies are HttpOnly, so
        // JavaScript cannot delete them; the privileged WebDriver API can.
        $this->deleteCookiesExcept(AuthUtils::REFRESH_TOKEN_COOKIE);
        $this->loadUrl($this->getUrl());
        // The initial page load must already be rendered as logged in, because
        // the session is restored from the refresh token.
        $this->assertSame('admin', $this->getRenderedUsername());
        $this->screenshot('session_refresh_restored');

        // Without the refresh token, the session cannot be restored.
        $this->getClient()->getWebDriver()->manage()->deleteAllCookies();
        $this->loadUrl($this->getUrl());
        $this->assertNull($this->getRenderedUsername());
        $this->screenshot('session_refresh_logged_out');
    }

    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testSessionRefreshWhileEditing(): void {
        $this->login('admin', 'adm1n');
        $this->loadUrl("{$this->getTargetUrl()}/service");
        $this->assertSame([
            'SOLV',
            'GO2OL',
            'Online-Trainings',
        ], $this->filter('.olz-links li')->each(
            fn ($node) => $node->text()
        ));

        // Open an edit dialog and change something...
        $this->click('#edit-link-1-button');
        $this->waitForModal('#edit-link-modal');
        $this->clear('#edit-link-modal #name-input');
        $this->sendKeys('#edit-link-modal #name-input', 'SOLV updated');

        // ...but the session expires in the meantime. Only the session cookie is
        // removed, the refresh token cookie is kept.
        $this->deleteCookiesExcept(AuthUtils::REFRESH_TOKEN_COOKIE);

        // Saving must transparently refresh the session and retry the request.
        $this->click('#edit-link-modal #submit-button');
        $this->waitUntilGone('#edit-link-modal');
        $this->loadUrl("{$this->getTargetUrl()}/service");
        $this->assertSame([
            'SOLV updated',
            'GO2OL',
            'Online-Trainings',
        ], $this->filter('.olz-links li')->each(
            fn ($node) => $node->text()
        ));
        $this->screenshot('session_refresh_while_editing');

        $this->resetDb();
    }

    /**
     * Returns the username of the server-rendered user (`window.olzUser`), or
     * null if the page was rendered for a guest.
     */
    private function getRenderedUsername(): mixed {
        return $this->getClient()->executeScript(
            'return (window.olzUser && window.olzUser.username) || null;',
            [],
        );
    }

    /** Deletes all cookies (including HttpOnly ones) except the given one. */
    private function deleteCookiesExcept(string $keep_name): void {
        $manage = $this->getClient()->getWebDriver()->manage();
        foreach ($manage->getCookies() as $cookie) {
            if ($cookie->getName() !== $keep_name) {
                $manage->deleteCookieNamed($cookie->getName());
            }
        }
    }

    protected function getUrl(): string {
        return "{$this->getTargetUrl()}/";
    }
}
