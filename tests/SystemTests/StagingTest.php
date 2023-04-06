<?php

declare(strict_types=1);

namespace Olz\Tests\SystemTests;

use Olz\Tests\SystemTests\Common\SystemTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class StagingTest extends SystemTestCase {
    public static $stagingDomain = "staging.olzimmerberg.ch";
    public static $stagingUrl = "https://staging.olzimmerberg.ch/";

    public function testStagingIsUp(): void {
        $url = "{$this::$stagingUrl}";
        $headers = $this->getHeaders($url);

        $this->assertSame(200, $headers['http_code']);
        $this->assertSame(0, $headers['ssl_verify_result']);
    }

    public function testStagingIsWorking(): void {
        $url = "{$this::$stagingUrl}";
        $body = file_get_contents($url);

        $this->assertMatchesRegularExpression('/token/i', $body);
    }

    public function testHttpGetsRedirected(): void {
        $url = "http://{$this::$stagingDomain}/";
        $headers = $this->getHeaders($url);

        $this->assertSame(301, $headers['http_code']);
        $this->assertSame(0, $headers['ssl_verify_result']);
        $this->assertSame(
            "https://{$this::$stagingDomain}/",
            $headers['redirect_url']
        );
    }
}
