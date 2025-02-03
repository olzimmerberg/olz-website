<?php

namespace Olz\Termine\Utils;

use Olz\Entity\SolvEvent;
use Olz\Entity\Termine\Termin;
use Olz\Utils\WithUtilsTrait;

class TermineUtils {
    use WithUtilsTrait;

    public function updateTerminFromSolvEvent(Termin $termin, ?SolvEvent $solv_event_arg = null): void {
        $solv_id = $termin->getSolvId();
        if (!$solv_id) {
            $this->log()->warning("Update termin {$termin->getId()} from SOLV: no SOLV ID.");
            return;
        }
        $solv_event = $solv_event_arg;
        if ($solv_event_arg === null) {
            $solv_event_repo = $this->entityManager()->getRepository(SolvEvent::class);
            $solv_event = $solv_event_repo->findOneBy(['solv_uid' => $solv_id]);
        } else {
            if ($solv_id !== $solv_event_arg->getSolvUid()) {
                $this->log()->warning("Update termin {$termin->getId()} from SOLV: SOLV ID mismatch ({$solv_id} vs. {$solv_event_arg->getSolvUid()}).");
                return;
            }
        }

        $duration_days = $solv_event->getDuration() - 1;
        $duration = \DateInterval::createFromDateString("{$duration_days} days");
        $end_date = (clone $solv_event->getDate())->add($duration);
        $deadline = $solv_event->getDeadline()
            ? (clone $solv_event->getDeadline())->setTime(23, 59, 59) : null;
        $link = $solv_event->getLink() ?: '-';
        $club = $solv_event->getClub() ?: '-';
        $map = $solv_event->getMap() ?: '-';
        $location = $solv_event->getLocation() ?: '-';
        $text = <<<ZZZZZZZZZZ
            Link: {$link}

            Organisator: {$club}

            Karte: {$map}

            Ort: {$location}
            ZZZZZZZZZZ;

        $termin->setStartDate($solv_event->getDate());
        $termin->setStartTime(null);
        $termin->setEndDate($end_date);
        $termin->setEndTime(null);
        $termin->setDeadline($deadline);
        $termin->setTitle($solv_event->getName());
        $termin->setText($text);
        $termin->setNewsletter(false); // TODO: Enable Newsletter for SOLV Termine
        $termin->setLocation(null);
        $termin->setCoordinateX($solv_event->getCoordX());
        $termin->setCoordinateY($solv_event->getCoordY());
        $this->log()->info("Termin {$termin->getId()} updated from SOLV.");
    }

    public static function fromEnv(): self {
        return new self();
    }
}
