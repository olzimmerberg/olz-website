<?php

namespace Olz\Apps\Oev\Endpoints;

use Olz\Api\OlzEndpoint;
use Olz\Apps\Oev\Utils\CoordinateUtils;
use Olz\Apps\Oev\Utils\TransportConnection;
use Olz\Apps\Oev\Utils\TransportSuggestion;
use Olz\Fetchers\TransportApiFetcher;
use PhpTypeScriptApi\Fields\FieldTypes;

class SearchTransportConnectionEndpoint extends OlzEndpoint {
    public const MIN_CHANGING_TIME = 1; // Minimum time to change at same station

    protected $originStations;
    protected $transportApiFetcher;

    protected $is_origin_station_by_station_id = [];

    public function __construct() {
        $filename = __DIR__.'/../olz_transit_stations.json';
        $content = file_get_contents($filename);
        $data = json_decode($content, true);
        $this->originStations = $data;
    }

    public function runtimeSetup() {
        parent::runtimeSetup();
        $transport_api_fetcher = new TransportApiFetcher();
        $this->setTransportApiFetcher($transport_api_fetcher);
    }

    public function setTransportApiFetcher($transportApiFetcher) {
        $this->transportApiFetcher = $transportApiFetcher;
    }

    public static function getIdent(): string {
        return 'SearchTransportConnectionEndpoint';
    }

    public function getResponseField(): FieldTypes\Field {
        $suggestion_field = TransportSuggestion::getField();
        return new FieldTypes\ObjectField(['field_structure' => [
            'status' => new FieldTypes\EnumField(['allowed_values' => [
                'OK',
                'ERROR',
            ]]),
            'suggestions' => new FieldTypes\ArrayField([
                'allow_null' => true,
                'item_field' => $suggestion_field,
            ]),
        ]]);
    }

    public function getRequestField(): FieldTypes\Field {
        return new FieldTypes\ObjectField(['field_structure' => [
            'destination' => new FieldTypes\StringField(['allow_null' => false]),
            'arrival' => new FieldTypes\DateTimeField(['allow_null' => false]),
        ]]);
    }

    protected function handle($input) {
        $this->checkPermission('any');

        $destination = $input['destination'];
        $arrival_datetime = new \DateTime($input['arrival']);
        try {
            $all_connections =
                $this->getConnectionsFromOriginsToDestination(
                    $destination,
                    $arrival_datetime
                );
        } catch (\Throwable $th) {
            $this->log()->error($th);
            return ['status' => 'ERROR', 'suggestions' => null];
        }

        $suggestions = [];
        foreach ($all_connections as $main_connection) {
            $suggestion = new TransportSuggestion();
            $suggestion->setMainConnection($main_connection);

            $result = $this->processMainConnection($main_connection);
            // For each station on the main connection, the departure time:
            $latest_joining_time_by_station_id = $result['latest_joining_time_by_station_id'];
            // For each origin station, the departure time using the main connection:
            $latest_departure_by_station_id = $result['latest_departure_by_station_id'];

            $suggestion->addDebug("Latest joining time by station id:");
            $suggestion->addDebug(json_encode($latest_joining_time_by_station_id, JSON_PRETTY_PRINT));
            $suggestion->addDebug("Latest departure time by station id:");
            $suggestion->addDebug(json_encode($latest_departure_by_station_id, JSON_PRETTY_PRINT));

            foreach (array_reverse($all_connections) as $connection) {
                $joining_station_id = $this->getJoiningStationFromConnection(
                    $connection,
                    $latest_joining_time_by_station_id,
                    $latest_departure_by_station_id
                );

                if ($joining_station_id !== null) {
                    $result = $this->shouldUseConnection(
                        $connection,
                        $joining_station_id,
                        $latest_departure_by_station_id
                    );
                    $use_this_connection = $result['use_this_connection'];
                    if ($use_this_connection) {
                        $latest_departure_by_station_id =
                            $result['latest_departure_by_station_id'];
                        $side_connection = [
                            'connection' => $connection,
                            'joiningStationId' => $joining_station_id,
                        ];
                        $suggestion->addSideConnection($side_connection);
                    }
                }
            }

            $origin_info = $suggestion->getOriginInfo();
            $suggestion->setOriginInfo($origin_info);
            foreach ($origin_info as $station_info) {
                $station_name = $station_info['halt']['stationName'];
                $rating = $station_info['rating'];
                $suggestion->addDebug("Station info {$station_name} {$rating}");
            }

            $missing_stations_covered = [];
            foreach ($latest_departure_by_station_id as $station_id => $latest_departure) {
                if ($latest_departure === 0) {
                    $missing_stations_covered[] = $station_id;
                }
            }
            if (count($missing_stations_covered) === 0) {
                $normalized_suggestion = $this->getNormalizedSuggestion(
                    $suggestion,
                    $latest_departure_by_station_id
                );
                $suggestions[] = $normalized_suggestion->getFieldValue();
            } else {
                $missing_station_ids_covered = implode(', ', $missing_stations_covered);
                $this->log()->info("Suggestion omitted: {$suggestion->getPrettyPrint()}\n\nMissing stations: {$missing_station_ids_covered}");
            }
        }

        return ['status' => 'OK', 'suggestions' => $suggestions];
    }

