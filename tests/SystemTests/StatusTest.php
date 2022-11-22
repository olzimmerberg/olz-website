<?php

declare(strict_types=1);

namespace Olz\Tests\SystemTests;

use Olz\Tests\SystemTests\Common\SystemTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class StatusTest extends SystemTestCase {
    public static $statusDomain = "status.olzimmerberg.ch";
    public static $statusUrl = "https://status.olzimmerberg.ch/";

    public static $statusUsername = "olz_system_test";
    public static $statusPassword = "jup,thisIsPublic";

    public function testStatusIsUp(): void {
        $url = "{$this::$statusUrl}";
        $headers = $this->getHeaders($url);

        $this->assertSame(200, $headers['http_code']);
        $this->assertSame(0, $headers['ssl_verify_result']);
    }

    public function testStatusIsWorking(): void {
        $url = "{$this::$statusUrl}";
        $body = file_get_contents($url);

        $this->assertMatchesRegularExpression('/Login/i', $body);
        $this->assertMatchesRegularExpression('/Server Monitor/i', $body);
    }

    public function testStatusIsMonitoring(): void {
        $browser = $this->getBrowser('firefox');
        $browser->get("{$this::$statusUrl}");
        $username_input = $this->findBrowserElement('#input-username');
        $username_input->sendKeys($this::$statusUsername);
        $password_input = $this->findBrowserElement('#input-password');
        $password_input->sendKeys($this::$statusPassword);
        $login_button = $this->findBrowserElement('button[type="submit"]');
        $login_button->click();
        $browser->get("{$this::$statusUrl}?&mod=server");
        $prod_check = $this->findBrowserElement('a[href="https://olzimmerberg.ch"]');
        $this->assertNotNull($prod_check);
        $prod_backup = $this->findBrowserElement('a[href="https://olzimmerberg.ch/tools.php/backup-monitoring"]');
        $this->assertNotNull($prod_backup);
        $prod_logs = $this->findBrowserElement('a[href="https://olzimmerberg.ch/tools.php/logs-monitoring"]');
        $this->assertNotNull($prod_logs);
        $some_view_link = $this->findBrowserElement('a[href*="action=view&id="]');
        $this->assertNotNull($some_view_link);
        $some_view_href = $some_view_link->getAttribute('href');
        $escaped_status_url = preg_quote($this::$statusUrl, '/');
        $this->assertMatchesRegularExpression("/^{$escaped_status_url}/", $some_view_href);
        $browser->get($some_view_href);
        $body = $this->findBrowserElement('body');
        $res = preg_match('/Last check:\s*([0-9]+)\s+(seconds|minutes)\s+ago/im', $body->getText(), $matches);
        $this->assertSame(1, $res, $body->getText());
        $number = intval($matches[1]);
        $unit = $matches[2];
        if ($unit === 'minutes') {
            $this->assertLessThanOrEqual(15, $number);
        } elseif ($unit === 'seconds') {
            $this->assertLessThanOrEqual(15 * 60, $number);
        } else {
            throw new \Exception("Invalid unit: {$unit}");
        }
    }

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
