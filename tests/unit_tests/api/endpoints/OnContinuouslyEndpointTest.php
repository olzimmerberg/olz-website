<?php

declare(strict_types=1);

use Monolog\Logger;
use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../../../../src/api/endpoints/OnContinuouslyEndpoint.php';
require_once __DIR__.'/../../../../src/config/vendor/autoload.php';
require_once __DIR__.'/../../../../src/utils/date/FixedDateUtils.php';

class FakeOnContinuouslyEndpointServerConfig {
    public function getCronAuthenticityCode() {
        return 'some-token';
    }
}

/**
 * @internal
 * @covers \OnContinuouslyEndpoint
 */
final class OnContinuouslyEndpointTest extends TestCase {
    public function testOnContinuouslyEndpointIdent(): void {
        $endpoint = new OnContinuouslyEndpoint();
        $this->assertSame('OnContinuouslyEndpoint', $endpoint->getIdent());
    }

    public function testOnContinuouslyEndpointParseInput(): void {
        global $_GET;
        $_GET = ['authenticityCode' => 'some-token'];
        $endpoint = new OnContinuouslyEndpoint();
        $parsed_input = $endpoint->parseInput();
        $this->assertSame([
            'authenticityCode' => 'some-token',
        ], $parsed_input);
    }

    public function testOnContinuouslyEndpointWrongToken(): void {
        $logger = new Logger('OnContinuouslyEndpointTest');
        $endpoint = new OnContinuouslyEndpoint();
        $endpoint->setLogger($logger);
        $endpoint->setDateUtils(new FixedDateUtils('2020-03-13 19:30:00'));
        $endpoint->setServerConfig(new FakeOnContinuouslyEndpointServerConfig());

        try {
            $result = $endpoint->call([
                'authenticityCode' => 'wrong-token',
            ]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame(403, $err->getCode());
        }
    }

    public function testOnContinuouslyEndpoint(): void {
        $logger = new Logger('OnContinuouslyEndpointTest');
        $endpoint = new OnContinuouslyEndpoint();
        $endpoint->setLogger($logger);
        $endpoint->setDateUtils(new FixedDateUtils('2020-03-13 19:30:00'));
        $endpoint->setServerConfig(new FakeOnContinuouslyEndpointServerConfig());

        $result = $endpoint->call([
            'authenticityCode' => 'some-token',
        ]);

        $this->assertSame([], $result);
    }
}
