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
        return $measurement;
    }

    public function set(string $name, ?int $year, ?int $month, ?float $value): Measurement {
        $key = $this->getKey($name, $year, $month);
        $this->measurements[$key] = $value;
        $measurement = new Measurement();
        $measurement->setName($name);
        $measurement->setYear($year);
        $measurement->setMonth($month);
        $measurement->setValue($value);
        return $measurement;
    }

    protected function getKey(string $name, ?int $year, ?int $month): string {
        return "{$name}|{$year}|{$month}";
    }
}
