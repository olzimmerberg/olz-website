<?php

use Doctrine\Common\Collections\Criteria;

require_once __DIR__.'/Notification.php';
require_once __DIR__.'/../../model/NotificationSubscription.php';
require_once __DIR__.'/../../model/Termin.php';

class WeeklyPreviewGetter {
    use Psr\Log\LoggerAwareTrait;

    public function setEntityManager($entityManager) {
        $this->entityManager = $entityManager;
    }

    public function setDateUtils($dateUtils) {
        $this->dateUtils = $dateUtils;
    }

    public function setEnvUtils($envUtils) {
        $this->envUtils = $envUtils;
    }

    public function getWeeklyPreviewNotification() {
        $current_weekday = intval($this->dateUtils->getCurrentDateInFormat('N'));
        $thursday = 4;
        if ($current_weekday != $thursday) {
            return null;
        }

        $four_days = DateInterval::createFromDateString('+4 days');
        $eleven_days = DateInterval::createFromDateString('+11 days');
        $today = new DateTime($this->dateUtils->getIsoToday());
        $next_monday = (new DateTime($this->dateUtils->getIsoToday()))->add($four_days);
        $end_of_timespan = (new DateTime($this->dateUtils->getIsoToday()))->add($eleven_days);

        $termin_repo = $this->entityManager->getRepository(Termin::class);
        $criteria = Criteria::create()
            ->where(Criteria::expr()->andX(
                Criteria::expr()->gt('datum', $today),
                Criteria::expr()->lt('datum', $end_of_timespan),
                Criteria::expr()->eq('on_off', 1),
            ))
            ->orderBy(['datum' => Criteria::ASC])
            ->setFirstResult(0)
            ->setMaxResults(1000)
        ;
        $termine = $termin_repo->matching($criteria);

        $base_href = $this->envUtils->getBaseHref();
        $code_href = $this->envUtils->getCodeHref();

        $termine_url = "{$base_href}{$code_href}termine.php";
        $termine_text = "";
        foreach ($termine as $termin) {
            $id = $termin->getId();
            $starts_on = $termin->getStartsOn();
            $ends_on = $termin->getEndsOn();
            $date = ($ends_on && $ends_on > $starts_on)
                ? $starts_on->format('d.m.').' - '.$ends_on->format('d.m.')
                : $starts_on->format('d.m.');
            $title = $termin->getTitle();
            $termine_text .= "- {$date}: [{$title}]({$termine_url}#id{$id})\n";
        }

        $next_monday_text = $this->dateUtils->olzDate('t. MM', $next_monday);
        $title = "Vorschau auf die Woche vom {$next_monday_text}";
        $text = "Hallo %%userFirstName%%,\n\nBis Ende nächster Woche finden folgende Anlässe statt:\n\n{$termine_text}";

        return new Notification($title, $text, [
            'notification_type' => NotificationSubscription::TYPE_WEEKLY_PREVIEW,
        ]);
    }
}
