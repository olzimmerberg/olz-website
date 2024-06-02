<?php

namespace Olz\Repository;

use Olz\Entity\Throttling;
use Olz\Repository\Common\OlzRepository;

/**
 * @extends OlzRepository<Throttling>
 */
class ThrottlingRepository extends OlzRepository {
    public function getLastOccurrenceOf(string $event_name): ?\DateTime {
        $throttling = $this->findOneBy(['event_name' => $event_name]);
        if (!$throttling) {
            return null;
        }
        return $throttling->getLastOccurrence();
    }

    public function recordOccurrenceOf(string $event_name, \DateTime|string $datetime): void {
        $throttling = $this->findOneBy(['event_name' => $event_name]);
        if (!$throttling) {
            $throttling = new Throttling();
            $throttling->setEventName($event_name);
            $this->_em->persist($throttling);
        }
        $sane_datetime = is_string($datetime) ? new \DateTime($datetime) : $datetime;
        $throttling->setLastOccurrence($sane_datetime);
        $this->_em->flush();
    }
}
