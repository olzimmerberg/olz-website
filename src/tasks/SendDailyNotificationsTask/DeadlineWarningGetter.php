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
        $days_arg = intval($args['days'] ?? '');
        if ($days_arg <= 0 || $days_arg > 7) {
            return null;
        }
        $given_days = DateInterval::createFromDateString("+{$days_arg} days");
        $in_given_days = (new DateTime($this->dateUtils->getIsoToday()))->add($given_days);

        $termin_repo = $this->entityManager->getRepository(Termin::class);
        $solv_event_repo = $this->entityManager->getRepository(SolvEvent::class);
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('deadline', $in_given_days))
            ->orderBy(['datum' => Criteria::ASC])
            ->setFirstResult(0)
            ->setMaxResults(1000)
        ;
        $deadlines = $solv_event_repo->matching($criteria);
        $deadlines_text = '';
        foreach ($deadlines as $deadline) {
            $solv_uid = $deadline->getSolvUid();
            $termin = $termin_repo->findOneBy(['solv_uid' => $solv_uid]);
            if (!$termin) {
                continue;
            }
            $deadline_date = $deadline->getDeadline();
            $date = $deadline_date->format('d.m.');
            $title = $termin->getTitle();
            $deadlines_text .= "{$date}: Meldeschluss für '{$title}'\n";
        }

        if (strlen($deadlines_text) == 0) {
            return null;
        }

        $title = "Meldeschlusswarnung";
        $text = "Hallo %%userFirstName%%,\n\nAchtung:\n\n{$deadlines_text}";

        return new Notification($title, $text);
    }
}
