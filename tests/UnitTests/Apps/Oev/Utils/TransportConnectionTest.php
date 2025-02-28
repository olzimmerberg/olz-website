<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Apps\Oev\Utils;

use Olz\Apps\Oev\Utils\TransportConnection;
use Olz\Tests\UnitTests\Common\UnitTestCase;

/**
 * @internal
 *
 * @covers \Olz\Apps\Oev\Utils\TransportConnection
 */
final class TransportConnectionTest extends UnitTestCase {
    public const SAMPLE_API_CONNECTION = [
        'sections' => [
            TransportSectionTest::SAMPLE_API_SECTION,
        ],
    ];

    public function testFromTransportApi(): void {
        $object = TransportConnection::fromTransportApi(self::SAMPLE_API_CONNECTION);
        $this->assertSame([['16', '14', '15']], array_map(function ($section) {
            return array_map(function ($halt) {
                return $halt->getStationId();
            }, $section->getHalts());
        }, $object->getSections()));

        $this->assertSame([
            'sections' => [
                [
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
                ],
            ],
        ], $object->getFieldValue());
    }

    public function testIsSuperConnectionOf(): void {
        $super_connection = TransportConnection::fromTransportApi(
            [
                'sections' => [
                    // just has the section twice
                    TransportSectionTest::SAMPLE_API_SECTION,
                    TransportSectionTest::SAMPLE_API_SECTION,
                ],
            ]
        );
        $sub_connection = TransportConnection::fromTransportApi(self::SAMPLE_API_CONNECTION);

        $this->assertTrue($super_connection->isSuperConnectionOf($sub_connection));
        $this->assertTrue($super_connection->isSuperConnectionOf($super_connection));
        $this->assertTrue($sub_connection->isSuperConnectionOf($sub_connection));
        $this->assertFalse($sub_connection->isSuperConnectionOf($super_connection));
    }
}
