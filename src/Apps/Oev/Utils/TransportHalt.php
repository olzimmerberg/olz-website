<?php

namespace Olz\Apps\Oev\Utils;

use Olz\Utils\WithUtilsTrait;
use PhpTypeScriptApi\Fields\FieldTypes;

class TransportHalt {
    use WithUtilsTrait;

    protected string $stationId;
    protected string $stationName;
    protected ?int $timeSeconds;
    protected string $timeString;

    public static function getField(): FieldTypes\Field {
        return new FieldTypes\ObjectField([
            'field_structure' => [
                'stationId' => new FieldTypes\StringField(),
                'stationName' => new FieldTypes\StringField(),
                'time' => new FieldTypes\DateTimeField(),
            ],
            'export_as' => 'OlzTransportHalt',
        ]);
    }

    /** @param array<string, mixed> $api_halt */
    public static function fromTransportApi(array $api_halt): self {
        $halt = new self();
        $halt->parseFromTransportApi($api_halt);
        return $halt;
    }

    /** @param array<string, mixed> $api_halt */
    protected function parseFromTransportApi(array $api_halt): void {
        date_default_timezone_set('Europe/Zurich');
        $this->stationId = $api_halt['station']['id'];
        $this->stationName = $api_halt['station']['name'];
        $this->timeSeconds = $api_halt['departureTimestamp'] ?? $api_halt['arrivalTimestamp'] ?? null;
    }

    /** @return array<string, mixed> */
    public function getFieldValue(): array {
        return [
            'stationId' => $this->stationId,
            'stationName' => $this->stationName,
            'time' => $this->getTimeString(),
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
        date_default_timezone_set('Europe/Zurich');
        $this->stationId = $value['stationId'];
        $this->stationName = $value['stationName'];
        $this->timeSeconds = strtotime($value['time']);
    }

    public function getStationId(): string {
        return $this->stationId;
    }

    public function getStationName(): string {
        return $this->stationName;
    }

    public function getTimeSeconds(): ?int {
        return $this->timeSeconds;
    }

    public function getTimeString(): ?string {
        return $this->timeSeconds ? date('Y-m-d H:i:s', $this->timeSeconds) : null;
    }

    public function equals(TransportHalt $other_halt): bool {
        return
            $this->getStationId() === $other_halt->getStationId()
            && $this->getTimeSeconds() === $other_halt->getTimeSeconds();
    }
}
