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
        $this->loadUrl("{$this::$statusUrl}");
        $this->filter('#input-username')->sendKeys($this::$statusUsername);
        $this->filter('#input-password')->sendKeys($this::$statusPassword);
        $this->filter('button[type="submit"]')->click();
        $this->loadUrl("{$this::$statusUrl}?&mod=server");
        $crawler = $this->getCrawler();
        $this->assertCount(1, $crawler->filter('a[href="https://olzimmerberg.ch"]'));
        $this->assertCount(1, $crawler->filter('a[href*="monitor-backup"]'));
        $this->assertCount(1, $crawler->filter('a[href*="monitor-logs"]'));
        $some_view_link = $crawler->filter('a[href*="action=view&id="]');
        $this->assertCount(1, $some_view_link);
        $some_view_href = $some_view_link->attr('href') ?? '';
        $escaped_status_url = preg_quote($this::$statusUrl, '/');
        $this->assertMatchesRegularExpression("/^{$escaped_status_url}/", $some_view_href);
        $this->loadUrl($some_view_href);
        $body = $this->filter('body');
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
