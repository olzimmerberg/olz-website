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

    public static function fromFieldValue($value) {
        $instance = new self();
        $instance->populateFromFieldValue($value);
        return $instance;
    }

    protected function populateFromFieldValue($value) {
        $this->sections = array_map(function ($section) {
            return TransportSection::fromFieldValue($section);
        }, $value['sections']);
    }

    public function getSections() {
        return $this->sections;
    }

    public function setSections($new_sections) {
        $this->sections = $new_sections;
    }

    public function isSuperConnectionOf($sub_connection) {
        $super_halts = $this->getFlatHalts();
        $sub_halts = $sub_connection->getFlatHalts();
        $sub_halt_index = 0;
        foreach ($super_halts as $super_halt) {
            $sub_halt = $sub_halts[$sub_halt_index];
            if ($super_halt->equals($sub_halt)) {
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

    public function getCropped($start_halt, $end_halt) {
        $stage = 'search_start';
        $cropped_sections = [];
        foreach ($this->sections as $section) {
            if ($stage === 'search_start') {
                $cropped_section = null;
                $contains_start = true;
                try {
                    $cropped_section = $section->getCropped($start_halt, null);
                } catch (\ValueError $th) {
                    $contains_start = false;
                }
                if ($contains_start) {
                    try {
                        $cropped_section = $section->getCropped($start_halt, $end_halt);
                        $cropped_sections[] = $cropped_section;
                        if ($end_halt !== null) {
                            $stage = 'end_found';
                        } else {
                            $stage = 'search_end';
                        }
                    } catch (\ValueError $th) {
                        $cropped_sections[] = $cropped_section;
                        $stage = 'search_end';
                    }
                }
            } elseif ($stage === 'search_end') {
                try {
                    $cropped_section = $section->getCropped(null, $end_halt);
                    $cropped_sections[] = $cropped_section;
                    if ($end_halt !== null) {
                        $stage = 'end_found';
                    }
                } catch (\ValueError $th) {
                    // End not yet found. Add Section as a whole.
                    $cropped_sections[] = $section;
                }
            }
        }
        $cropped_connection = new self();
        $cropped_connection->setSections($cropped_sections);
        return $cropped_connection;
    }
}
