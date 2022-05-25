<?php

use Olz\Utils\WithUtilsTrait;
use PhpTypeScriptApi\Fields\FieldTypes;

class TransportHalt {
    use WithUtilsTrait;
    public const UTILS = [];

    protected $stationId;
    protected $stationName;
    protected $timeSeconds;
    protected $timeString;

    public static function getField() {
        return new FieldTypes\ObjectField([
            'field_structure' => [
                'stationId' => new FieldTypes\StringField(),
                'stationName' => new FieldTypes\StringField(),
                'time' => new FieldTypes\DateTimeField(),
            ],
            'export_as' => 'OlzTransportHalt',
        ]);
    }

    public static function parseFromTransportApi($api_halt) {
        date_default_timezone_set('Europe/Zurich');
        $halt = new self();
        $halt->stationId = $api_halt['station']['id'];
        $halt->stationName = $api_halt['station']['name'];
        $halt->timeSeconds = $api_halt['departureTimestamp'] ?? $api_halt['arrivalTimestamp'] ?? null;
        return $halt;
    }

    public function getStationId() {
        return $this->stationId;
    }

    public function getStationName() {
        return $this->stationName;
    }

    public function getTimeSeconds() {
        return $this->timeSeconds;
    }

    public function getTimeString() {
        return $this->timeSeconds ? date('Y-m-d H:i:s', $this->timeSeconds) : null;
    }

    public function getFieldValue() {
        return [
            'stationId' => $this->stationId,
            'stationName' => $this->stationName,
            'time' => $this->timeString,
        ];
    }
}
