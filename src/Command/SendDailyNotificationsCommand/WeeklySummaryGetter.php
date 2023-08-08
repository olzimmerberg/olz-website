<?php

namespace Olz\Command\SendDailyNotificationsCommand;

use Doctrine\Common\Collections\Criteria;
use Olz\Entity\News\NewsEntry;
use Olz\Entity\NotificationSubscription;
use Olz\Entity\Termine\Termin;
use Olz\Utils\WithUtilsTrait;

class WeeklySummaryGetter {
    use WithUtilsTrait;

    public const CUT_OFF_TIME = '16:00:00';

    protected \DateTime $today;
    protected \DateTime $lastWeek;

    public function getWeeklySummaryNotification($args) {
        $current_weekday = intval($this->dateUtils()->getCurrentDateInFormat('N'));
        $monday = 1;
        if ($current_weekday != $monday) {
            return null;
        }

        $this->today = new \DateTime($this->dateUtils()->getIsoToday());
        $minus_one_week = \DateInterval::createFromDateString("-7 days");
        $this->lastWeek = (new \DateTime($this->dateUtils()->getIsoToday()))->add($minus_one_week);

        $today_at_cut_off = new \DateTime($this->today->format('Y-m-d').' '.self::CUT_OFF_TIME);
        $last_week_at_cut_off = new \DateTime($this->lastWeek->format('Y-m-d').' '.self::CUT_OFF_TIME);
        $termine_criteria = Criteria::create()
            ->where(Criteria::expr()->andX(
                Criteria::expr()->lte('last_modified_at', $today_at_cut_off),
                Criteria::expr()->gt('last_modified_at', $last_week_at_cut_off),
                Criteria::expr()->eq('newsletter', 1),
                Criteria::expr()->eq('on_off', 1),
            ))
            ->orderBy(['datum' => Criteria::ASC, 'zeit' => Criteria::ASC])
            ->setFirstResult(0)
            ->setMaxResults(1000)
        ;

        $notification_text = '';
        $base_href = $this->envUtils()->getBaseHref();
        $code_href = $this->envUtils()->getCodeHref();

        if ($args['aktuell'] ?? false) {
            $news_url = "{$base_href}{$code_href}news";
            $aktuell_text = '';
            $news_repo = $this->entityManager()->getRepository(NewsEntry::class);
            $aktuell_criteria = $this->getNewsCriteria(['aktuell']);
            $aktuells = $news_repo->matching($aktuell_criteria);
            foreach ($aktuells as $aktuell) {
                $id = $aktuell->getId();
                $pretty_datetime = $this->getPrettyDateAndMaybeTime(
                    $aktuell->getDate(), $aktuell->getTime());
                $title = $aktuell->getTitle();
                $aktuell_text .= "- {$pretty_datetime}: [{$title}]({$news_url}/{$id})\n";
            }
            if (strlen($aktuell_text) > 0) {
                $notification_text .= "\n**Aktuell**\n\n{$aktuell_text}\n";
            }
        }

        if ($args['blog'] ?? false) {
            $news_url = "{$base_href}{$code_href}news";
            $blog_text = '';
            $news_repo = $this->entityManager()->getRepository(NewsEntry::class);
            $blog_criteria = $this->getNewsCriteria(['kaderblog']);
            $blogs = $news_repo->matching($blog_criteria);
            foreach ($blogs as $blog) {
                $id = $blog->getId();
                $pretty_datetime = $this->getPrettyDateAndMaybeTime(
                    $blog->getDate(), $blog->getTime());
                $title = $blog->getTitle();
                $blog_text .= "- {$pretty_datetime}: [{$title}]({$news_url}/{$id})\n";
            }
            if (strlen($blog_text) > 0) {
                $notification_text .= "\n**Kaderblog**\n\n{$blog_text}\n";
            }
        }

        if ($args['forum'] ?? false) {
            $news_url = "{$base_href}{$code_href}news";
            $forum_text = '';
            $news_repo = $this->entityManager()->getRepository(NewsEntry::class);
            $forum_criteria = $this->getNewsCriteria(['forum']);
            $forums = $news_repo->matching($forum_criteria);
            foreach ($forums as $forum) {
                $id = $forum->getId();
                $pretty_datetime = $this->getPrettyDateAndMaybeTime(
                    $forum->getDate(), $forum->getTime());
                $title = $forum->getTitle();
                if (strlen(trim($title)) > 0) {
                    $forum_text .= "- {$pretty_datetime}: [{$title}]({$news_url}/{$id})\n";
                }
            }
            if (strlen($forum_text) > 0) {
                $notification_text .= "\n**Forum**\n\n{$forum_text}\n";
            }
        }

        if ($args['galerie'] ?? false) {
            $news_url = "{$base_href}{$code_href}news";
            $galerie_text = '';
            $news_repo = $this->entityManager()->getRepository(NewsEntry::class);
            $galerie_criteria = $this->getNewsCriteria(['galerie', 'video']);
            $galeries = $news_repo->matching($galerie_criteria);
            foreach ($galeries as $galerie) {
                $id = $galerie->getId();
                $pretty_datetime = $this->getPrettyDateAndMaybeTime(
                    $galerie->getDate(), $galerie->getTime());
                $title = $galerie->getTitle();
                $galerie_text .= "- {$pretty_datetime}: [{$title}]({$news_url}/{$id})\n";
            }
            if (strlen($galerie_text) > 0) {
                $notification_text .= "\n**Galerien**\n\n{$galerie_text}\n";
            }
        }

        if ($args['termine'] ?? false) {
            $termine_url = "{$base_href}{$code_href}termine";
            $termine_text = '';
            $termin_repo = $this->entityManager()->getRepository(Termin::class);
            $termine = $termin_repo->matching($termine_criteria);
            foreach ($termine as $termin) {
                $id = $termin->getId();
                $starts_on = $termin->getStartsOn();
                $ends_on = $termin->getEndsOn();
                $pretty_date = ($ends_on && $ends_on > $starts_on)
                    ? $starts_on->format('d.m.').' - '.$ends_on->format('d.m.')
                    : $starts_on->format('d.m.');
                $title = $termin->getTitle();
                if (strlen(trim($title)) > 0) {
                    $termine_text .= "- {$pretty_date}: [{$title}]({$termine_url}/{$id})\n";
                }
            }
            if (strlen($termine_text) > 0) {
                $notification_text .= "\n**Aktualisierte Termine**\n\n{$termine_text}\n";
            }
        }

        if (strlen($notification_text) == 0) {
            return null;
        }

        $title = "Wochenzusammenfassung";
        $text = "Hallo %%userFirstName%%,\n\nDas lief diese Woche auf [olzimmerberg.ch](https://olzimmerberg.ch):\n\n{$notification_text}";

        return new Notification($title, $text, [
            'notification_type' => NotificationSubscription::TYPE_WEEKLY_SUMMARY,
        ]);
    }

