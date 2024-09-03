<?php

namespace Olz\Termine\Components\OlzTerminTemplatesListItem;

use Olz\Components\Common\OlzComponent;
use Olz\Entity\Termine\TerminLabel;
use Olz\Entity\Termine\TerminLocation;
use Olz\Termine\Components\OlzDateCalendar\OlzDateCalendar;

class OlzTerminTemplatesListItem extends OlzComponent {
    /** @var array<string, string> */
    protected static $iconBasenameByType = [
        'programm' => 'termine_type_programm_20.svg',
        'weekend' => 'termine_type_weekend_20.svg',
        'ol' => 'termine_type_ol_20.svg',
        'training' => 'termine_type_training_20.svg',
        'club' => 'termine_type_club_20.svg',
        'meldeschluss' => 'termine_type_meldeschluss_20.svg',
    ];

    /** @param array<string, mixed> $args */
    public function getHtml(array $args = []): string {
        $db = $this->dbUtils()->getDb();
        $code_href = $this->envUtils()->getCodeHref();
        $code_path = $this->envUtils()->getCodePath();
        $termin_location_repo = $this->entityManager()->getRepository(TerminLocation::class);

        $out = '';
        $enc_current_filter = urlencode($_GET['filter'] ?? '{}');

        $termin_template = $args['termin_template'];
        $id = $termin_template->getId();
        $start_time = $termin_template->getStartTime();
        $duration_seconds = $termin_template->getDurationSeconds();
        $title = $termin_template->getTitle();
        $text = $termin_template->getText();
        $labels = [...$termin_template->getLabels()];
        $termin_location = $termin_template->getLocation();

        $link = "{$code_href}termin_vorlagen/{$id}?filter={$enc_current_filter}";
        $type_imgs = implode('', array_map(function (TerminLabel $label) use ($code_path, $code_href) {
            $ident = $label->getIdent();
            // TODO: Remove fallback mechanism?
            $fallback_path = "{$code_path}assets/icns/termine_type_{$ident}_20.svg";
            $fallback_href = is_file($fallback_path)
                ? "{$code_href}assets/icns/termine_type_{$ident}_20.svg" : null;
            $icon_href = $label->getIcon() ? $label->getFileHref($label->getIcon()) : $fallback_href;
            return $icon_href ? "<img src='{$icon_href}' alt='' class='type-icon'>" : '';
        }, $labels));

        $duration_seconds_or_zero = $duration_seconds ?? 0;
        $duration_interval = \DateInterval::createFromDateString(
            "+{$duration_seconds_or_zero} seconds"
        );
        $start_time_or_midnight = $start_time ? (clone $start_time) : new \DateTime('00:00:00');
        $end_time = $duration_seconds
            ? $start_time_or_midnight->add($duration_interval) : null;
        $end_time_text = $this->getTimeText($end_time);
        $day_diff = ($start_time && $end_time)
            ? intval($end_time->format('d')) - intval($start_time->format('d')) : null;
        $start_icon = OlzDateCalendar::render([
            'weekday' => '',
            'day' => 'X',
            'month' => '',
            'size' => 'S',
        ]);
        $end_icon = ($day_diff > 0)
            ? ' &ndash; '.OlzDateCalendar::render([
                'weekday' => '',
                'day' => "X+{$day_diff}",
                'month' => '',
                'size' => 'S',
            ])
            : null;
        $start_time_text = $this->getTimeText($start_time);

        $time_text = $start_time_text ? (
            $end_time_text
                ? "{$start_time_text} &ndash; {$end_time_text}"
                : "{$start_time_text}"
        ) : (
            $end_time_text
                ? "? +{$end_time_text}"
                : ''
        );
        $text = $this->htmlUtils()->renderMarkdown($text ?? '');
        $text = $termin_template->replaceImagePaths($text);
        $text = $termin_template->replaceFilePaths($text);
        if ($termin_location) {
            $location = $termin_location_repo->findOneBy(['id' => $termin_location->getId()]);
            $text = "<a href='{$code_href}termin_orte/{$location->getIdent()}?filter={$enc_current_filter}' class='linkmap'>{$location->getName()}</a> {$text}";
        }

        $out .= <<<ZZZZZZZZZZ
            <div class='olz-termin-templates-list-item'>
                <a class='link' href='{$link}'></a>
                <div class='content'>
                    <div class='date'>
                        <div class='date-calendars'>{$start_icon}{$end_icon}</div>
                        <div class='time-text'>{$time_text}</div>
                    </div>
                    <div class='title-text-container'>
                        <div class='title'>{$title} {$type_imgs}</div>
                        <div class='text'>{$text}</div>
                    </div>
                </div>
            </div>
            ZZZZZZZZZZ;
        return $out;
    }

    protected function getTimeText(?\DateTime $datetime_time): ?string {
        if (!$datetime_time) {
            return null;
        }
        return $datetime_time->format('H:i');
    }
}
