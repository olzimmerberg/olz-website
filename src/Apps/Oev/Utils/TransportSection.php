<?php

namespace Olz\Apps\Oev\Utils;

use Olz\Utils\WithUtilsTrait;

class TransportSection {
    use WithUtilsTrait;

    protected TransportHalt $departure;
    protected TransportHalt $arrival;
    /** @var array<TransportHalt> */
    protected array $passList = [];
    protected bool $isWalk;

    /** @param array<string, mixed> $api_section */
    public static function fromTransportApi(array $api_section): self {
        $section = new self();
        $section->parseFromTransportApi($api_section);
        return $section;
    }

    /** @param array<string, mixed> $api_section */
    protected function parseFromTransportApi(array $api_section): void {
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

    /** @return array<string, mixed> */
    public function getFieldValue(): array {
        return [
            'departure' => $this->departure->getFieldValue(),
            'arrival' => $this->arrival->getFieldValue(),
            'passList' => array_map(function ($halt) {
                return $halt->getFieldValue();
            }, $this->passList),
            'isWalk' => $this->isWalk,
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
        $this->departure = TransportHalt::fromFieldValue($value['departure']);
        $this->arrival = TransportHalt::fromFieldValue($value['arrival']);
        $this->passList = array_map(function ($halt) {
            return TransportHalt::fromFieldValue($halt);
        }, $value['passList']);
        $this->isWalk = $value['isWalk'];
    }

    public function getDeparture(): TransportHalt {
        return $this->departure;
    }

    public function setDeparture(TransportHalt $new_departure): void {
        $this->departure = $new_departure;
    }

    public function getArrival(): TransportHalt {
        return $this->arrival;
    }

    public function setArrival(TransportHalt $new_arrival): void {
        $this->arrival = $new_arrival;
    }

    /** @return array<TransportHalt> */
    public function getPassList(): array {
        return $this->passList;
    }

    /** @param array<TransportHalt> $new_pass_list */
    public function setPassList(array $new_pass_list): void {
        $this->passList = $new_pass_list;
    }

    public function getIsWalk(): bool {
        return $this->isWalk;
    }

    public function setIsWalk(bool $new_is_walk): void {
        $this->isWalk = $new_is_walk;
    }

    /** @return array<TransportHalt> */
    public function getHalts(): array {
        $halts = [];
        $halts[] = $this->departure;
        foreach ($this->passList as $halt) {
            $halts[] = $halt;
        }
        $halts[] = $this->arrival;
        return $halts;
    }

    public function getCropped(?TransportHalt $start_halt, ?TransportHalt $end_halt): TransportSection {
        $cropped_section = new self();
        $cropped_section->setIsWalk($this->isWalk);
        if ($start_halt === null && $end_halt === null) {
            $cropped_section->setDeparture($this->departure);
            $cropped_section->setArrival($this->arrival);
            $cropped_section->setPassList(array_slice($this->passList, 0));
            return $cropped_section;
        }
        $stage = 'search_start';
        if ($start_halt === null) {
            $cropped_section->setDeparture($this->departure);
            $stage = 'search_end';
        }
        $cropped_pass_list = [];
        foreach ($this->getHalts() as $halt) {
            if ($stage === 'search_start') {
                if ($start_halt && $halt->equals($start_halt)) {
                    $cropped_section->setDeparture($halt);
                    $stage = 'search_end';
                }
            } elseif ($stage === 'search_end') {
                if ($end_halt && $halt->equals($end_halt)) {
                    $cropped_section->setArrival($halt);
                    $stage = 'end_found';
                } else {
                    $cropped_pass_list[] = $halt;
                }
            }
        }
        if ($end_halt === null && $stage === 'search_end') {
            $pass_list_length = count($cropped_pass_list);
            $cropped_section->setArrival($cropped_pass_list[$pass_list_length - 1]);
            $cropped_pass_list = array_slice($cropped_pass_list, 0, $pass_list_length - 1);
            $stage = 'end_found';
        }
        $cropped_section->setPassList($cropped_pass_list);
        if ($stage === 'search_start') {
            throw new \ValueError('TransportSection::getCropped: Start not found.');
        }
        if ($stage === 'search_end') {
            throw new \ValueError('TransportSection::getCropped: End not found.');
        }
        return $cropped_section;
    }
}
