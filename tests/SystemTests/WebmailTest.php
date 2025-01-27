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
final class WebmailTest extends SystemTestCase {
    public static string $webmailUrl = "https://webmail.olzimmerberg.ch/";

    #[OnlyInModes(['meta'])]
    public function testWebmailIsWorking(): void {
        $url = "{$this::$webmailUrl}";
        $body = file_get_contents($url);

        $this->assertMatchesRegularExpression('/Roundcube Webmail/i', $body);
        $this->assertMatchesRegularExpression('/Login/i', $body);
    }
}
