<?php

namespace Olz\Repository;

use Olz\Entity\Counter;
use Olz\Repository\Common\OlzRepository;

class CounterRepository extends OlzRepository {
    public function record($page) {
        $truncated_page = substr($page, 0, 255);
        $config = [
            'page' => $truncated_page,
            'date_range' => $this->dateUtils()->getCurrentDateInFormat('Y-m'),
            'args' => null,
        ];
        $this->recordWithConfig($config);
    }

    public function recordWithConfig($config) {
        $counters = $this->findBy($config);
        if (count($counters) > 1) {
            $new_count = 0;
            foreach ($counters as $counter) {
                $new_count += $counter->getCounter();
                $this->getEntityManager()->remove($counter);
            }
            $counter = new Counter();
            $counter->setPage($config['page']);
            $counter->setDateRange($config['date_range']);
            $counter->setArgs($config['args']);
            $counter->setCounter($new_count);
            $this->getEntityManager()->persist($counter);
        } elseif (count($counters) == 1) {
            $counter = $counters[0];
            $counter->incrementCounter();
        } else {
            $counter = new Counter();
            $counter->setPage($config['page']);
            $counter->setDateRange($config['date_range']);
            $counter->setArgs($config['args']);
            $counter->setCounter(1);
            $this->getEntityManager()->persist($counter);
        }
        $this->getEntityManager()->flush();
    }
}
