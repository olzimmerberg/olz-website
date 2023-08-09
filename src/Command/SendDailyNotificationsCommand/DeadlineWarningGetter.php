<?php

namespace Olz\Command\SendDailyNotificationsCommand;

use Doctrine\Common\Collections\Criteria;
use Olz\Entity\NotificationSubscription;
use Olz\Entity\SolvEvent;
use Olz\Entity\Termine\Termin;
use Olz\Utils\WithUtilsTrait;

class DeadlineWarningGetter {
    use WithUtilsTrait;

    public function getDeadlineWarningNotification($args) {
        $days_arg = intval($args['days'] ?? '');
        if ($days_arg <= 0 || $days_arg > 7) {
            return null;
        }
        $given_days = \DateInterval::createFromDateString("+{$days_arg} days");
        $in_given_days = (new \DateTime($this->dateUtils()->getIsoToday()))->add($given_days);
        $in_given_days_start = new \DateTime($in_given_days->format('Y-m-d').' 00:00:00');
        $in_given_days_end = new \DateTime($in_given_days->format('Y-m-d').' 23:59:59');

        $termin_repo = $this->entityManager()->getRepository(Termin::class);
        $solv_event_repo = $this->entityManager()->getRepository(SolvEvent::class);

        $base_href = $this->envUtils()->getBaseHref();
        $code_href = $this->envUtils()->getCodeHref();
        $termine_url = "{$base_href}{$code_href}termine";

        $deadlines_text = '';

        // SOLV-Meldeschlüsse
        $criteria = Criteria::create()
            ->where(Criteria::expr()->andX(
                Criteria::expr()->gte('deadline', $in_given_days_start),
                Criteria::expr()->lte('deadline', $in_given_days_end),
            ))
            ->orderBy(['date' => Criteria::ASC])
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
                    Criteria::expr()->gte('deadline', $in_given_days_start),
                    Criteria::expr()->lte('deadline', $in_given_days_end),
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

        if (strlen($deadlines_text) == 0) {
            return null;
        }

        $title = "Meldeschlusswarnung";
        $text = "Hallo %%userFirstName%%,\n\nFolgende Meldeschlüsse stehen bevor:\n\n{$deadlines_text}";

        return new Notification($title, $text, [
            'notification_type' => NotificationSubscription::TYPE_DEADLINE_WARNING,
        ]);
    }
}
