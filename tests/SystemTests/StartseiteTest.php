<?php

declare(strict_types=1);

namespace Olz\Tests\SystemTests;

use Olz\Tests\SystemTests\Common\SystemTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class StartseiteTest extends SystemTestCase {
    public function testStartseiteHeaders(): void {
        $this->onlyRunInModes($this::$readOnlyModes);

        $url = "{$this->getTargetUrl()}";
        $headers = $this->getHeaders($url);

        $this->assertSame(200, $headers['http_code']);
    }

    public function testStartseiteHeadersLegacy(): void {
        $this->onlyRunInModes($this::$readOnlyModes);

        $url = "{$this->getTargetUrl()}/startseite.php";
        $headers = $this->getHeaders($url);

        $this->assertSame(301, $headers['http_code']);
    }

    public function testStartseiteBody(): void {
        $this->onlyRunInModes($this::$readOnlyModes);

        $url = "{$this->getTargetUrl()}";
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

    public function testStartseiteBodyLegacy(): void {
        $this->onlyRunInModes($this::$readOnlyModes);

        $url = "{$this->getTargetUrl()}/startseite.php";
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
}
