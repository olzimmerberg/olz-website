<?php

namespace Olz\Command\SendDailyNotificationsCommand;

use Doctrine\Common\Collections\Criteria;
use Olz\Entity\NotificationSubscription;
use Olz\Entity\SolvEvent;
use Olz\Entity\Termine\Termin;
use Olz\Utils\WithUtilsTrait;

class MonthlyPreviewGetter {
    use WithUtilsTrait;

    public function getMonthlyPreviewNotification($args) {
        $current_weekday = intval($this->dateUtils()->getCurrentDateInFormat('N'));
        $saturday = 6;
        if ($current_weekday != $saturday) {
            return null;
        }
        $day_of_month = intval($this->dateUtils()->getCurrentDateInFormat('j'));
        $total_days_of_month = intval($this->dateUtils()->getCurrentDateInFormat('t'));
        if ($day_of_month <= $total_days_of_month - 14) {
            return null; // not yet this month
        }
        if ($day_of_month > $total_days_of_month - 7) {
            return null; // not anymore this month
        }

        $one_month = \DateInterval::createFromDateString('+1 months');
        $two_months = \DateInterval::createFromDateString('+2 months');
        $today = new \DateTime($this->dateUtils()->getIsoToday());
        $next_month = (new \DateTime($this->dateUtils()->getIsoToday()))->add($one_month);
        $in_two_months = (new \DateTime($this->dateUtils()->getIsoToday()))->add($two_months);
        $end_of_timespan = new \DateTime($in_two_months->format('Y-m-01'));

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

        $month_name = $this->dateUtils()->olzDate('MM', $next_month);
        $title = "Monatsvorschau {$month_name}";
        $text = "Hallo %%userFirstName%%,\n\nIm {$month_name} haben wir Folgendes auf dem Programm:\n\n{$notification_text}";

        return new Notification($title, $text, [
            'notification_type' => NotificationSubscription::TYPE_MONTHLY_PREVIEW,
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
            $starts_on = $termin->getStartDate();
            $ends_on = $termin->getEndDate();
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
