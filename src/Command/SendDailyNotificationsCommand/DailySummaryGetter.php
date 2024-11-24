<?php

namespace Olz\Command\SendDailyNotificationsCommand;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Order;
use Olz\Entity\News\NewsEntry;
use Olz\Entity\NotificationSubscription;
use Olz\Entity\Termine\Termin;
use Olz\Utils\WithUtilsTrait;

class DailySummaryGetter implements NotificationGetterInterface {
    use WithUtilsTrait;

    public const CUT_OFF_TIME = '16:00:00';

    protected \DateTime $today;
    protected \DateTime $yesterday;

    /** @param array<string, mixed> $args */
    public function getNotification(array $args): ?Notification {
        $this->today = new \DateTime($this->dateUtils()->getIsoToday());
        $minus_one_day = \DateInterval::createFromDateString("-1 days");
        $this->yesterday = (new \DateTime($this->dateUtils()->getIsoToday()))->add($minus_one_day);

        $today_at_cut_off = new \DateTime($this->today->format('Y-m-d').' '.self::CUT_OFF_TIME);
        $yesterday_at_cut_off = new \DateTime($this->yesterday->format('Y-m-d').' '.self::CUT_OFF_TIME);
        $termine_criteria = Criteria::create()
            ->where(Criteria::expr()->andX(
                Criteria::expr()->lte('last_modified_at', $today_at_cut_off),
                Criteria::expr()->gt('last_modified_at', $yesterday_at_cut_off),
                Criteria::expr()->eq('newsletter', 1),
                Criteria::expr()->eq('on_off', 1),
            ))
            ->orderBy(['start_date' => Order::Ascending, 'start_time' => Order::Ascending])
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
                    $aktuell->getPublishedDate(),
                    $aktuell->getPublishedTime()
                );
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
                    $blog->getPublishedDate(),
                    $blog->getPublishedTime()
                );
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
                    $forum->getPublishedDate(),
                    $forum->getPublishedTime()
                );
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
                    $galerie->getPublishedDate(),
                    $galerie->getPublishedTime()
                );
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
                $starts_on = $termin->getStartDate();
                $ends_on = $termin->getEndDate();
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

        $title = "Tageszusammenfassung";
        $text = "Hallo %%userFirstName%%,\n\nDas lief heute auf [olzimmerberg.ch](https://olzimmerberg.ch):\n\n{$notification_text}";

        return new Notification($title, $text, [
            'notification_type' => NotificationSubscription::TYPE_DAILY_SUMMARY,
        ]);
    }

    protected function getPrettyDateAndMaybeTime(?\DateTime $date, ?\DateTime $time = null): string {
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

    /** @param array<string> $formats */
    protected function getNewsCriteria(array $formats): Criteria {
        return Criteria::create()
            ->where(Criteria::expr()->andX(
                Criteria::expr()->in('format', $formats),
                Criteria::expr()->orX(
                    Criteria::expr()->andX(
                        Criteria::expr()->eq('published_date', $this->today),
                        Criteria::expr()->lte('published_time', new \DateTime(self::CUT_OFF_TIME)),
                    ),
                    Criteria::expr()->andX(
                        Criteria::expr()->eq('published_date', $this->yesterday),
                        Criteria::expr()->gt('published_time', new \DateTime(self::CUT_OFF_TIME)),
                    ),
                ),
                Criteria::expr()->eq('on_off', 1),
            ))
            ->orderBy(['published_date' => Order::Ascending, 'published_time' => Order::Ascending])
            ->setFirstResult(0)
            ->setMaxResults(1000)
        ;
    }
}