    protected function getPrettyDateAndMaybeTime($date, $time = null) {
        if (!$date) {
            return "??";
        }
        $pretty_date = $date->format('d.m.');
        if (!$time) {
            return $pretty_date;
        }
        $pretty_time = $time->format('H:i');
        return "{$pretty_date} {$pretty_time}";
    }

    protected function getNewsCriteria(array $formats) {
        return Criteria::create()
            ->where(Criteria::expr()->andX(
                // TODO: typ -> format
                Criteria::expr()->in('typ', $formats),
                Criteria::expr()->orX(
                    Criteria::expr()->andX(
                        Criteria::expr()->eq('datum', $this->today),
                        Criteria::expr()->lte('zeit', new \DateTime(self::CUT_OFF_TIME)),
                    ),
                    Criteria::expr()->andX(
                        Criteria::expr()->lt('datum', $this->today),
                        Criteria::expr()->gt('datum', $this->lastWeek),
                    ),
                    Criteria::expr()->andX(
                        Criteria::expr()->eq('datum', $this->lastWeek),
                        Criteria::expr()->gt('zeit', new \DateTime(self::CUT_OFF_TIME)),
                    ),
                ),
                Criteria::expr()->eq('on_off', 1),
            ))
            ->orderBy(['datum' => Criteria::ASC, 'zeit' => Criteria::ASC])
            ->setFirstResult(0)
            ->setMaxResults(1000)
        ;
    }
}
