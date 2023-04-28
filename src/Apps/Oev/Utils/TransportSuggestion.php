<?php

namespace Olz\Apps\Oev\Utils;

use Olz\Utils\WithUtilsTrait;
use PhpTypeScriptApi\Fields\FieldTypes;

class TransportSuggestion {
    use WithUtilsTrait;
    public const UTILS = [];

    protected $mainConnection;
    protected $sideConnections = [];
    protected $originInfo = [];
    protected $debug = [];

    public static function getField() {
        $halt_field = TransportHalt::getField();
        $connection_field = TransportConnection::getField();
        $origin_info_field = new FieldTypes\ObjectField([
            'field_structure' => [
                'halt' => $halt_field,
                'isSkipped' => new FieldTypes\BooleanField(),
                'rating' => new FieldTypes\NumberField(['min_value' => 0.0, 'max_value' => 1.0]),
            ],
            'export_as' => 'OlzOriginInfo',
        ]);
        return new FieldTypes\ObjectField([
            'field_structure' => [
                'mainConnection' => $connection_field,
                'sideConnections' => new FieldTypes\ArrayField([
                    'item_field' => new FieldTypes\ObjectField(['field_structure' => [
                        'connection' => $connection_field,
                        'joiningStationId' => new FieldTypes\StringField(),
                    ]]),
                ]),
                'originInfo' => new FieldTypes\ArrayField([
                    'item_field' => $origin_info_field,
                ]),
                'debug' => new FieldTypes\StringField(),
            ],
            'export_as' => 'OlzTransportSuggestion',
        ]);
    }

    public function getFieldValue() {
        return [
            'mainConnection' => $this->mainConnection->getFieldValue(),
            'sideConnections' => array_map(function ($side_connection) {
                return [
                    'connection' => $side_connection['connection']->getFieldValue(),
                    'joiningStationId' => $side_connection['joiningStationId'],
                ];
            }, $this->sideConnections),
            'originInfo' => $this->originInfo,
            'debug' => implode("\n", $this->debug),
        ];
    }

    public static function fromFieldValue($value) {
        $instance = new self();
        $instance->populateFromFieldValue($value);
        return $instance;
    }

    protected function populateFromFieldValue($value) {
        $this->mainConnection = TransportConnection::fromFieldValue($value['mainConnection']);
        $this->sideConnections = array_map(function ($side_connection) {
            return [
                'connection' => TransportConnection::fromFieldValue($side_connection['connection']),
                'joiningStationId' => $side_connection['joiningStationId'],
            ];
        }, $value['sideConnections']);
        $this->originInfo = $value['originInfo'];
        $this->debug = explode("\n", $value['debug']);
    }

    public function getPrettyPrint() {
        $all_entries = [];
        foreach ($this->mainConnection->getFlatHalts() as $halt) {
            $all_entries[] = [
                'halt' => $halt,
                'connection_index' => 0,
                'is_joining_halt' => false,
            ];
        }
        $side_connection_index = 1;
        foreach ($this->sideConnections as $side_connection) {
            $has_joined = false;
            foreach ($side_connection['connection']->getFlatHalts() as $halt) {
                $is_joining_halt = $side_connection['joiningStationId'] == $halt->getStationId();
                if (!$has_joined) {
                    $all_entries[] = [
                        'halt' => $halt,
                        'connection_index' => $side_connection_index,
                        'is_joining_halt' => $is_joining_halt,
                    ];
                }
                if ($is_joining_halt) {
                    $has_joined = true;
                }
            }
            $side_connection_index++;
        }
        usort(
            $all_entries,
            function ($entry_a, $entry_b) {
                $entry_a_value = $entry_a['halt']->getTimeSeconds();
                $entry_b_value = $entry_b['halt']->getTimeSeconds();
                return $entry_a_value < $entry_b_value ? -1 : 1;
            }
        );
        $num_connections = count($this->sideConnections) + 1;
        return implode("\n", array_map(function ($entry) use ($num_connections) {
            $before_dot = str_repeat('  ', $entry['connection_index']);
            $after_dot = str_repeat('  ', $num_connections - $entry['connection_index'] - 1);
            $dot = $entry['is_joining_halt'] ? '<' : 'O';
            $halt = $entry['halt'];
            return "{$before_dot}{$dot}{$after_dot} {$halt->getTimeString()} {$halt->getStationName()}";
        }, $all_entries));
    }

    public function getMainConnection() {
        return $this->mainConnection;
    }

    public function setMainConnection($new_main_connection) {
        $this->mainConnection = $new_main_connection;
    }

    public function getSideConnections() {
        return $this->sideConnections;
    }

    public function addSideConnection($new_side_connection) {
        $this->sideConnections[] = $new_side_connection;
    }

    public function getOriginInfo() {
        return $this->originInfo;
    }

    public function setOriginInfo($new_origin_info) {
        $this->originInfo = $new_origin_info;
    }

    public function getDebug() {
        return $this->debug;
    }

    public function addDebug($line) {
        $this->debug[] = $line;
    }

    public function generateOriginInfo($origin_stations) {
        $most_important_stations = array_slice($origin_stations, 0);
        usort(
            $most_important_stations,
            function ($station_a, $station_b) {
                $station_a_weight = $station_a['weight'] ?? 0;
                $station_b_weight = $station_b['weight'] ?? 0;
                return $station_a_weight < $station_b_weight ? 1 : -1;
            }
        );
        $destination_halt = $this->getDestinationHalt();
        return array_map(function ($station) use ($destination_halt) {
            $station_id = $station['id'];
            $halt_at_station = $this->getHaltAtStation($station_id);
            return [
                'halt' => [
                    'stationId' => $station_id,
                    'stationName' => $station['name'],
                    'time' => $halt_at_station->timeString ?? '',
                ],
                'isSkipped' => $halt_at_station === null,
                'rating' => $this->getRatingForHalt($halt_at_station, $destination_halt),
            ];
        }, $most_important_stations);
    }

    public function getDestinationHalt() {
        return $this->mainConnection->getDestinationHalt();
    }

    public function getHaltAtStation($station_id) {
        $halts = $this->getFlatHalts();
        $halts_at_station = array_values(array_filter(
            $halts,
            function ($halt) use ($station_id) {
                return $halt->stationId === $station_id;
            }
        ));
        $last_index = count($halts_at_station) - 1;
        if ($last_index === -1) {
            return null;
        }
        return $halts_at_station[$last_index];
    }

    public function getFlatHalts() {
        return array_merge(
            $this->mainConnection->getFlatHalts(),
            ...array_map(function ($side_connection) {
                return $side_connection['connection']->getFlatHalts();
            }, $this->sideConnections),
        );
    }

    public function getRatingForHalt($halt, $destination_halt) {
        if (!$halt || !$destination_halt) {
            return 0;
        }
        $duration_seconds = $destination_halt->timeSeconds - $halt->timeSeconds;
        return 1 / max(1, log($duration_seconds));
    }
}
