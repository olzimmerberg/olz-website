<?php

declare(strict_types=1);

namespace Olz\Tests\SystemTests;

use Olz\Tests\SystemTests\Common\SystemTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class WebmailTest extends SystemTestCase {
    public static $webmailUrl = "https://webmail.olzimmerberg.ch/";

    public function testWebmalIsWorking(): void {
        $url = "{$this::$webmailUrl}";
        $body = file_get_contents($url);

        $this->assertMatchesRegularExpression('/Roundcube Webmail/i', $body);
        $this->assertMatchesRegularExpression('/Login/i', $body);
    }
}
