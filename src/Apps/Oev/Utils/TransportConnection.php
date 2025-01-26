<?php

namespace Olz\Apps\Oev\Utils;

use Olz\Utils\WithUtilsTrait;

class TransportConnection {
    use WithUtilsTrait;

    /** @var array<TransportSection> */
    protected array $sections = [];

    /** @param array<string, mixed> $api_connection */
    public static function fromTransportApi(array $api_connection): self {
        $connection = new self();
        $connection->parseFromTransportApi($api_connection);
        return $connection;
    }

    /** @param array<string, mixed> $api_connection */
    protected function parseFromTransportApi(array $api_connection): void {
        $this->sections = [];
        $api_sections = $api_connection['sections'] ?? [];
        foreach ($api_sections as $api_section) {
            $section = TransportSection::fromTransportApi($api_section);
            $this->sections[] = $section;
        }
    }

    /** @return array<string, mixed> */
    public function getFieldValue() {
        return [
            'sections' => array_map(function ($section) {
                return $section->getFieldValue();
            }, $this->sections),
        ];
    }

    /** @param array<string, mixed> $value */
    public static function fromFieldValue(array $value): self {
        $instance = new self();
        $instance->populateFromFieldValue($value);
        return $instance;
    }

    /** @param array<string, mixed> $value */
    protected function populateFromFieldValue(array $value): void {
        $this->sections = array_map(function ($section) {
            return TransportSection::fromFieldValue($section);
        }, $value['sections']);
    }

    /** @return array<TransportSection> */
    public function getSections(): array {
        return $this->sections;
    }

    /** @param array<TransportSection> $new_sections */
    public function setSections(array $new_sections): void {
        $this->sections = $new_sections;
    }

    public function isSuperConnectionOf(TransportConnection $sub_connection): bool {
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

    /** @return array<TransportHalt> */
    public function getFlatHalts(): array {
        return array_merge(...array_map(function ($section) {
            return $section->getHalts();
        }, $this->sections));
    }

    public function getDestinationHalt(): TransportHalt {
        $flat_halts = $this->getFlatHalts();
        $last_index = count($flat_halts) - 1;
        return $flat_halts[$last_index];
    }

    public function getCropped(?TransportHalt $start_halt, ?TransportHalt $end_halt): TransportConnection {
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
