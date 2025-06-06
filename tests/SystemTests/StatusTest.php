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
final class StatusTest extends SystemTestCase {
    public static string $statusDomain = "status.olzimmerberg.ch";
    public static string $statusUrl = "https://status.olzimmerberg.ch/";

    public static string $statusUsername = "olz_system_test";
    public static string $statusPassword = "jup,thisIsPublic";

    #[OnlyInModes(['meta'])]
    public function testStatusIsUp(): void {
        $url = "{$this::$statusUrl}";
        $headers = $this->getHeaders($url);

        $this->assertSame(200, $headers['http_code']);
        $this->assertSame(0, $headers['ssl_verify_result']);
    }

    #[OnlyInModes(['meta'])]
    public function testStatusIsWorking(): void {
        $url = "{$this::$statusUrl}";
        $body = file_get_contents($url) ?: '';

        $this->assertMatchesRegularExpression('/Login/i', $body);
        $this->assertMatchesRegularExpression('/Server Monitor/i', $body);
    }

    #[OnlyInModes(['meta'])]
    public function testStatusIsMonitoring(): void {
        $browser = $this->getBrowser();
        $browser->get("{$this::$statusUrl}");
        $username_input = $this->findBrowserElement('#input-username');
        $username_input->sendKeys($this::$statusUsername);
        $password_input = $this->findBrowserElement('#input-password');
        $password_input->sendKeys($this::$statusPassword);
        $login_button = $this->findBrowserElement('button[type="submit"]');
        $login_button->click();
        $browser->get("{$this::$statusUrl}?&mod=server");
        $prod_check = $this->getBrowserElement('a[href="https://olzimmerberg.ch"]');
        $this->assertNotNull($prod_check);
        $prod_backup = $this->getBrowserElement('a[href*="monitor-backup"]');
        $this->assertNotNull($prod_backup);
        $prod_logs = $this->getBrowserElement('a[href*="monitor-logs"]');
        $this->assertNotNull($prod_logs);
        $some_view_link = $this->getBrowserElement('a[href*="action=view&id="]');
        $this->assertNotNull($some_view_link);
        $some_view_href = strval($some_view_link->getAttribute('href'));
        $escaped_status_url = preg_quote($this::$statusUrl, '/');
        $this->assertMatchesRegularExpression("/^{$escaped_status_url}/", $some_view_href);
        $browser->get($some_view_href);
        $body = $this->findBrowserElement('body');
        $last_check = $this->parseLastCheck($body->getText());
        $this->assertNotNull($last_check, $body->getText());
        $this->assertLessThanOrEqual(15 * 60, $last_check);
    }

    #[OnlyInModes(['meta'])]
    protected function parseLastCheck(string $text): ?int {
        $res = preg_match('/Last check:\s*(([0-9]+) (seconds|minutes) ago|about a minute ago)/im', $text, $matches);
        if (!$res) {
            return null;
        }
        if ($matches[1] === 'about a minute ago') {
            return 60;
        }
        if ($matches[1] === 'about an hour ago') {
            return 3600;
        }
        if ($matches[1] === 'about a ady ago') {
            return 86400;
        }
        $number = intval($matches[2] ?? 0);
        $unit = $matches[3] ?? null;
        if ($unit === 'minutes') {
            return $number * 60;
        }
        if ($unit === 'seconds') {
            return $number;
        }
        throw new \Exception("Invalid unit: {$unit}");
    }

    #[OnlyInModes(['meta'])]
    public function testHttpGetsRedirected(): void {
        $url = "http://{$this::$statusDomain}/";
        $headers = $this->getHeaders($url);

        $this->assertSame(301, $headers['http_code']);
        $this->assertSame(0, $headers['ssl_verify_result']);
        $this->assertSame(
            "https://{$this::$statusDomain}/",
            $headers['redirect_url']
        );
    }
}
