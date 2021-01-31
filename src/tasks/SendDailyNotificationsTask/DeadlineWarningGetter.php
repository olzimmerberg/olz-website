<?php

use Doctrine\Common\Collections\Criteria;

require_once __DIR__.'/Notification.php';
require_once __DIR__.'/../../model/Termin.php';

class DeadlineWarningGetter {
    use Psr\Log\LoggerAwareTrait;

    public function setEntityManager($entityManager) {
        $this->entityManager = $entityManager;
    }

    public function setDateUtils($dateUtils) {
        $this->dateUtils = $dateUtils;
    }

    public function getDeadlineWarningNotification($args) {
        $seven_days = DateInterval::createFromDateString('+7 days');
        $today = new DateTime($this->dateUtils->getIsoToday());
        $in_seven_days = (new DateTime($this->dateUtils->getIsoToday()))->add($seven_days);

        $termin_repo = $this->entityManager->getRepository(Termin::class);
        $criteria = Criteria::create()
            ->where(Criteria::expr()->andX(
                Criteria::expr()->gt('datum', $today),
                Criteria::expr()->lte('datum', $in_seven_days),
            ))
            ->orderBy(['datum' => Criteria::ASC])
            ->setFirstResult(0)
            ->setMaxResults(1000)
        ;
        $termine = $termin_repo->matching($criteria);
        $termine_text = "";
        foreach ($termine as $termin) {
            $starts_on = $termin->getStartsOn();
            $ends_on = $termin->getEndsOn();
            $date = ($ends_on && $ends_on > $starts_on)
                ? $starts_on->format('d.m.').' - '.$ends_on->format('d.m.')
                : $starts_on->format('d.m.');
            $title = $termin->getTitle();
            $termine_text .= "{$date}: {$title}\n";
        }

        $title = "Meldeschlusswarnung";
        $text = "Hallo %%userFirstName%%,\n\nAchtung:\n\n{$termine_text}";

        return new Notification($title, $text);
    }
}
