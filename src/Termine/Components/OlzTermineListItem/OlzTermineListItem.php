<?php

namespace Olz\Termine\Components\OlzTermineListItem;

use Olz\Common\Components\OlzComponent;
use Olz\Entity\Termine\Termin;
use Olz\Entity\Users\User;
use Olz\Termine\Components\OlzDateCalendar\OlzDateCalendar;
use Olz\Users\Components\OlzUserInfoModal\OlzUserInfoModal;

/** @extends OlzComponent<array<string, mixed>> */
class OlzTermineListItem extends OlzComponent {
    public function getHtml(mixed $args): string {
        $db = $this->dbUtils()->getDb();
        $code_path = $this->envUtils()->getCodePath();
        $code_href = $this->envUtils()->getCodeHref();

        $out = '';

        $id = $args['id'];
        $owner_user_id = $args['owner_user_id'];
        $start_date = $args['start_date'];
        $start_time = $args['start_time'];
        $end_date = $args['end_date'];
        $end_time = $args['end_time'];
        $organizer_user_id = $args['organizer_user_id'];
        $title = $args['title'];
        $text = $args['text'];
        $labels = $args['labels'];
        $termin_location_id = $args['location_id'];
        $image_ids = $args['image_ids'];
        $is_deadline = count($labels) > 0 && $labels[0]->getIdent() === 'meldeschluss';

        $link = "{$code_href}termine/{$id}";
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
        $organizer_prefix = '';
        if ($organizer_user_id) {
            $user_repo = $this->entityManager()->getRepository(User::class);
            $organizer = $user_repo->findOneBy(['id' => $organizer_user_id]);
            $pretty_organizer = OlzUserInfoModal::render(['user' => $organizer]);
            $organizer_prefix = "{$pretty_organizer} ";
        }
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
        $sub_out = '';
        if (!$is_deadline) {
            $termine_repo = $this->entityManager()->getRepository(Termin::class);
            $sub_termine = [...$termine_repo->findBy(
                ['parent_termin' => $id, 'on_off' => 1],
                ['start_date' => 'ASC', 'start_time' => 'ASC'],
            )];
            $num_sub_termine = count($sub_termine);
            if ($num_sub_termine > 0) {
                $sub_out .= "<div class='sub-termine'>";
                for ($i = 0; $i < $num_sub_termine && $i < 3; $i++) {
                    $sub_termin = $sub_termine[$i];
                    $sub_out .= <<<ZZZZZZZZZZ
                        <div class='sub-termin'>
                            <img src="{$code_href}assets/icns/termine_type_all_20.svg" alt="Termine" class="text-icon">
                            {$sub_termin->getTitle()}
                        </div>
                        ZZZZZZZZZZ;
                }
                $num_more = $num_sub_termine - 3;
                if ($num_more > 0) {
                    $sub_out .= "<div class='sub-termin more'>+{$num_more}</div>";
                }
                $sub_out .= "</div>";
            }
        }

        $user = $this->authUtils()->getCurrentUser();
        $is_owner = $user && $owner_user_id && intval($owner_user_id) === intval($user->getId());
        $is_organizer = $user && $organizer_user_id && intval($organizer_user_id) === intval($user->getId());
        $has_all_permissions = $this->authUtils()->hasPermission('all');
        $can_edit = $is_owner || $is_organizer || $has_all_permissions;
        $edit_admin = '';
        if ($can_edit) {
            $json_id = json_encode(intval($id));
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
                        <div class='title'>{$title}{$edit_admin}</div>
                        <div class='text'>{$organizer_prefix}{$text}</div>
                    </div>
                    <div class='image-container'>
                        {$image}
                    </div>
                </div>
                <div class='sub'>
                    {$sub_out}
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
