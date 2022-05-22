<?php

declare(strict_types=1);

require_once __DIR__.'/../../../../src/oev/utils/TransportSection.php';
require_once __DIR__.'/../../common/UnitTestCase.php';
require_once __DIR__.'/TransportHaltTest.php';

/**
 * @internal
 * @covers \TransportSection
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

    public function testGetField(): void {
        $field = TransportSection::getField();
        $this->assertSame(
            'OlzTransportSection',
            $field->getTypeScriptType(),
        );
        $this->assertSame(
            [
                'OlzTransportSection' => "{\n    'departure': OlzTransportHalt,\n    'arrival': OlzTransportHalt,\n    'passList': Array<OlzTransportHalt>,\n    'isWalk': boolean,\n}",
                'OlzTransportHalt' => "{\n    'stationId': string,\n    'stationName': string,\n    'time': string,\n}",
            ],
            $field->getExportedTypeScriptTypes(),
        );
    }

    public function testTransportSection(): void {
        $object = TransportSection::parseFromTransportApi(self::SAMPLE_API_SECTION);

        $departure = $object->getDeparture();
        $this->assertSame(16, $departure->getStationId());
        $this->assertSame('Station-sous-test', $departure->getStationName());
        $this->assertSame(1652789574, $departure->getTimeSeconds());
        $this->assertSame('2022-05-17 14:12:54', $departure->getTimeString());

        $pass_list = $object->getPassList();
        $this->assertSame(1, count($pass_list));
        $this->assertSame(14, $pass_list[0]->getStationId());
        $this->assertSame('Testingen', $pass_list[0]->getStationName());
        $this->assertSame(1652791574, $pass_list[0]->getTimeSeconds());
        $this->assertSame('2022-05-17 14:46:14', $pass_list[0]->getTimeString());

        $arrival = $object->getArrival();
        $this->assertSame(15, $arrival->getStationId());
        $this->assertSame('Testwil', $arrival->getStationName());
        $this->assertSame(1652792593, $arrival->getTimeSeconds());
        $this->assertSame('2022-05-17 15:03:13', $arrival->getTimeString());

        $this->assertSame([16, 14, 15], array_map(function ($halt) {
            return $halt->getStationId();
        }, $object->getHalts()));

        $this->assertSame([
            'departure' => [
                'stationId' => 16,
                'stationName' => 'Station-sous-test',
                'time' => null,
            ],
            'arrival' => [
                'stationId' => 15,
                'stationName' => 'Testwil',
                'time' => null,
            ],
            'passList' => [
                [
                    'stationId' => 14,
                    'stationName' => 'Testingen',
                    'time' => null,
                ],
            ],
            'isWalk' => false,
        ], $object->getFieldValue());
    }
}
