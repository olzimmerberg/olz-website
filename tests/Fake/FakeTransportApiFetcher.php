<?php

declare(strict_types=1);

namespace Olz\Tests\Fake;

use Olz\Fetchers\TransportApiFetcher;

class FakeTransportApiFetcher extends TransportApiFetcher {
    use FakeFetcherTrait;
}
