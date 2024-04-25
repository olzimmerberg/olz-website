<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Apps\Oev\Endpoints;

use Olz\Apps\Oev\Endpoints\SearchTransportConnectionEndpoint;
use Olz\Apps\Oev\Utils\TransportConnection;
use Olz\Apps\Oev\Utils\TransportSuggestion;
use Olz\Fetchers\TransportApiFetcher;
use Olz\Tests\Fake;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

class FakeSearchTransportConnectionEndpointTransportApiFetcher extends Fake\FakeTransportApiFetcher {
    public function fetchConnection($request_data) {
        $from = str_replace(' ', '_', $request_data['from']);
        $to = str_replace(' ', '_', $request_data['to']);
        $date = $request_data['date'];
        $time = str_replace(':', '-', $request_data['time']);
        $request_ident = "from_{$from}_to_{$to}_at_{$date}T{$time}";

        $response = $this->getMockedResponse(
            $request_ident,
            __DIR__,
            function () use ($request_data) {
                $real_fetcher = new TransportApiFetcher();
                $real_data = $real_fetcher->fetchConnection($request_data);
                return json_encode($real_data, JSON_PRETTY_PRINT);
            }
        );
        return json_decode($response, true);
    }
}

/**
 * @internal
 *
 * @coversNothing
 */
class SearchTransportConnectionEndpointForTest extends SearchTransportConnectionEndpoint {
    public function testOnlyGetCenterOfOriginStations() {
        return $this->getCenterOfOriginStations();
    }

    public function testOnlyGetMostPeripheralOriginStations() {
        return $this->getMostPeripheralOriginStations();
    }

    public function testOnlyGetJoiningStationFromConnection(
        $connection,
        $latest_joining_time_by_station_id,
        $latest_departure_by_station_id,
    ) {
        return $this->getJoiningStationFromConnection(
            $connection,
            $latest_joining_time_by_station_id,
            $latest_departure_by_station_id
        );
    }
}

/**
 * @internal
 *
 * @covers \Olz\Apps\Oev\Endpoints\SearchTransportConnectionEndpoint
 */
final class SearchTransportConnectionEndpointTest extends UnitTestCase {
    public function testSearchTransportConnectionEndpointIdent(): void {
        $endpoint = new SearchTransportConnectionEndpoint();
        $this->assertSame('SearchTransportConnectionEndpoint', $endpoint->getIdent());
    }

    public function testSearchTransportConnectionEndpointGetCenterOfOriginStations(): void {
        $endpoint = new SearchTransportConnectionEndpointForTest();
        $endpoint->runtimeSetup();
        // sic!
        $this->assertSame([
            'x' => 47.296817499999996,
            'y' => 8.577107125000001,
        ], $endpoint->testOnlyGetCenterOfOriginStations());
    }

    public function testSearchTransportConnectionEndpointGetMostPeripheralOriginStations(): void {
        $endpoint = new SearchTransportConnectionEndpointForTest();
        $endpoint->runtimeSetup();
        $this->assertSame([
            'Richterswil',
            'Wädenswil',
            'Zürich Wiedikon',
            'Zürich HB',
            'Au ZH',
            'Zürich Enge',
            'Zürich Wollishofen',
            'Adliswil',
            'Horgen',
            'Horgen Oberdorf',
            'Kilchberg ZH',
            'Langnau-Gattikon',
            'Rüschlikon',
            'Oberrieden Dorf',
            'Oberrieden',
            'Thalwil',
        ], array_map(function ($station) {
            return $station['name'];
        }, $endpoint->testOnlyGetMostPeripheralOriginStations()));
    }

    public function testSearchTransportConnectionEndpointGetJoiningStationFromConnection(): void {
        $connection = TransportConnection::fromFieldValue(['sections' => [
            [
                'departure' => ['stationId' => 1, 'stationName' => 'A', 'time' => 1],
                'arrival' => ['stationId' => 2, 'stationName' => 'B', 'time' => 2],
                'passList' => [],
                'isWalk' => false,
            ],
        ]]);
        $latest_joining_time_by_station_id = [
            // Cannot join at A (1)
            1 => 0 + SearchTransportConnectionEndpoint::MIN_CHANGING_TIME,
            // Can join at B (2)
            2 => 2 + SearchTransportConnectionEndpoint::MIN_CHANGING_TIME,
        ];
        $latest_departure_by_station_id = [];
        $endpoint = new SearchTransportConnectionEndpointForTest();
        $endpoint->runtimeSetup();
        $this->assertSame(
            2,
            $endpoint->testOnlyGetJoiningStationFromConnection(
                $connection,
                $latest_joining_time_by_station_id,
                $latest_departure_by_station_id
            )
        );
    }

