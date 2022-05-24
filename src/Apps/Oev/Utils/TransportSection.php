<?php

namespace Olz\Apps\Oev\Utils;

use Olz\Utils\WithUtilsTrait;
use PhpTypeScriptApi\Fields\FieldTypes;

require_once __DIR__.'/TransportHalt.php';

class TransportSection {
    use WithUtilsTrait;
    public const UTILS = [];

    protected $departure;
    protected $arrival;
    protected $passList = [];
    protected $isWalk;

    public static function getField() {
        $halt_field = TransportHalt::getField();
        return new FieldTypes\ObjectField([
            'field_structure' => [
                'departure' => $halt_field,
                'arrival' => $halt_field,
                'passList' => new FieldTypes\ArrayField([
                    'item_field' => $halt_field,
                ]),
                'isWalk' => new FieldTypes\BooleanField(),
            ],
            'export_as' => 'OlzTransportSection',
        ]);
    }

    public static function fromTransportApi($api_section) {
        $section = new self();
        $section->parseFromTransportApi($api_section);
        return $section;
    }

    protected function parseFromTransportApi($api_section) {
        $this->isWalk = (
            ($api_section['journey'] ?? null) === null
            && $api_section['walk']
        );
        $this->departure = TransportHalt::fromTransportApi($api_section['departure']);
        $this->arrival = TransportHalt::fromTransportApi($api_section['arrival']);
        $this->passList = [];

        $pass_list = $api_section['journey']['passList'] ?? [];
        foreach ($pass_list as $pass_location) {
            $halt = TransportHalt::fromTransportApi($pass_location);
            if ($halt->getStationId() === $this->departure->getStationId()) {
                continue;
            }
            if ($halt->getStationId() === $this->arrival->getStationId()) {
                continue;
            }
            if (!$halt->getTimeSeconds()) {
                continue;
            }
            $this->passList[] = $halt;
        }
    }

    public function getFieldValue() {
        return [
            'departure' => $this->departure->getFieldValue(),
            'arrival' => $this->arrival->getFieldValue(),
            'passList' => array_map(function ($halt) {
                return $halt->getFieldValue();
            }, $this->passList),
            'isWalk' => $this->isWalk,
        ];
    }

    public function getDeparture() {
        return $this->departure;
    }

    public function getArrival() {
        return $this->arrival;
    }

    public function getPassList() {
        return $this->passList;
    }

    public function getHalts() {
        $halts = [];
        $halts[] = $this->departure;
        foreach ($this->passList as $halt) {
            $halts[] = $halt;
        }
        $halts[] = $this->arrival;
        return $halts;
    }
}
