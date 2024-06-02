<?php

declare(strict_types=1);

namespace Olz\Tests\SystemTests;

use Olz\Tests\SystemTests\Common\SystemTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class ProdTest extends SystemTestCase {
    public static string $prodDomain = "olzimmerberg.ch";
    public static string $prodUrl = "https://olzimmerberg.ch/";

    public function testProdIsUp(): void {
        $url = "{$this::$prodUrl}";
        $headers = $this->getHeaders($url);

        $this->assertSame(200, $headers['http_code']);
        $this->assertSame(0, $headers['ssl_verify_result']);
    }

    public function testProdIsWorking(): void {
        $url = "{$this::$prodUrl}";
        $body = file_get_contents($url);

        $this->assertMatchesRegularExpression(
            '/<title>OL Zimmerberg<\/title>/i',
            $body
        );
        $this->assertMatchesRegularExpression(
            '/Startseite/i',
            $body
        );
    }

    public function testProdLegacyIsWorking(): void {
        $url = "{$this::$prodUrl}startseite.php";
        $body = file_get_contents($url);

        $this->assertMatchesRegularExpression(
            '/<title>OL Zimmerberg<\/title>/i',
            $body
        );
        $this->assertMatchesRegularExpression(
            '/Startseite/i',
            $body
        );
    }

    public function testWwwGetsRedirected(): void {
        $url = "https://www.{$this::$prodDomain}/";
        $headers = $this->getHeaders($url);

        $this->assertSame(308, $headers['http_code']);
        $this->assertSame(0, $headers['ssl_verify_result']);
        $this->assertSame(
            "https://{$this::$prodDomain}/",
            $headers['redirect_url']
        );
    }

    public function testHttpGetsRedirected(): void {
        $url = "http://{$this::$prodDomain}/";
        $headers = $this->getHeaders($url);

        $this->assertSame(301, $headers['http_code']);
        $this->assertSame(0, $headers['ssl_verify_result']);
        $this->assertSame(
            "https://{$this::$prodDomain}/",
            $headers['redirect_url']
        );
    }

    public function testHttpWwwGetsRedirected(): void {
        $url = "http://www.{$this::$prodDomain}/";
        $headers = $this->getHeaders($url);

        $this->assertSame(301, $headers['http_code']);
        $this->assertSame(0, $headers['ssl_verify_result']);
        $this->assertSame(
            "https://www.{$this::$prodDomain}/",
            $headers['redirect_url']
        );
    }
}