    public function testSearchTransportConnectionEndpointExample1(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $fake_transport_api_fetcher = new FakeSearchTransportConnectionEndpointTransportApiFetcher();
        $endpoint = new SearchTransportConnectionEndpoint();
        $endpoint->runtimeSetup();
        $endpoint->setTransportApiFetcher($fake_transport_api_fetcher);

        $result = $endpoint->call([
            'destination' => 'Winterthur',
            'arrival' => '2021-09-22 09:00:00',
        ]);

        $this->assertSame([
            'INFO Valid user request',
            'INFO Suggestion omitted:   O   2021-09-22 07:10:00 Langnau-G',
            'INFO Suggestion omitted:   O   2021-09-22 07:10:00 Langnau-G',
            'INFO Suggestion omitted:   O   2021-09-22 07:10:00 Langnau-G',
            'INFO Suggestion omitted: O 2021-09-22 07:10:00 Langnau-Gatti',
            'INFO Valid user response',
        ], array_map(function ($line) {
            return substr($line, 0, 60);
        }, $this->getLogs()));

        $this->assertSame('OK', $result['status']);
        $this->assertCount(8, $result['suggestions']);

        $pretty_prints = array_map(function ($suggestion) {
            $transport_suggestion = TransportSuggestion::fromFieldValue($suggestion);
            return $transport_suggestion->getPrettyPrint();
        }, $result['suggestions']);
        $this->assertSame(
            <<<'ZZZZZZZZZZ'
                  O   2021-09-22 07:10:00 Langnau-Gattikon
                  O   2021-09-22 07:11:00 Wildpark-Höfli
                  O   2021-09-22 07:13:00 Sihlau
                  O   2021-09-22 07:15:00 Adliswil
                    O 2021-09-22 07:15:00 Horgen Oberdorf
                  O   2021-09-22 07:16:00 Sood-Oberleimbach
                    O 2021-09-22 07:17:00 Oberrieden Dorf
                O     2021-09-22 07:18:00 Richterswil
                  O   2021-09-22 07:19:00 Zürich Leimbach
                  O   2021-09-22 07:20:00 Zürich Manegg
                    < 2021-09-22 07:21:00 Thalwil
                  O   2021-09-22 07:22:00 Zürich Brunau
                O     2021-09-22 07:23:00 Wädenswil
                  O   2021-09-22 07:24:00 Zürich Saalsporthalle
                O     2021-09-22 07:25:00 Au ZH
                  O   2021-09-22 07:26:00 Zürich Giesshübel
                  O   2021-09-22 07:28:00 Zürich Selnau
                O     2021-09-22 07:30:00 Horgen
                  O   2021-09-22 07:31:00 Zürich HB SZU
                  O   2021-09-22 07:31:00 Zürich HB SZU
                O     2021-09-22 07:32:00 Oberrieden
                O     2021-09-22 07:36:00 Thalwil
                  <   2021-09-22 07:38:00 Zürich HB
                O     2021-09-22 07:38:00 Rüschlikon
                O     2021-09-22 07:40:00 Kilchberg ZH
                O     2021-09-22 07:43:00 Zürich Wollishofen
                O     2021-09-22 07:48:00 Zürich Enge
                O     2021-09-22 07:49:00 Zürich Wiedikon
                O     2021-09-22 07:55:00 Zürich HB
                O     2021-09-22 08:00:00 Zürich Oerlikon
                O     2021-09-22 08:04:00 Wallisellen
                O     2021-09-22 08:06:00 Dietlikon
                O     2021-09-22 08:12:00 Effretikon
                O     2021-09-22 08:19:00 Winterthur
                ZZZZZZZZZZ,
            $pretty_prints[0]
        );
        $this->assertSame(
            <<<'ZZZZZZZZZZ'
                      O 2021-09-22 07:25:00 Au ZH
                      O 2021-09-22 07:30:00 Horgen
                      O 2021-09-22 07:32:00 Oberrieden
                      < 2021-09-22 07:36:00 Thalwil
                  O     2021-09-22 07:40:00 Langnau-Gattikon
                O       2021-09-22 07:42:00 Richterswil
                  O     2021-09-22 07:43:00 Sihlau
                  O     2021-09-22 07:45:00 Adliswil
                    O   2021-09-22 07:45:00 Horgen Oberdorf
                  O     2021-09-22 07:46:00 Sood-Oberleimbach
                    O   2021-09-22 07:47:00 Oberrieden Dorf
                O       2021-09-22 07:48:00 Wädenswil
                  O     2021-09-22 07:49:00 Zürich Leimbach
                  O     2021-09-22 07:50:00 Zürich Manegg
                    O   2021-09-22 07:51:00 Thalwil
                  O     2021-09-22 07:52:00 Zürich Brunau
                    O   2021-09-22 07:53:00 Rüschlikon
                O       2021-09-22 07:54:00 Horgen
                  O     2021-09-22 07:54:00 Zürich Saalsporthalle
                    O   2021-09-22 07:55:00 Kilchberg ZH
                  O     2021-09-22 07:56:00 Zürich Giesshübel
                  O     2021-09-22 07:58:00 Zürich Selnau
                    O   2021-09-22 07:58:00 Zürich Wollishofen
                O       2021-09-22 07:59:00 Thalwil
                  O     2021-09-22 08:01:00 Zürich HB SZU
                  O     2021-09-22 08:01:00 Zürich HB SZU
                    <   2021-09-22 08:03:00 Zürich Enge
                O       2021-09-22 08:06:00 Zürich Enge
                O       2021-09-22 08:07:00 Zürich Wiedikon
                  <     2021-09-22 08:08:00 Zürich HB
                O       2021-09-22 08:14:00 Zürich HB
                O       2021-09-22 08:18:00 Zürich Oerlikon
                O       2021-09-22 08:22:00 Zürich Oerlikon
                O       2021-09-22 08:27:00 Zürich Flughafen
                O       2021-09-22 08:31:00 Bassersdorf
                O       2021-09-22 08:38:00 Effretikon
                O       2021-09-22 08:46:00 Winterthur
                ZZZZZZZZZZ,
            $pretty_prints[1]
        );
        $this->assertSame(
            <<<'ZZZZZZZZZZ'
                  O   2021-09-22 07:45:00 Horgen Oberdorf
                  O   2021-09-22 07:47:00 Oberrieden Dorf
                    O 2021-09-22 07:48:00 Richterswil
                  O   2021-09-22 07:51:00 Thalwil
                  O   2021-09-22 07:53:00 Rüschlikon
                    O 2021-09-22 07:53:00 Wädenswil
                  O   2021-09-22 07:55:00 Kilchberg ZH
                    O 2021-09-22 07:55:00 Au ZH
                  O   2021-09-22 07:58:00 Zürich Wollishofen
                O     2021-09-22 08:00:00 Langnau-Gattikon
                    O 2021-09-22 08:00:00 Horgen
                    O 2021-09-22 08:02:00 Oberrieden
                  O   2021-09-22 08:03:00 Zürich Enge
                O     2021-09-22 08:03:00 Sihlau
                  O   2021-09-22 08:04:00 Zürich Wiedikon
                O     2021-09-22 08:05:00 Adliswil
                    O 2021-09-22 08:06:00 Thalwil
                O     2021-09-22 08:06:00 Sood-Oberleimbach
                    O 2021-09-22 08:08:00 Rüschlikon
                O     2021-09-22 08:09:00 Zürich Leimbach
                O     2021-09-22 08:10:00 Zürich Manegg
                    O 2021-09-22 08:10:00 Kilchberg ZH
                O     2021-09-22 08:12:00 Zürich Brunau
                    O 2021-09-22 08:13:00 Zürich Wollishofen
                O     2021-09-22 08:14:00 Zürich Saalsporthalle
                  <   2021-09-22 08:14:00 Zürich HB
                O     2021-09-22 08:16:00 Zürich Giesshübel
                    O 2021-09-22 08:18:00 Zürich Enge
                O     2021-09-22 08:18:00 Zürich Selnau
                    O 2021-09-22 08:19:00 Zürich Wiedikon
                O     2021-09-22 08:21:00 Zürich HB SZU
                O     2021-09-22 08:21:00 Zürich HB SZU
                    < 2021-09-22 08:25:00 Zürich HB
                O     2021-09-22 08:28:00 Zürich HB
                O     2021-09-22 08:31:00 Zürich HB
                O     2021-09-22 08:35:00 Zürich Stadelhofen
                O     2021-09-22 08:39:00 Stettbach
                O     2021-09-22 08:51:00 Winterthur
                ZZZZZZZZZZ,
            $pretty_prints[7]
        );
    }

