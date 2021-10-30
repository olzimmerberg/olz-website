<?php

use PhpTypeScriptApi\Fields\FieldTypes;

require_once __DIR__.'/../../api/OlzEndpoint.php';
require_once __DIR__.'/../utils/CoordinateUtils.php';

class SearchTransportConnectionEndpoint extends OlzEndpoint {
    const MIN_CHANGING_TIME = 1; // Minimum time to change at same station

    public function __construct() {
        $filename = __DIR__.'/../../shared/olz_transit_stations.json';
        $content = file_get_contents($filename);
        $data = json_decode($content, true);
        $this->originStations = $data;
    }

    public function runtimeSetup() {
        parent::runtimeSetup();
        global $_CONFIG;
        require_once __DIR__.'/../../fetchers/TransportApiFetcher.php';
        require_once __DIR__.'/../../utils/auth/AuthUtils.php';
        $auth_utils = AuthUtils::fromEnv();
        $transport_api_fetcher = new TransportApiFetcher();
        $this->setAuthUtils($auth_utils);
        $this->setTransportApiFetcher($transport_api_fetcher);
    }

    public function setAuthUtils($authUtils) {
        $this->authUtils = $authUtils;
    }

    public function setTransportApiFetcher($transportApiFetcher) {
        $this->transportApiFetcher = $transportApiFetcher;
    }

    public static function getIdent() {
        return 'SearchTransportConnectionEndpoint';
    }

