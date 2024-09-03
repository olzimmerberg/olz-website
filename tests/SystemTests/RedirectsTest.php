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
final class RedirectsTest extends SystemTestCase {
    #[OnlyInModes(['dev', 'dev_rw'])]
    public function testRedirects(): void {
        $this->assertRedirects("/_/index.php", "/index.php");
        // $this->assertRedirects("/index.php", "/"); // Strangely, this is broken
        $this->assertRedirects("/_/startseite.php", "/startseite.php");
        $this->assertRedirects("/startseite.php", "/");
        $this->assertRedirects("/_/aktuell.php", "/aktuell.php");
        $this->assertRedirects("/aktuell.php/index.php", "/aktuell.php");
        $this->assertRedirects("/_/aktuell.php/index.php", "/aktuell.php");
        $this->assertRedirects("/_/", "/");
        $this->assertRedirects("/_/screenshots", "/_/screenshots/");
        $this->assertRedirects("/termine/orte", "/termin_orte");
        $this->assertRedirects("/termine/orte/1", "/termin_orte/chilbiplatz_thalwil");
        $this->assertRedirects("/termine/vorlagen", "/termin_vorlagen");
        $this->assertRedirects("/termine/vorlagen/1", "/termin_vorlagen/1");
    }

    protected function assertRedirects(string $source_path, string $destination_path): void {
        $headers = $this->getHeaders("{$this->getTargetUrl()}{$source_path}");
        $this->assertSame(
            300,
            intval($headers['http_code'] / 100) * 100,
            "{$source_path} => {$destination_path}",
        );
        $this->assertSame(
            "{$this->getTargetUrl()}{$destination_path}",
            $headers['redirect_url'],
            "{$source_path} => {$destination_path}",
        );
    }
}