    public function testSearchTransportConnectionEndpointExample2(): void {
        // TODO
        $this->markTestSkipped('Too slow and broken.');

        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $fake_transport_api_fetcher = new FakeSearchTransportConnectionEndpointTransportApiFetcher();
        $endpoint = new SearchTransportConnectionEndpoint();
        $endpoint->runtimeSetup();
        $endpoint->setTransportApiFetcher($fake_transport_api_fetcher);

        $result = $endpoint->call([
            'destination' => 'Flumserberg Tannenheim',
            'arrival' => '2021-10-01 13:15:00',
        ]);

        $this->assertSame([
            'INFO Valid user request',
            'INFO Suggestion omitted:     O   2021-10-01 07:10:00 Kilchbe',
            'INFO Suggestion omitted:       O   2021-10-01 07:10:00 Kilch',
            'INFO Suggestion omitted:             O   2021-10-01 08:05:00',
            'INFO Suggestion omitted:             O   2021-10-01 08:05:00',
            'INFO Suggestion omitted:     O     2021-10-01 07:05:00 Langn',
            'INFO Suggestion omitted:       O       2021-10-01 07:10:00 K',
            'INFO Suggestion omitted:           O   2021-10-01 08:05:00 A',
            'INFO Suggestion omitted:           O   2021-10-01 08:05:00 A',
            'INFO Suggestion omitted:     O       2021-10-01 07:10:00 Kil',
            'INFO Suggestion omitted:     O       2021-10-01 08:05:00 Lan',
            'INFO Suggestion omitted:           O   2021-10-01 08:05:00 A',
            'INFO Suggestion omitted:           O   2021-10-01 08:05:00 A',
            'INFO Suggestion omitted:     O     2021-10-01 07:05:00 Langn',
            'INFO Suggestion omitted:       O     2021-10-01 07:10:00 Kil',
            'INFO Suggestion omitted:         O     2021-10-01 08:05:00 A',
            'INFO Suggestion omitted:         O     2021-10-01 08:05:00 A',
            'INFO Suggestion omitted:     O     2021-10-01 07:05:00 Langn',
            'INFO Suggestion omitted:         O   2021-10-01 08:05:00 Adl',
            'INFO Suggestion omitted: O       2021-10-01 07:05:00 Langnau',
            'INFO Suggestion omitted:     O       2021-10-01 07:10:00 Kil',
            'INFO Suggestion omitted:           O   2021-10-01 08:05:00 A',
            'INFO Suggestion omitted:           O   2021-10-01 08:05:00 A',
            'INFO Suggestion omitted:         O   2021-10-01 08:05:00 Adl',
            'INFO Suggestion omitted:         O     2021-10-01 08:05:00 A',
            'INFO Suggestion omitted:         O   2021-10-01 08:05:00 Adl',
            'INFO Suggestion omitted:         O     2021-10-01 08:05:00 A',
            'INFO Suggestion omitted:   O     2021-10-01 07:05:00 Langnau',
            'INFO Suggestion omitted:     O       2021-10-01 07:10:00 Kil',
            'INFO Suggestion omitted:         O   2021-10-01 08:05:00 Adl',
            'INFO Suggestion omitted:         O   2021-10-01 08:05:00 Adl',
            'INFO Valid user response',
        ], array_map(function ($line) {
            return substr($line, 0, 60);
        }, $this->getLogs()));
        $this->assertSame('OK', $result['status']);
        $this->assertCount(0, $result['suggestions']);
    }

