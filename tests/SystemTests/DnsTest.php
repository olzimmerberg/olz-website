<?php

declare(strict_types=1);

namespace Olz\Tests\SystemTests;

use Olz\Tests\SystemTests\Common\SystemTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class DnsTest extends SystemTestCase {
    public static string $dnsHostname = "olzimmerberg.ch";

    public function testHasGoogleSiteVerificationRecord(): void {
        $records = dns_get_record("{$this::$dnsHostname}", DNS_TXT);
        $has_google_site_verification = false;
        foreach ($records as $record) {
            if (preg_match('/^google\-site\-verification\=/i', $record['txt'])) {
                $has_google_site_verification = true;
            }
        }
        $this->assertTrue($has_google_site_verification, json_encode($records));
    }
}