    protected function getConnectionsFromOriginsToDestination(
        $destination,
        $arrival_datetime,
    ) {
        $most_peripheral_stations = $this->getMostPeripheralOriginStations();
        $arrival_date = $arrival_datetime->format('Y-m-d');
        $arrival_time = $arrival_datetime->format('H:i');

        $connections_by_origin_station_id = [];
        // For each station ID stores whether it has already been covered by
        // another connection.
        $is_covered_by_station_id = [];
        foreach ($most_peripheral_stations as $station) {
            $station_id = $station['id'];
            if ($is_covered_by_station_id[$station_id] ?? false) {
                continue;
            }
            $connection_response = $this->transportApiFetcher->fetchConnection([
                'from' => $station_id,
                'to' => $destination,
                'date' => $arrival_date,
                'time' => $arrival_time,
                'isArrivalTime' => 1,
            ]);
            $api_connections = $connection_response['connections'] ?? null;
            if ($api_connections === null) {
                throw new \Exception('Request to transport API failed');
            }
            foreach ($api_connections as $api_connection) {
                $connection = TransportConnection::fromTransportApi($api_connection);
                $halts = $connection->getFlatHalts();
                foreach ($halts as $halt) {
                    $halt_station_id = $halt->getStationId();
                    $is_covered_by_station_id[$halt_station_id] = true;
                    $connections_from_halt =
                        $connections_by_origin_station_id[$halt_station_id] ?? [];
                    $relevant_connections_from_halt = [];
                    foreach ($connections_from_halt as $connection_from_halt) {
                        if (!$connection->isSuperConnectionOf($connection_from_halt)) {
                            $relevant_connections_from_halt[] = $connection_from_halt;
                        }
                    }
                    $connections_by_origin_station_id[$halt_station_id] =
                        $relevant_connections_from_halt;
                }
                $connections_from_station = $connections_by_origin_station_id[$station_id] ?? [];
                $connections_from_station[] = $connection;
                $connections_by_origin_station_id[$station_id] = $connections_from_station;
            }
        }

        $all_connections = [];
        foreach ($connections_by_origin_station_id as $station_id => $connections) {
            foreach ($connections as $connection) {
                $all_connections[] = $connection;
            }
        }

        return $all_connections;
    }

    protected function getMostPeripheralOriginStations() {
        $coord_utils = CoordinateUtils::fromEnv();
        $center_of_stations = $this->getCenterOfOriginStations();
        $most_peripheral_stations = array_slice($this->originStations, 0);
        usort(
            $most_peripheral_stations,
            function ($station_a, $station_b) use ($coord_utils, $center_of_stations) {
                $station_a_point = [
                    'x' => $station_a['coordinate']['x'],
                    'y' => $station_a['coordinate']['y'],
                ];
                $station_b_point = [
                    'x' => $station_b['coordinate']['x'],
                    'y' => $station_b['coordinate']['y'],
                ];
                $station_a_dist = $coord_utils->getDistance(
                    $station_a_point,
                    $center_of_stations
                );
                $station_b_dist = $coord_utils->getDistance(
                    $station_b_point,
                    $center_of_stations
                );
                return $station_a_dist < $station_b_dist ? 1 : -1;
            }
        );
        return $most_peripheral_stations;
    }

    protected function getCenterOfOriginStations() {
        $coord_utils = CoordinateUtils::fromEnv();
        $station_points = array_map(function ($station) {
            return [
                'x' => $station['coordinate']['x'],
                'y' => $station['coordinate']['y'],
            ];
        }, $this->originStations);
        return $coord_utils->getCenter($station_points);
    }

