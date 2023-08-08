<?php

namespace Olz\Command\SendDailyNotificationsCommand;

use Doctrine\Common\Collections\Criteria;
use Olz\Entity\NotificationSubscription;
use Olz\Entity\SolvEvent;
use Olz\Entity\Termine\Termin;
use Olz\Utils\WithUtilsTrait;

class WeeklyPreviewGetter {
    use WithUtilsTrait;

    public function getWeeklyPreviewNotification() {
        $current_weekday = intval($this->dateUtils()->getCurrentDateInFormat('N'));
        $thursday = 4;
        if ($current_weekday != $thursday) {
            return null;
        }

        $four_days = \DateInterval::createFromDateString('+4 days');
        $eleven_days = \DateInterval::createFromDateString('+11 days');
        $today = new \DateTime($this->dateUtils()->getIsoToday());
        $next_monday = (new \DateTime($this->dateUtils()->getIsoToday()))->add($four_days);
        $end_of_timespan = (new \DateTime($this->dateUtils()->getIsoToday()))->add($eleven_days);

        $notification_text = '';
        $termine_text = $this->getTermineText($today, $end_of_timespan);
        if (strlen($termine_text) > 0) {
            $notification_text .= "\n**Termine**\n\n{$termine_text}\n";
        }
        $deadlines_text = $this->getDeadlinesText($today, $end_of_timespan);
        if (strlen($deadlines_text) > 0) {
            $notification_text .= "\n**Meldeschlüsse**\n\n{$deadlines_text}\n";
        }

        if (strlen($notification_text) == 0) {
            return null;
        }

        $next_monday_text = $this->dateUtils()->olzDate('t. MM', $next_monday);
        $title = "Vorschau auf die Woche vom {$next_monday_text}";
        $text = "Hallo %%userFirstName%%,\n\nBis Ende nächster Woche haben wir Folgendes auf dem Programm:\n\n{$notification_text}";

        return new Notification($title, $text, [
            'notification_type' => NotificationSubscription::TYPE_WEEKLY_PREVIEW,
        ]);
    }

    public function getTermineText($today, $end_of_timespan) {
        $termin_repo = $this->entityManager()->getRepository(Termin::class);
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

        $base_href = $this->envUtils()->getBaseHref();
        $code_href = $this->envUtils()->getCodeHref();

        $termine_url = "{$base_href}{$code_href}termine";
        $termine_text = "";
        foreach ($termine as $termin) {
            $id = $termin->getId();
            $starts_on = $termin->getStartsOn();
            $ends_on = $termin->getEndsOn();
            $date = ($ends_on && $ends_on > $starts_on)
                ? $starts_on->format('d.m.').' - '.$ends_on->format('d.m.')
                : $starts_on->format('d.m.');
            $title = $termin->getTitle();
            $termine_text .= "- {$date}: [{$title}]({$termine_url}/{$id})\n";
        }
        return $termine_text;
    }

    public function getDeadlinesText($today, $end_of_timespan) {
        $termin_repo = $this->entityManager()->getRepository(Termin::class);
        $solv_event_repo = $this->entityManager()->getRepository(SolvEvent::class);

        $base_href = $this->envUtils()->getBaseHref();
        $code_href = $this->envUtils()->getCodeHref();
        $termine_url = "{$base_href}{$code_href}termine";

        $deadlines_text = '';

        // SOLV-Meldeschlüsse
        $criteria = Criteria::create()
            ->where(Criteria::expr()->andX(
                Criteria::expr()->gt('deadline', $today),
                Criteria::expr()->lt('deadline', $end_of_timespan),
            ))
            ->orderBy(['deadline' => Criteria::ASC])
            ->setFirstResult(0)
            ->setMaxResults(1000)
        ;
        $deadlines = $solv_event_repo->matching($criteria);
        foreach ($deadlines as $deadline) {
            $solv_uid = $deadline->getSolvUid();
            $termin = $termin_repo->findOneBy([
                'solv_uid' => $solv_uid,
                'on_off' => 1,
            ]);
            if (!$termin) {
                continue;
            }
            $deadline_date = $deadline->getDeadline();
            $date = $deadline_date->format('d.m.');
            $id = $termin->getId();
            $title = $termin->getTitle();
            $deadlines_text .= "- {$date}: Meldeschluss für '[{$title}]({$termine_url}/{$id})'\n";
        }

        // OLZ-Meldeschlüsse
        $criteria = Criteria::create()
            ->where(
                Criteria::expr()->andX(
                    Criteria::expr()->gt('deadline', $today),
                    Criteria::expr()->lt('deadline', $end_of_timespan),
                    Criteria::expr()->eq('on_off', 1),
                )
            )
            ->orderBy(['datum' => Criteria::ASC])
            ->setFirstResult(0)
            ->setMaxResults(1000)
        ;
        $deadlines = $termin_repo->matching($criteria);
        foreach ($deadlines as $termin) {
            if (!$termin) {
                continue;
            }
            $deadline_date = $termin->getDeadline();
            $date = $deadline_date->format('d.m.');
            $id = $termin->getId();
            $title = $termin->getTitle();
            $deadlines_text .= "- {$date}: Meldeschluss für '[{$title}]({$termine_url}/{$id})'\n";
        }

        return $deadlines_text;
    }
}
