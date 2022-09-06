<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Apps\Oev\Utils;

use Olz\Apps\Oev\Utils\TransportHalt;
use Olz\Tests\UnitTests\Common\UnitTestCase;

/**
 * @internal
 *
 * @covers \Olz\Apps\Oev\Utils\TransportHalt
 */
final class TransportHaltTest extends UnitTestCase {
    public const SAMPLE_API_HALT = [
        'station' => [
            'id' => 14,
            'name' => 'Testingen',
        ],
        'departureTimestamp' => 1652791574,
        'arrivalTimestamp' => 1652790593,
    ];

    public const SAMPLE_API_HALT_ARRIVAL = [
        'station' => [
            'id' => 15,
            'name' => 'Testwil',
        ],
        'arrivalTimestamp' => 1652792593,
    ];

    public const SAMPLE_API_HALT_DEPARTURE = [
        'station' => [
            'id' => 16,
            'name' => 'Station-sous-test',
        ],
        'departureTimestamp' => 1652789574,
    ];

    public const SAMPLE_API_HALT_NEITHER = [
        'station' => [
            'id' => 17,
            'name' => 'Testberg',
        ],
    ];

    public function testGetField(): void {
        $field = TransportHalt::getField();
        $this->assertSame(
            'OlzTransportHalt',
            $field->getTypeScriptType(),
        );
        $this->assertSame(
            [
                'OlzTransportHalt' => "{\n    'stationId': string,\n    'stationName': string,\n    'time': string,\n}",
            ],
            $field->getExportedTypeScriptTypes(),
        );
    }

    public function testTransportHalt(): void {
        $object = TransportHalt::fromTransportApi(self::SAMPLE_API_HALT);
        $this->assertSame(14, $object->getStationId());
        $this->assertSame('Testingen', $object->getStationName());
        $this->assertSame(1652791574, $object->getTimeSeconds());
        $this->assertSame('2022-05-17 14:46:14', $object->getTimeString());

        $this->assertSame([
            'stationId' => 14,
            'stationName' => 'Testingen',
            'time' => '2022-05-17 14:46:14',
        ], $object->getFieldValue());
    }
}
