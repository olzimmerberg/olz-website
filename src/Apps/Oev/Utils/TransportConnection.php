<?php

namespace Olz\Apps\Oev\Utils;

use Olz\Utils\WithUtilsTrait;
use PhpTypeScriptApi\Fields\FieldTypes;

require_once __DIR__.'/TransportSection.php';

class TransportConnection {
    use WithUtilsTrait;
    public const UTILS = [];

    protected $sections = [];

    public static function getField() {
        $section_field = TransportSection::getField();
        return new FieldTypes\ObjectField([
            'field_structure' => [
                'sections' => new FieldTypes\ArrayField([
                    'item_field' => $section_field,
                ]),
            ],
            'export_as' => 'OlzTransportConnection',
        ]);
    }

    public static function fromTransportApi($api_connection) {
        $connection = new self();
        $connection->parseFromTransportApi($api_connection);
        return $connection;
    }

    protected function parseFromTransportApi($api_connection) {
        $this->sections = [];
        $api_sections = $api_connection['sections'] ?? [];
        foreach ($api_sections as $api_section) {
            $section = TransportSection::fromTransportApi($api_section);
            $this->sections[] = $section;
        }
    }

    public function getFieldValue() {
        return [
            'sections' => array_map(function ($section) {
                return $section->getFieldValue();
            }, $this->sections),
        ];
    }

    public function getSections() {
        return $this->sections;
    }

    public function isSuperConnectionOf($sub_connection) {
        $super_halts = $this->getFlatHalts();
        $sub_halts = $sub_connection->getFlatHalts();
        $sub_halt_index = 0;
        foreach ($super_halts as $super_halt) {
            $sub_halt = $sub_halts[$sub_halt_index];
            if (
                $super_halt->getStationId() === $sub_halt->getStationId()
                && $super_halt->getTimeSeconds() === $sub_halt->getTimeSeconds()
            ) {
                $sub_halt_index++;
                if ($sub_halt_index >= count($sub_halts)) {
                    return true;
                }
            }
        }
        return false;
    }

    public function getFlatHalts() {
        return array_merge(...array_map(function ($section) {
            return $section->getHalts();
        }, $this->sections));
    }

    public function getDestinationHalt() {
        $flat_halts = $this->getFlatHalts();
        $last_index = count($flat_halts) - 1;
        return $flat_halts[$last_index];
    }
}