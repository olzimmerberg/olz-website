<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity;

use Olz\Entity\Measurement;
use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;

/**
 * @extends FakeOlzRepository<Measurement>
 */
class FakeMeasurementRepository extends FakeOlzRepository {
    /** @var array<string, ?float> */
    public array $measurements = [];

    /** @var array<string, int> */
    public array $versions = [];

    /** @var array<string> */
    public array $removed = [];

    public function get(string $name, ?int $year, ?int $month): ?Measurement {
        $key = $this->getKey($name, $year, $month);
        if (!array_key_exists($key, $this->measurements)) {
            return null;
        }
        $measurement = new Measurement();
        $measurement->setName($name);
        $measurement->setYear($year);
        $measurement->setMonth($month);
        $measurement->setValue($this->measurements[$key]);
        $measurement->setVersion($this->versions[$key] ?? 0);
        return $measurement;
    }

    public function set(string $name, ?int $year, ?int $month, ?float $value, int $version): Measurement {
        $key = $this->getKey($name, $year, $month);
        $this->measurements[$key] = $value;
        $this->versions[$key] = $version;
        $measurement = new Measurement();
        $measurement->setName($name);
        $measurement->setYear($year);
        $measurement->setMonth($month);
        $measurement->setValue($value);
        $measurement->setVersion($version);
        return $measurement;
    }

    protected function getKey(string $name, ?int $year, ?int $month): string {
        return "{$name}|{$year}|{$month}";
    }

    public function remove(string $name, ?int $year, ?int $month): void {
        $key = $this->getKey($name, $year, $month);
        if (array_key_exists($key, $this->measurements)) {
            unset($this->measurements[$key], $this->versions[$key]);
            $this->removed[] = $key;
        }
    }
}
