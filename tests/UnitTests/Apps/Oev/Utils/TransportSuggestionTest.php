<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Apps\Oev\Utils;

use Olz\Apps\Oev\Utils\TransportSuggestion;
use Olz\Tests\UnitTests\Common\UnitTestCase;

/**
 * @internal
 *
 * @covers \Olz\Apps\Oev\Utils\TransportSuggestion
 */
final class TransportSuggestionTest extends UnitTestCase {
    public function testTransportSection(): void {
        $transport_suggestion = new TransportSuggestion();
        $this->assertSame(TransportSuggestion::class, get_class($transport_suggestion));
    }
}
