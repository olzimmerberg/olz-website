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
final class DnsTest extends SystemTestCase {
    public static string $dnsHostname = "olzimmerberg.ch";

    #[OnlyInModes(['meta'])]
    public function testHasGoogleSiteVerificationRecord(): void {
        $records = dns_get_record("{$this::$dnsHostname}", DNS_TXT) ?: [];
        $has_google_site_verification = false;
        foreach ($records as $record) {
            if (preg_match('/^google\-site\-verification\=/i', $record['txt'])) {
                $has_google_site_verification = true;
            }
        }
        $message = json_encode($records) ?: '-';
        $this->assertTrue($has_google_site_verification, $message);
    }
}
