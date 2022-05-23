<?php

declare(strict_types=1);

require_once __DIR__.'/../../../../public/_/oev/utils/TransportConnection.php';
require_once __DIR__.'/../../common/UnitTestCase.php';
require_once __DIR__.'/TransportSectionTest.php';

/**
 * @internal
 * @covers \TransportConnection
 */
final class TransportConnectionTest extends UnitTestCase {
    public const SAMPLE_API_CONNECTION = [
        'sections' => [
            TransportSectionTest::SAMPLE_API_SECTION,
        ],
    ];

    public function testGetField(): void {
        $field = TransportConnection::getField();
        $this->assertSame(
            'OlzTransportConnection',
            $field->getTypeScriptType(),
        );
        $this->assertSame(
            [
                'OlzTransportConnection' => "{\n    'sections': Array<OlzTransportSection>,\n}",
                'OlzTransportSection' => "{\n    'departure': OlzTransportHalt,\n    'arrival': OlzTransportHalt,\n    'passList': Array<OlzTransportHalt>,\n    'isWalk': boolean,\n}",
                'OlzTransportHalt' => "{\n    'stationId': string,\n    'stationName': string,\n    'time': string,\n}",
            ],
            $field->getExportedTypeScriptTypes(),
        );
    }

    public function testParseFromTransportApi(): void {
        $object = TransportConnection::parseFromTransportApi(self::SAMPLE_API_CONNECTION);
        $sections = $object->getSections();
        $this->assertSame([[16, 14, 15]], array_map(function ($section) {
            return array_map(function ($halt) {
                return $halt->getStationId();
            }, $section->getHalts());
        }, $object->getSections()));

        $this->assertSame([
            'sections' => [
                [
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
                ],
            ],
        ], $object->getFieldValue());
    }
}
