<?php

namespace Olz\Repository;

use Doctrine\ORM\EntityRepository;
use Olz\Entity\Counter;

class CounterRepository extends EntityRepository {
    public function record($page, $date, $referrer, $user_agent) {
        if (
            preg_match('/bingbot/i', $user_agent)
            || preg_match('/googlebot/i', $user_agent)
        ) {
            return;
        }
        $truncated_page = substr($page, 0, 255);
        $config = [
            'page' => $truncated_page,
            'date_range' => $date->getCurrentDateInFormat('Y-m'),
            'args' => null,
        ];
        $this->recordWithConfig($config);
        $config = [
            'page' => $truncated_page,
            'date_range' => $date->getCurrentDateInFormat('Y'),
            'args' => json_encode(['referrer' => $referrer]),
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