    protected function processMainConnection($main_connection) {
        $latest_departure_by_station_id = [];

        foreach ($this->originStations as $station) {
            $latest_departure_by_station_id[$station['id']] = 0;
        }

        $latest_joining_time_by_station_id = [];

        $sections = $main_connection->getSections() ?? [];
        foreach ($sections as $section) {
            $halts = $section->getHalts();
            foreach ($halts as $halt) {
                $station_id = $halt->getStationId();
                $time = $halt->getTimeSeconds() ?? 0;
                if (($latest_joining_time_by_station_id[$station_id] ?? 0) < $time) {
                    $latest_joining_time_by_station_id[$station_id] = $time;
                }
                if (isset($latest_departure_by_station_id[$station_id])) {
                    $latest_departure_by_station_id[$station_id] = $time;
                }
            }
        }

        return [
            'latest_joining_time_by_station_id' => $latest_joining_time_by_station_id,
            'latest_departure_by_station_id' => $latest_departure_by_station_id,
        ];
    }

    protected function getJoiningStationFromConnection(
        $connection,
        $latest_joining_time_by_station_id,
        $latest_departure_by_station_id,
    ) {
        $joining_station_id = null;
        $look_for_joining_station = true;
        $halts = $connection->getFlatHalts();
        foreach ($halts as $halt) {
            $station_id = $halt->getStationId();
            $time = $halt->getTimeSeconds();
            $latest_joining_at_station =
                $latest_joining_time_by_station_id[$station_id] ?? $time;
            $can_join_at_station =
                $latest_joining_at_station > $time + self::MIN_CHANGING_TIME;
            if ($can_join_at_station && $look_for_joining_station) {
                $joining_station_id = $station_id;
                $look_for_joining_station = false;
            }
            $is_unserved_origin_station =
                ($latest_departure_by_station_id[$station_id] ?? null) === 0;
            if ($is_unserved_origin_station) {
                $look_for_joining_station = true;
            }
        }
        return $joining_station_id;
    }

    protected function shouldUseConnection(
        $connection,
        $joining_station_id,
        $latest_departure_by_station_id,
    ) {
        $use_this_connection = false;
        $is_before_joining = true;
        $halts = $connection->getFlatHalts();
        foreach ($halts as $halt) {
            $station_id = $halt->getStationId();
            $time = $halt->getTimeSeconds();
            if ($station_id === $joining_station_id) {
                $is_before_joining = false;
            }
            $does_improve_travel_time =
                ($latest_departure_by_station_id[$station_id] ?? $time) < $time;
            if ($is_before_joining && $does_improve_travel_time) {
                $latest_departure_by_station_id[$station_id] = $time;
                $use_this_connection = true;
            }
        }
        return [
            'use_this_connection' => $use_this_connection,
            'latest_departure_by_station_id' => $latest_departure_by_station_id,
        ];
    }

    protected function getNormalizedSuggestion(
        $suggestion,
        $latest_departure_by_station_id,
    ) {
        $normalized_suggestion = new TransportSuggestion();

        $normalized_main_connection = $this->getNormalizedConnection(
            $suggestion->getMainConnection(),
            $latest_departure_by_station_id
        );
        $normalized_suggestion->setMainConnection($normalized_main_connection);

        foreach ($suggestion->getSideConnections() as $side_connection) {
            $normalized_connection = $this->getNormalizedConnection(
                $side_connection['connection'],
                $latest_departure_by_station_id
            );
            if ($normalized_connection !== null) {
                $normalized_side_connection = [
                    'connection' => $normalized_connection,
                    'joiningStationId' => $side_connection['joiningStationId'],
                ];
                $normalized_suggestion->addSideConnection($normalized_side_connection);
            }
        }

        $origin_info = $normalized_suggestion->getOriginInfo();
        $normalized_suggestion->setOriginInfo($origin_info);

        foreach ($suggestion->getDebug() as $line) {
            $normalized_suggestion->addDebug($line);
        }

        return $normalized_suggestion;
    }

    protected function getNormalizedConnection(
        $connection,
        $latest_departure_by_station_id,
    ) {
        $crop_from_halt = null;
        foreach ($connection->getFlatHalts() as $halt) {
            $station_id = $halt->getStationId();
            $latest_departure = $latest_departure_by_station_id[$station_id] ?? null;
            if ($latest_departure === null) {
                continue;
            }
            $halt_is_latest_departure = $halt->getTimeSeconds() >= $latest_departure;
            if ($crop_from_halt === null && $halt_is_latest_departure) {
                $crop_from_halt = $halt;
            }
        }
        if ($crop_from_halt === null) {
            return null;
        }
        return $connection->getCropped($crop_from_halt, null);
    }

    protected function isOriginStation($station_id) {
        if (($this->is_origin_station_by_station_id ?? null) === null) {
            $this->is_origin_station_by_station_id = [];
            foreach ($this->originStations as $station) {
                $this->is_origin_station_by_station_id[$station['id']] = true;
            }
        }
        return $this->is_origin_station_by_station_id[$station_id] ?? false;
    }
}
