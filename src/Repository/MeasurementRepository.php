<?php

namespace Olz\Repository;

use Olz\Entity\Measurement;
use Olz\Repository\Common\OlzRepository;

/**
 * @extends OlzRepository<Measurement>
 */
class MeasurementRepository extends OlzRepository {
    public function get(string $name, ?int $year, ?int $month): ?Measurement {
        return $this->findOneBy(['name' => $name, 'year' => $year, 'month' => $month]);
    }

    public function set(string $name, ?int $year, ?int $month, ?float $value): Measurement {
        $measurement = $this->get($name, $year, $month);
        if (!$measurement) {
            $measurement = new Measurement();
            $measurement->setName($name);
            $measurement->setYear($year);
            $measurement->setMonth($month);
        }
        $measurement->setValue($value);
        $this->getEntityManager()->persist($measurement);
        return $measurement;
    }
}