    public function testSearchTransportConnectionEndpointExample3(): void {
        // TODO
        $this->markTestSkipped('Too slow and broken.');

        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $fake_transport_api_fetcher = new FakeSearchTransportConnectionEndpointTransportApiFetcher();
        $endpoint = new SearchTransportConnectionEndpoint();
        $endpoint->runtimeSetup();
        $endpoint->setTransportApiFetcher($fake_transport_api_fetcher);

        $result = $endpoint->call([
            'destination' => 'Chur',
            'arrival' => '2022-10-30 15:00:00',
        ]);

        $this->assertSame([
            'INFO Valid user request',
            'INFO Suggestion omitted: O   2022-10-30 11:45:00 Richterswil',
            'INFO Suggestion omitted:   O     2022-10-30 11:15:00 Horgen ',
            'INFO Suggestion omitted:           O             2022-10-30 ',
            'INFO Suggestion omitted:     O         2022-10-30 11:15:00 H',
            'INFO Suggestion omitted:       O           2022-10-30 11:15:',
            'INFO Suggestion omitted:           O         2022-10-30 11:1',
            'INFO Suggestion omitted:         O           2022-10-30 11:1',
            'INFO Suggestion omitted:   O 2022-10-30 11:45:00 Richterswil',
            'INFO Suggestion omitted:   O       2022-10-30 11:15:00 Horge',
            'INFO Suggestion omitted:           O           2022-10-30 11',
            'INFO Suggestion omitted:           O               2022-10-3',
            'INFO Suggestion omitted:     O         2022-10-30 11:15:00 H',
            'INFO Suggestion omitted:         O           2022-10-30 11:1',
            'INFO Suggestion omitted:           O         2022-10-30 11:1',
            'INFO Suggestion omitted:     O       2022-10-30 11:15:00 Hor',
            'INFO Suggestion omitted:       O       2022-10-30 11:15:00 H',
            'INFO Suggestion omitted:           O       2022-10-30 11:15:',
            'INFO Suggestion omitted:         O         2022-10-30 11:15:',
            'INFO Suggestion omitted:     O         2022-10-30 11:15:00 H',
            'INFO Suggestion omitted:         O               2022-10-30 ',
            'INFO Suggestion omitted:   O     2022-10-30 11:15:00 Horgen ',
            'INFO Suggestion omitted:   O     2022-10-30 11:15:00 Horgen ',
            'INFO Suggestion omitted:           O             2022-10-30 ',
            'INFO Suggestion omitted: O       2022-10-30 11:15:00 Horgen ',
            'INFO Suggestion omitted:   O         2022-10-30 11:15:00 Hor',
            'INFO Suggestion omitted:         O             2022-10-30 11',
            'INFO Suggestion omitted:       O               2022-10-30 11',
            'INFO Suggestion omitted:     O         2022-10-30 11:15:00 H',
            'INFO Suggestion omitted:       O           2022-10-30 11:15:',
            'INFO Suggestion omitted:         O               2022-10-30 ',
            'INFO Suggestion omitted:         O             2022-10-30 11',
            'INFO Valid user response',
        ], array_map(function ($line) {
            return substr($line, 0, 60);
        }, $this->getLogs()));

        $this->assertSame('OK', $result['status']);
        // $this->assertCount(8, $result['suggestions']);

        $pretty_prints = array_map(function ($suggestion) {
            $transport_suggestion = TransportSuggestion::fromFieldValue($suggestion);
            return $transport_suggestion->getPrettyPrint();
        }, $result['suggestions']);
        // TODO: Fix; Change to rating-based system (missing station => worse rating)
        $this->assertSame([], $pretty_prints);
    }

