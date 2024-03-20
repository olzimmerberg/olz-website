<?php

namespace Olz\Repository;

use Olz\Entity\Throttling;
use Olz\Repository\Common\OlzRepository;

class ThrottlingRepository extends OlzRepository {
    public function getLastOccurrenceOf($event_name) {
        $throttling = $this->findOneBy(['event_name' => $event_name]);
        if (!$throttling) {
            return null;
        }
        return $throttling->getLastOccurrence();
    }

    public function recordOccurrenceOf($event_name, $datetime) {
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
