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

    public function testHeaders(): void {
        $this->onlyRunInModes('prod');

        $url = "{$this->getTargetUrl()}";
        $headers = $this->getHeaders($url);

        $this->assertSame(0, $headers['ssl_verify_result']);
        $this->assertSame(443, $headers['primary_port']);
        $this->assertSame('https', strtolower($headers['scheme']));
    }

    public function testWwwGetsRedirected(): void {
        $this->onlyRunInModes('prod');

        $url = "https://www.{$this::$prodDomain}/";
        $headers = $this->getHeaders($url);

        $this->assertSame(308, $headers['http_code']);
        $this->assertSame(0, $headers['ssl_verify_result']);
        $this->assertSame(443, $headers['primary_port']);
        $this->assertSame('https', strtolower($headers['scheme']));
        $this->assertSame(
            "https://{$this::$prodDomain}/",
            $headers['redirect_url']
        );
    }

    public function testHttpGetsRedirected(): void {
        $this->onlyRunInModes('prod');

        $url = "http://{$this::$prodDomain}/";
        $headers = $this->getHeaders($url);

        $this->assertSame(301, $headers['http_code']);
        $this->assertSame(0, $headers['ssl_verify_result']);
        $this->assertSame(80, $headers['primary_port']);
        $this->assertSame('http', strtolower($headers['scheme']));
        $this->assertSame(
            "https://{$this::$prodDomain}/",
            $headers['redirect_url']
        );
    }

    public function testHttpWwwGetsRedirected(): void {
        $this->onlyRunInModes('prod');

        $url = "http://www.{$this::$prodDomain}/";
        $headers = $this->getHeaders($url);

        $this->assertSame(301, $headers['http_code']);
        $this->assertSame(0, $headers['ssl_verify_result']);
        $this->assertSame(80, $headers['primary_port']);
        $this->assertSame('http', strtolower($headers['scheme']));
        $this->assertSame(
            "https://www.{$this::$prodDomain}/",
            $headers['redirect_url']
        );
    }
}
