<?php

use Doctrine\Common\Collections\Criteria;

require_once __DIR__.'/Notification.php';
require_once __DIR__.'/../../model/NotificationSubscription.php';
require_once __DIR__.'/../../model/Termin.php';

class MonthlyPreviewGetter {
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

    public function getMonthlyPreviewNotification($args) {
        $current_weekday = intval($this->dateUtils->getCurrentDateInFormat('N'));
        $saturday = 6;
        if ($current_weekday != $saturday) {
            return null;
        }
        $day_of_month = intval($this->dateUtils->getCurrentDateInFormat('j'));
        $total_days_of_month = intval($this->dateUtils->getCurrentDateInFormat('t'));
        if ($day_of_month <= $total_days_of_month - 14) {
            return null; // not yet this month
        }
        if ($day_of_month > $total_days_of_month - 7) {
            return null; // not anymore this month
        }

        $one_month = DateInterval::createFromDateString('+1 months');
        $two_months = DateInterval::createFromDateString('+2 months');
        $today = new DateTime($this->dateUtils->getIsoToday());
        $next_month = (new DateTime($this->dateUtils->getIsoToday()))->add($one_month);
        $in_two_months = (new DateTime($this->dateUtils->getIsoToday()))->add($two_months);
        $end_of_timespan = new DateTime($in_two_months->format('Y-m-01'));

        $termin_repo = $this->entityManager->getRepository(Termin::class);
        $criteria = Criteria::create()
            ->where(Criteria::expr()->andX(
                Criteria::expr()->gt('datum', $today),
                Criteria::expr()->lt('datum', $end_of_timespan),
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

        $month_name = $next_month->format('F');
        $title = "Monatsvorschau {$month_name}";
        $text = "Hallo %%userFirstName%%,\n\nIm {$month_name} finden folgende Anlässe statt:\n\n{$termine_text}";

        return new Notification($title, $text, [
            'notification_type' => NotificationSubscription::TYPE_MONTHLY_PREVIEW,
        ]);
    }
}
