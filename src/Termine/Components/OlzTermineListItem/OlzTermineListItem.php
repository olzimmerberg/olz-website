<?php

namespace Olz\Termine\Components\OlzTermineListItem;

use Olz\Components\Common\OlzComponent;
use Olz\Entity\Termine\TerminLabel;
use Olz\Termine\Components\OlzDateCalendar\OlzDateCalendar;
use Olz\Termine\Utils\TermineFilterUtils;

/** @extends OlzComponent<array<string, mixed>> */
class OlzTermineListItem extends OlzComponent {
    /** @var array<string, string> */
    protected static $iconBasenameByType = [
        'programm' => 'termine_type_programm_20.svg',
        'weekend' => 'termine_type_weekend_20.svg',
        'ol' => 'termine_type_ol_20.svg',
        'training' => 'termine_type_training_20.svg',
        'club' => 'termine_type_club_20.svg',
        'meldeschluss' => 'termine_type_meldeschluss_20.svg',
    ];

    public function getHtml(mixed $args): string {
        $db = $this->dbUtils()->getDb();
        $code_path = $this->envUtils()->getCodePath();
        $code_href = $this->envUtils()->getCodeHref();
        $termine_utils = TermineFilterUtils::fromEnv()->loadTypeOptions();

        $out = '';
        $current_filter = json_decode($_GET['filter'] ?? '{}', true);
        $filter_arg = '';
        if ($current_filter !== $termine_utils->getDefaultFilter()) {
            $enc_current_filter = urlencode($_GET['filter'] ?? '{}');
            $filter_arg = "?filter={$enc_current_filter}";
        }

        $id = $args['id'];
        $owner_user_id = $args['owner_user_id'];
        $start_date = $args['start_date'];
        $start_time = $args['start_time'];
        $end_date = $args['end_date'];
        $end_time = $args['end_time'];
        $title = $args['title'];
        $text = $args['text'];
        $labels = $args['labels'];
        $termin_location_id = $args['location_id'];
        $image_ids = $args['image_ids'];
        $is_deadline = count($labels) > 0 && $labels[0]->getIdent() === 'meldeschluss';

        $link = "{$code_href}termine/{$id}{$filter_arg}";
        $type_imgs = implode('', array_map(function (TerminLabel $label) use ($code_path, $code_href) {
            $ident = $label->getIdent();
            // TODO: Remove fallback mechanism?
            $fallback_path = "{$code_path}assets/icns/termine_type_{$ident}_20.svg";
            $fallback_href = is_file($fallback_path)
                ? "{$code_href}assets/icns/termine_type_{$ident}_20.svg" : null;
            $icon_href = $label->getIcon() ? $label->getFileHref($label->getIcon()) : $fallback_href;
            return $icon_href ? "<img src='{$icon_href}' alt='' class='type-icon'>" : '';
        }, $labels));
        $start_icon = OlzDateCalendar::render([
            'date' => $start_date,
            'size' => 'S',
        ]);
        $end_icon = ($end_date && $end_date !== $start_date)
            ? ' &ndash; '.OlzDateCalendar::render([
                'date' => $end_date,
                'size' => 'S',
            ])
            : null;
        $start_time_text = $this->getTimeText($start_time);
        if ($is_deadline && $start_time_text === '23:59') {
            $start_time_text = null;
        }
        $end_time_text = $this->getTimeText($end_time);
        $time_text = $start_time_text ? (
            $end_time_text
                ? "{$start_time_text} &ndash; {$end_time_text}"
                : "{$start_time_text}"
        ) : null;
        if ($termin_location_id) {
            $sane_termin_location_id = intval($termin_location_id);
            $result_location = $db->query("SELECT name FROM termin_locations WHERE id='{$sane_termin_location_id}'");
            // @phpstan-ignore-next-line
            $row_location = $result_location->fetch_assoc();
            $location_name = $row_location['name'] ?? null;
            $text = "{$location_name} {$text}";
        }
        $text = strip_tags($this->htmlUtils()->renderMarkdown($text));
        $image = '';
        if (count($image_ids ?? []) > 0) {
            $image = $this->imageUtils()->olzImage(
                'termine',
                $id,
                $image_ids[0],
                64,
                'image'
            );
        }

        $user = $this->authUtils()->getCurrentUser();
        $is_owner = $user && $owner_user_id && intval($owner_user_id) === intval($user->getId());
        $has_all_permissions = $this->authUtils()->hasPermission('all');
        $can_edit = $is_owner || $has_all_permissions;
        $edit_admin = '';
        if ($can_edit) {
            $json_id = json_encode($id);
            $edit_admin = <<<ZZZZZZZZZZ
                <button
                    class='btn btn-secondary-outline btn-sm edit-termin-list-button'
                    onclick='return olz.termineListItemEditTermin({$json_id})'
                >
                    <img src='{$code_href}assets/icns/edit_16.svg' class='noborder' />
                </button>
                ZZZZZZZZZZ;
        }

        $out .= <<<ZZZZZZZZZZ
            <div class='olz-termine-list-item'>
                <a class='link' href='{$link}'></a>
                <div class='content'>
                    <div class='date-container'>
                        <div class='date-calendars'>{$start_icon}{$end_icon}</div>
                        <div class='time-text'>{$time_text}</div>
                    </div>
                    <div class='title-text-container'>
                        <div class='title'>{$title}{$edit_admin} {$type_imgs}</div>
                        <div class='text'>{$text}</div>
                    </div>
                    <div class='image-container'>
                        {$image}
                    </div>
                </div>
            </div>
            ZZZZZZZZZZ;
        return $out;
    }

    protected function getTimeText(?string $iso_time): ?string {
        if (!$iso_time || $iso_time === '00:00:00') {
            return null;
        }
        return date("H:i", strtotime($iso_time) ?: 0);
    }
}
