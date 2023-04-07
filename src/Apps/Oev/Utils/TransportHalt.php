<?php

namespace Olz\Apps\Oev\Utils;

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

    public static function fromTransportApi($api_halt) {
        $halt = new self();
        $halt->parseFromTransportApi($api_halt);
        return $halt;
    }

    protected function parseFromTransportApi($api_halt) {
        date_default_timezone_set('Europe/Zurich');
        $this->stationId = $api_halt['station']['id'];
        $this->stationName = $api_halt['station']['name'];
        $this->timeSeconds = $api_halt['departureTimestamp'] ?? $api_halt['arrivalTimestamp'] ?? null;
    }

    public function getFieldValue() {
        return [
            'stationId' => $this->stationId,
            'stationName' => $this->stationName,
            'time' => $this->getTimeString(),
        ];
    }

    public static function fromFieldValue($value) {
        $instance = new self();
        $instance->populateFromFieldValue($value);
        return $instance;
    }

    protected function populateFromFieldValue($value) {
        date_default_timezone_set('Europe/Zurich');
        $this->stationId = $value['stationId'];
        $this->stationName = $value['stationName'];
        $this->timeSeconds = strtotime($value['time']);
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

    public function equals($other_halt) {
        return
            $this->getStationId() === $other_halt->getStationId()
            && $this->getTimeSeconds() === $other_halt->getTimeSeconds();
    }
}
