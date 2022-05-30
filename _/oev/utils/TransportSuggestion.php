<?php

use PhpTypeScriptApi\Fields\FieldTypes;

require_once __DIR__.'/TransportHalt.php';
require_once __DIR__.'/TransportConnection.php';

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

    public function getMainConnection() {
        return $this->mainConnection;
    }

    public function getSideConnections() {
        return $this->sideConnections;
    }

    public function getOriginInfo() {
        return $this->originInfo;
    }

    public function getDebug() {
        return $this->debug;
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
