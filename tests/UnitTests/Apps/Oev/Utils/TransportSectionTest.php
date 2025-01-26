<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Apps\Oev\Utils;

use Olz\Apps\Oev\Utils\TransportSection;
use Olz\Tests\UnitTests\Common\UnitTestCase;

/**
 * @internal
 *
 * @covers \Olz\Apps\Oev\Utils\TransportSection
 */
final class TransportSectionTest extends UnitTestCase {
    public const SAMPLE_API_SECTION = [
        'journey' => [
            'passList' => [
                TransportHaltTest::SAMPLE_API_HALT,
            ],
        ],
        'walk' => ['duration' => 120],
        'departure' => TransportHaltTest::SAMPLE_API_HALT_DEPARTURE,
        'arrival' => TransportHaltTest::SAMPLE_API_HALT_ARRIVAL,
    ];

    public function testTransportSection(): void {
        $object = TransportSection::fromTransportApi(self::SAMPLE_API_SECTION);

        $departure = $object->getDeparture();
        $this->assertSame('16', $departure->getStationId());
        $this->assertSame('Station-sous-test', $departure->getStationName());
        $this->assertSame(1652789574, $departure->getTimeSeconds());
        $this->assertSame('2022-05-17 14:12:54', $departure->getTimeString());

        $pass_list = $object->getPassList();
        $this->assertCount(1, $pass_list);
        $this->assertSame('14', $pass_list[0]->getStationId());
        $this->assertSame('Testingen', $pass_list[0]->getStationName());
        $this->assertSame(1652791574, $pass_list[0]->getTimeSeconds());
        $this->assertSame('2022-05-17 14:46:14', $pass_list[0]->getTimeString());

        $arrival = $object->getArrival();
        $this->assertSame('15', $arrival->getStationId());
        $this->assertSame('Testwil', $arrival->getStationName());
        $this->assertSame(1652792593, $arrival->getTimeSeconds());
        $this->assertSame('2022-05-17 15:03:13', $arrival->getTimeString());

        $this->assertSame(['16', '14', '15'], array_map(function ($halt) {
            return $halt->getStationId();
        }, $object->getHalts()));

        $this->assertSame([
            'departure' => [
                'stationId' => '16',
                'stationName' => 'Station-sous-test',
                'time' => '2022-05-17 14:12:54',
            ],
            'arrival' => [
                'stationId' => '15',
                'stationName' => 'Testwil',
                'time' => '2022-05-17 15:03:13',
            ],
            'passList' => [
                [
                    'stationId' => '14',
                    'stationName' => 'Testingen',
                    'time' => '2022-05-17 14:46:14',
                ],
            ],
            'isWalk' => false,
        ], $object->getFieldValue());
    }
}