    public function testSearchTransportConnectionEndpointFailingRequest(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $fake_transport_api_fetcher = new FakeSearchTransportConnectionEndpointTransportApiFetcher();
        $endpoint = new SearchTransportConnectionEndpoint();
        $endpoint->runtimeSetup();
        $endpoint->setTransportApiFetcher($fake_transport_api_fetcher);

        $result = $endpoint->call([
            'destination' => 'inexistent',
            'arrival' => '2021-10-01 13:15:00',
        ]);

        $this->assertSame([
            'INFO Valid user request',
            'ERROR Exception: Unmocked request: from_8503207_to_inexistent_at_2021-10-01T13-15 in',
            'INFO Valid user response',
        ], $this->getLogs(function ($record, $level_name, $message) {
            $cropped_message = substr($message, 0, 78);
            return "{$level_name} {$cropped_message}";
        }));
        $this->assertSame([
            'status' => 'ERROR',
            'suggestions' => null,
        ], $result);
    }

    public function testSearchTransportConnectionEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => false];
        $endpoint = new SearchTransportConnectionEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                'destination' => 'Flumserberg Tannenheim',
                'arrival' => '2021-10-01 13:15:00',
            ]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                "INFO Valid user request",
                "WARNING HTTP error 403",
            ], $this->getLogs());
            $this->assertSame(403, $err->getCode());
        }
    }
}