    public function getResponseField() {
        $halt_field = new FieldTypes\ObjectField([
            'field_structure' => [
                'stationId' => new FieldTypes\StringField(),
                'stationName' => new FieldTypes\StringField(),
                'time' => new FieldTypes\DateTimeField(),
            ],
            'export_as' => 'OlzTransportHalt',
        ]);
        $section_field = new FieldTypes\ObjectField([
            'field_structure' => [
                'departure' => $halt_field,
                'arrival' => $halt_field,
                'passList' => new FieldTypes\ArrayField([
                    'item_field' => $halt_field,
                ]),
            ],
            'export_as' => 'OlzTransportSection',
        ]);
        $connection_field = new FieldTypes\ObjectField([
            'field_structure' => [
                'sections' => new FieldTypes\ArrayField([
                    'item_field' => $section_field,
                ]),
            ],
            'export_as' => 'OlzTransportConnection',
        ]);
        $suggestion_field = new FieldTypes\ObjectField([
            'field_structure' => [
                'mainConnection' => $connection_field,
                'sideConnections' => new FieldTypes\ArrayField([
                    'item_field' => new FieldTypes\ObjectField(['field_structure' => [
                        'connection' => $connection_field,
                        'joiningStationId' => new FieldTypes\StringField(),
                    ]]),
                ]),
                'debug' => new FieldTypes\StringField(),
            ],
            'export_as' => 'OlzTransportConnectionSuggestion',
        ]);
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

    public function getRequestField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'destination' => new FieldTypes\StringField(['allow_null' => false]),
            'arrival' => new FieldTypes\DateTimeField(['allow_null' => false]),
        ]]);
    }

    protected function handle($input) {
        $has_access = $this->authUtils->hasPermission('any');
        if (!$has_access) {
            return ['status' => 'ERROR', 'suggestions' => null];
        }

        $destination = $input['destination'];
        $arrival_datetime = new DateTime($input['arrival']);
        try {
            $all_connections =
                $this->getConnectionsFromOriginsToDestination(
                    $destination, $arrival_datetime);
        } catch (\Throwable $th) {
            return ['status' => 'ERROR', 'suggestions' => null];
        }

        $suggestions = [];
        foreach ($all_connections as $main_connection) {
            $suggestion = [
                'mainConnection' => $this->convertConnection($main_connection),
                'sideConnections' => [],
                'debug' => "",
            ];

            $result = $this->processMainConnection($main_connection);
            // For each station on the main connection, the departure time:
            $latest_joining_time_by_station_id = $result['latest_joining_time_by_station_id'];
            // For each origin station, the departure time using the main connection:
            $latest_departure_by_station_id = $result['latest_departure_by_station_id'];

            $suggestion['debug'] .= "Latest joining time by station id:\n";
            $suggestion['debug'] .= json_encode($latest_joining_time_by_station_id, JSON_PRETTY_PRINT);
            $suggestion['debug'] .= "\n\nLatest departure time by station id:\n";
            $suggestion['debug'] .= json_encode($latest_departure_by_station_id, JSON_PRETTY_PRINT);

            foreach ($all_connections as $connection) {
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
                    $latest_departure_by_station_id =
                        $result['latest_departure_by_station_id'];
                    if ($use_this_connection) {
                        $side_connection = [
                            'connection' => $this->convertConnection($connection),
                            'joiningStationId' => $joining_station_id,
                        ];
                        $suggestion['sideConnections'][] = $side_connection;
                    }
                }
            }

            $all_stations_covered = true;
            foreach ($latest_departure_by_station_id as $station_id => $latest_departure) {
                if ($latest_departure === 0) {
                    $all_stations_covered = false;
                }
            }
            if ($all_stations_covered) {
                $suggestions[] = $suggestion;
            }
        }
        return ['status' => 'OK', 'suggestions' => $suggestions];
    }

    protected function getConnectionsFromOriginsToDestination($destination, $arrival_datetime) {
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
            $connections = $connection_response['connections'] ?? null;
            if ($connections === null) {
                throw new Exception('Request to transport API failed');
            }
            foreach ($connections as $connection) {
                $sections = $connection['sections'] ?? [];
                foreach ($sections as $section) {
                    $pass_list = $section['journey']['passList'] ?? [];
                    foreach ($pass_list as $pass_location) {
                        $pass_station_id = $pass_location['station']['id'];
                        $is_covered_by_station_id[$pass_station_id] = true;
                        $connections_from_pass =
                            $connections_by_origin_station_id[$pass_station_id] ?? [];
                        $relevant_connections_from_pass = array_filter(
                            $connections_from_pass,
                            function ($connection_from_pass) use ($connection) {
                                return !$this->isSuperConnection(
                                    $connection, $connection_from_pass);
                            }
                        );
                        $connections_by_origin_station_id[$pass_station_id] =
                            $relevant_connections_from_pass;
                    }
                }
            }
            $connections_by_origin_station_id[$station_id] = $connections;
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

    protected function isSuperConnection($super_connection, $sub_connection) {
        $super_halts = $this->getFlatHaltListOfConnection($super_connection);
        $sub_halts = $this->getFlatHaltListOfConnection($sub_connection);
        $sub_halt_index = 0;
        foreach ($super_halts as $super_halt) {
            $super_station_id = $super_halt['station']['id'];
            $super_departure = $super_halt['departure'];
            $sub_halt = $sub_halts[$sub_halt_index];
            $sub_station_id = $sub_halt['station']['id'];
            $sub_departure = $sub_halt['departure'];
            if ($super_station_id === $sub_station_id && $super_departure === $sub_departure) {
                $sub_halt_index++;
                if ($sub_halt_index >= count($sub_halts)) {
                    return true;
                }
            }
        }
        return false;
    }

    protected function getFlatHaltListOfConnection($connection) {
        $flat_halt_list = [];
        $sections = $connection['sections'] ?? [];
        foreach ($sections as $section) {
            $pass_list = $section['journey']['passList'] ?? [];
            foreach ($pass_list as $pass_location) {
                $flat_halt_list[] = $pass_location;
            }
        }
        return $flat_halt_list;
    }

    protected function processMainConnection($main_connection) {
        $latest_departure_by_station_id = [];

        foreach ($this->originStations as $station) {
            $latest_departure_by_station_id[$station['id']] = 0;
        }

        $latest_joining_time_by_station_id = [];

        $sections = $main_connection['sections'] ?? [];
        foreach ($sections as $section) {
            $station_id = $section['departure']['station']['id'];
            $time = $section['departure']['departureTimestamp'];
            if (($latest_joining_time_by_station_id[$station_id] ?? 0) < $time) {
                $latest_joining_time_by_station_id[$station_id] = $time;
            }
            if (isset($latest_departure_by_station_id[$station_id])) {
                $latest_departure_by_station_id[$station_id] = $time;
            }
            $station_id = $section['arrival']['station']['id'];
            $time = $section['arrival']['arrivalTimestamp'];
            if (($latest_joining_time_by_station_id[$station_id] ?? 0) < $time) {
                $latest_joining_time_by_station_id[$station_id] = $time;
            }
            if (isset($latest_departure_by_station_id[$station_id])) {
                $latest_departure_by_station_id[$station_id] = $time;
            }
            $pass_list = $section['journey']['passList'] ?? [];
            foreach ($pass_list as $pass_location) {
                $station_id = $pass_location['station']['id'];
                $time = $pass_location['departureTimestamp'];
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
        $latest_departure_by_station_id
    ) {
        $joining_station_id = null;
        $look_for_joining_station = true;
        $sections = $connection['sections'] ?? [];
        foreach ($sections as $section) {
            $station_id = $section['departure']['station']['id'];
            $time = $section['departure']['departureTimestamp'] ?? $section['departure']['arrivalTimestamp'];
            $latest_joining_at_station = $latest_joining_time_by_station_id[$station_id] ?? $time;
            if ($latest_joining_at_station > $time + self::MIN_CHANGING_TIME && $look_for_joining_station) {
                $joining_station_id = $station_id;
                $look_for_joining_station = false;
            }
            if (($latest_departure_by_station_id[$station_id] ?? null) === 0) {
                $look_for_joining_station = true;
            }
            $pass_list = $section['journey']['passList'] ?? [];
            foreach ($pass_list as $pass_location) {
                $station_id = $pass_location['station']['id'];
                $time = $pass_location['departureTimestamp'] ?? $pass_location['arrivalTimestamp'];
                $latest_joining_at_station = $latest_joining_time_by_station_id[$station_id] ?? $time;
                if ($latest_joining_at_station > $time + self::MIN_CHANGING_TIME && $look_for_joining_station) {
                    $joining_station_id = $station_id;
                    $look_for_joining_station = false;
                }
                if (($latest_departure_by_station_id[$station_id] ?? null) === 0) {
                    $look_for_joining_station = true;
                }
            }
            $station_id = $section['arrival']['station']['id'];
            $time = $section['arrival']['arrivalTimestamp'] ?? $pass_location['departureTimestamp'];
            $latest_joining_at_station = $latest_joining_time_by_station_id[$station_id] ?? $time;
            if ($latest_joining_at_station > $time + self::MIN_CHANGING_TIME && $look_for_joining_station) {
                $joining_station_id = $station_id;
                $look_for_joining_station = false;
            }
            if (($latest_departure_by_station_id[$station_id] ?? null) === 0) {
                $look_for_joining_station = true;
            }
        }
        return $joining_station_id;
    }

    protected function shouldUseConnection(
        $connection,
        $joining_station_id,
        $latest_departure_by_station_id
    ) {
        $use_this_connection = false;
        $is_before_joining = true;
        $sections = $connection['sections'] ?? [];
        foreach ($sections as $section) {
            $station_id = $section['departure']['station']['id'];
            $time = $section['departure']['departureTimestamp'];
            if ($station_id === $joining_station_id) {
                $is_before_joining = false;
            }
            if ($is_before_joining && ($latest_departure_by_station_id[$station_id] ?? $time) < $time) {
                $latest_departure_by_station_id[$station_id] = $time;
                $use_this_connection = true;
            }
            $pass_list = $section['journey']['passList'] ?? [];
            foreach ($pass_list as $pass_location) {
                $station_id = $pass_location['station']['id'];
                $time = $pass_location['departureTimestamp'];
                if ($station_id === $joining_station_id) {
                    $is_before_joining = false;
                }
                if ($is_before_joining && ($latest_departure_by_station_id[$station_id] ?? $time) < $time) {
                    $latest_departure_by_station_id[$station_id] = $time;
                    $use_this_connection = true;
                }
            }
            $station_id = $section['arrival']['station']['id'];
            $time = $section['arrival']['arrivalTimestamp'];
            if ($station_id === $joining_station_id) {
                $is_before_joining = false;
            }
            if ($is_before_joining && ($latest_departure_by_station_id[$station_id] ?? $time) < $time) {
                $latest_departure_by_station_id[$station_id] = $time;
                $use_this_connection = true;
            }
        }
        return [
            'use_this_connection' => $use_this_connection,
            'latest_departure_by_station_id' => $latest_departure_by_station_id,
        ];
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

    protected function convertConnection($input_connection) {
        $converted_connection = ['sections' => []];
        $input_sections = $input_connection['sections'] ?? [];
        foreach ($input_sections as $input_section) {
            $converted_section = [
                'departure' => [],
                'arrival' => [],
                'passList' => [],
            ];
            $departure_station_id = $input_section['departure']['station']['id'];
            $converted_section['departure']['stationId'] = $departure_station_id;
            $converted_section['departure']['stationName'] =
                $input_section['departure']['station']['name'];
            $converted_section['departure']['time'] =
                date('Y-m-d H:i:s', $input_section['departure']['departureTimestamp']);
            $arrival_station_id = $input_section['arrival']['station']['id'];
            $converted_section['arrival']['stationId'] = $arrival_station_id;
            $converted_section['arrival']['stationName'] =
                $input_section['arrival']['station']['name'];
            $converted_section['arrival']['time'] =
                date('Y-m-d H:i:s', $input_section['arrival']['arrivalTimestamp']);
            $pass_list = $input_section['journey']['passList'] ?? [];
            foreach ($pass_list as $pass_location) {
                $station_id = $pass_location['station']['id'];
                if ($station_id === $departure_station_id || $station_id === $arrival_station_id) {
                    continue;
                }
                $timestamp = $pass_location['departureTimestamp'];
                if (!$timestamp) {
                    continue;
                }
                $time = date('Y-m-d H:i:s', $timestamp);
                $converted_section['passList'][] = [
                    'stationId' => $station_id,
                    'stationName' => $pass_location['station']['name'],
                    'time' => $time,
                ];
            }
            $converted_connection['sections'][] = $converted_section;
        }
        return $converted_connection;
    }
}
