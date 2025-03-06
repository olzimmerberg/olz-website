<?php

// =============================================================================
// Zeigt eine Startseiten-Kachel mit den nächsten Meldeschlüssen an.
// =============================================================================

namespace Olz\Startseite\Components\OlzTermineDeadlinesTile;

use Olz\Entity\Users\User;
use Olz\Startseite\Components\AbstractOlzTile\AbstractOlzTile;

class OlzTermineDeadlinesTile extends AbstractOlzTile {
    private string $in_three_days;
    private string $in_one_week;
    private string $in_two_weeks;

    public function getRelevance(?User $user): float {
        return 0.75;
    }

    public function getHtml(mixed $args): string {
        $db = $this->dbUtils()->getDb();
        $date_utils = $this->dateUtils();
        $today = $date_utils->getIsoToday();
        $now = $date_utils->getIsoNow();
        $plus_three_days = \DateInterval::createFromDateString("+3 days");
        $plus_one_week = \DateInterval::createFromDateString("+7 days");
        $plus_two_weeks = \DateInterval::createFromDateString("+14 days");
        $this->in_three_days = (new \DateTime($today))->add($plus_three_days)->format('Y-m-d');
        $this->in_one_week = (new \DateTime($today))->add($plus_one_week)->format('Y-m-d');
        $this->in_two_weeks = (new \DateTime($today))->add($plus_two_weeks)->format('Y-m-d');

        $out = "<h3>Meldeschlüsse</h3>";

        $res = $db->query(<<<ZZZZZZZZZZ
            SELECT
                DATE(t.deadline) as deadline,
                t.start_date as date,
                t.title as title,
                t.id as id,
                t.image_ids as image_ids
            FROM termine t
            WHERE
                t.deadline IS NOT NULL
                AND t.deadline >= '{$now}'
                AND t.deadline <= '{$this->in_two_weeks}'
            ORDER BY deadline ASC
            ZZZZZZZZZZ);
        // @phpstan-ignore-next-line
        if ($res->num_rows > 0) {
            $out .= "<ul class='links'>";
            // @phpstan-ignore-next-line
            while ($row = $res->fetch_assoc()) {
                $out .= $this->getDeadlineHtml($row);
            }
            $out .= "</ul>";
        } else {
            $out .= "<br /><center><i>Keine Meldeschlüsse in den nächsten drei Wochen</i></center>";
        }

        // Outlook
        $res = $db->query(<<<ZZZZZZZZZZ
            SELECT
                DATE(t.deadline) as deadline,
                t.start_date as date,
                t.title as title,
                t.id as id,
                t.image_ids as image_ids
            FROM termine t
            WHERE
                t.deadline IS NOT NULL
                AND t.deadline > '{$this->in_two_weeks}'
                AND t.should_promote != '0'
                AND t.image_ids IS NOT NULL
                AND t.image_ids != '[]'
            ORDER BY deadline ASC
            LIMIT 7
            ZZZZZZZZZZ);
        // @phpstan-ignore-next-line
        if ($res->num_rows > 0) {
            $out .= "<hr/>";
            $out .= "<ul class='links'>";
            // @phpstan-ignore-next-line
            while ($row = $res->fetch_assoc()) {
                $out .= $this->getDeadlineHtml($row);
            }
            $out .= "</ul>";
        }
        return $out;
    }

    /**
     * @param array<string, mixed> $row
     */
    protected function getDeadlineHtml(array $row): string {
        $code_href = $this->envUtils()->getCodeHref();

        $id = $row['id'];
        $deadline = $this->dateUtils()->compactDate($row['deadline']);
        $date = $this->dateUtils()->compactDate($row['date']);
        $title = $row['title'];
        $urgency = 'full';
        if ($row['deadline'] <= $this->in_three_days) {
            $urgency = 'empty';
        } elseif ($row['deadline'] <= $this->in_one_week) {
            $urgency = 'mid';
        }
        $image_ids = json_decode($row['image_ids'], true);
        $image_id = count($image_ids ?? []) > 0 ? $image_ids[0] : null;
        $icon_color = $image_id ? '_white' : '';
        $icon_basename = "termine_type_meldeschluss_{$urgency}{$icon_color}_20.svg";
        $icon = "{$code_href}assets/icns/{$icon_basename}";
        $icon_img = "<img src='{$icon}' alt='' class='link-icon'>";
        if ($image_id) {
            $image = $this->imageUtils()->olzImage(
                'termine',
                $id,
                $image_id,
                128,
                null,
                ' class="noborder"'
            );
            return <<<ZZZZZZZZZZ
                    <li class='flex deadline-image min-two-lines'>
                        <a href='{$code_href}termine/{$id}?von=startseite'>
                            <div class='overlay'>
                                {$icon_img}
                                <span class='date'>{$deadline}</span>
                                <span class='title'>Für {$title} vom {$date}</span>
                            </div>
                            <div class='blurry-image'>{$image}</div>
                            <div class='sharp-image'>{$image}</div>
                        </a>
                    </li>
                ZZZZZZZZZZ;
        }
        return <<<ZZZZZZZZZZ
                <li class='flex'>
                    {$icon_img}
                    <a href='{$code_href}termine/{$id}?von=startseite'>
                        <b>{$deadline}</b>: Für {$title} vom {$date}
                    </a>
                </li>
            ZZZZZZZZZZ;
    }
}
