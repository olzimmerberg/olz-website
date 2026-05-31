<?php

namespace Olz\Repository;

use Olz\Entity\Counter;
use Olz\Repository\Common\OlzRepository;

/**
 * @extends OlzRepository<Counter>
 */
class CounterRepository extends OlzRepository {
    public function recordVisit(string $page): void {
        $counter = $this->getCounter($page);
        $counter->incrementCounter();
        $this->getEntityManager()->flush();
    }

    public function recordLatency(string $page, float $latency_ms): void {
        $counter = $this->getCounter($page);
        $counter->addLatencyMeasurment($latency_ms);
        $this->getEntityManager()->flush();
    }

    public function getCounter(string $page): Counter {
        $date_range = $this->dateUtils()->getCurrentDateInFormat('Y-m');
        $counter = $this->findOneBy(['page' => $page, 'date_range' => $date_range]);
        if (!$counter) {
            $counter = new Counter();
            $counter->setPage($page);
            $counter->setDateRange($date_range);
            $counter->setCounter(0);
            $counter->setLatencyAvgMs(0);
            $counter->setLatencyNum(0);
            $this->getEntityManager()->persist($counter);
        }
        return $counter;
    }
}
