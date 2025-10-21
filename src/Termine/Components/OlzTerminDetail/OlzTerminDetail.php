<?php

namespace Olz\Termine\Components\OlzTerminDetail;

use Doctrine\Common\Collections\Criteria;
use Olz\Components\Common\OlzLocationMap\OlzLocationMap;
use Olz\Components\Common\OlzRootComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Components\Schema\OlzEventData\OlzEventData;
use Olz\Entity\Termine\Termin;
use Olz\Entity\Termine\TerminLabel;
use Olz\Termine\Components\OlzDateCalendar\OlzDateCalendar;
use Olz\Utils\HttpParams;

/** @extends HttpParams<array{filter?: ?string, von?: ?string}> */
class OlzTerminDetailParams extends HttpParams {
}

/** @extends OlzRootComponent<array<string, mixed>> */
class OlzTerminDetail extends OlzRootComponent {
    public function getSearchTitle(): string {
        return 'Termine';
    }

    public function getSearchResults(array $terms): array {
        $results = [];
        $code_href = $this->envUtils()->getCodeHref();
        $termin_repo = $this->entityManager()->getRepository(Termin::class);
        $termine = $termin_repo->search($terms);
        foreach ($termine as $termin) {
            $id = $termin->getId();
            $results[] = $this->searchUtils()->getScoredSearchResult([
                'link' => "{$code_href}termine/{$id}",
                'icon' => "{$code_href}assets/icns/termine_type_all_20.svg",
                'date' => $termin->getStartDate(),
                'title' => $termin->getTitle() ?: '?',
                'text' => $termin->getText() ?: null,
            ], $terms);
        }
        return $results;
    }

    public function getHtml(mixed $args): string {
        $params = $this->httpUtils()->validateGetParams(OlzTerminDetailParams::class);

        $code_href = $this->envUtils()->getCodeHref();
        $code_path = $this->envUtils()->getCodePath();
        $data_path = $this->envUtils()->getDataPath();
        $date_utils = $this->dateUtils();
        $today = $date_utils->getIsoToday();
        $entityManager = $this->dbUtils()->getEntityManager();
        $user = $this->authUtils()->getCurrentUser();
        $id = $args['id'] ?? null;

        $termin_repo = $entityManager->getRepository(Termin::class);
        $is_not_archived = $this->termineUtils()->getIsNotArchivedCriteria();
        $criteria = Criteria::create()
            ->where(Criteria::expr()->andX(
                $is_not_archived,
                Criteria::expr()->eq('id', $id),
                Criteria::expr()->eq('on_off', 1),
            ))
            ->setFirstResult(0)
            ->setMaxResults(1)
        ;
        $termine = $termin_repo->matching($criteria);
        $num_termine = $termine->count();
        $is_archived = $num_termine !== 1;

        if ($is_archived && !$this->authUtils()->hasPermission('any')) {
            $this->httpUtils()->dieWithHttpError(404);
            throw new \Exception('should already have failed');
        }

        $termin = $this->getTerminById($id);

        if (!$termin) {
            $this->httpUtils()->dieWithHttpError(404);
            throw new \Exception('should already have failed');
        }

        $title = $termin->getTitle() ?? '';
        $termin_year = $termin->getStartDate()->format('Y');
        $this_year = $this->dateUtils()->getCurrentDateInFormat('Y');
        $maybe_date = ($termin_year !== $this_year) ? " {$termin_year}" : '';
        $title = "{$title}{$maybe_date}";
        $back_filter = json_decode($params['filter'] ?? 'null', true);
        $termine_utils = $this->termineUtils()->loadTypeOptions();
        if ($back_filter && !$termine_utils->isValidFilter($back_filter)) {
            $valid_filter = $termine_utils->getValidFilter($back_filter);
            $enc_json_filter = urlencode(json_encode($valid_filter) ?: '{}');
            $this->httpUtils()->redirect("{$code_href}termine/{$id}?filter={$enc_json_filter}", 410);
        }
        $enc_back_filter = urlencode(json_encode($back_filter ?: $termine_utils->getDefaultFilter()) ?: '{}');
        $out = OlzHeader::render([
            'back_link' => "{$code_href}termine?filter={$enc_back_filter}",
            'title' => "{$title} - Termine",
            'description' => "Orientierungslauf-Wettkämpfe, OL-Wochen, OL-Weekends, Trainings und Vereinsanlässe der OL Zimmerberg.",
            'norobots' => $is_archived,
            'canonical_url' => "{$code_href}termine/{$id}",
        ]);

        $out .= <<<'ZZZZZZZZZZ'
            <div class='content-right optional'>
                <div style='padding:4px 3px 10px 3px;'>
                </div>
            </div>
            <div class='content-middle'>
            ZZZZZZZZZZ;

        $start_date = $termin->getStartDate();
        $end_date = $termin->getEndDate() ?? null;
        $start_time = $termin->getStartTime() ?? null;
        $end_time = $termin->getEndTime() ?? null;
        $text = $termin->getText() ?? '';
        $labels = [...$termin->getLabels()];
        $xkoord = $termin->getCoordinateX() ?? 0;
        $ykoord = $termin->getCoordinateY() ?? 0;
        $solv_uid = $termin->getSolvId();
        $termin_location = $termin->getLocation();
        $has_olz_location = ($xkoord > 0 && $ykoord > 0);
        $has_termin_location = (
            $termin_location
            && $termin_location->getLatitude() > 0
            && $termin_location->getLongitude() > 0
        );
        $lat = null;
        $lng = null;
        $location_name = null;
        if ($has_termin_location) {
            $lat = $termin_location->getLatitude();
            $lng = $termin_location->getLongitude();
            $location_name = $termin_location->getName();
        }
        if ($has_olz_location) {
            $lat = $this->mapUtils()->CHtoWGSlat($xkoord, $ykoord);
            $lng = $this->mapUtils()->CHtoWGSlng($xkoord, $ykoord);
            $location_name = null;
        }
        $has_location = $has_olz_location || $has_termin_location;
        $image_ids = $termin->getImageIds();

        $out .= OlzEventData::render([
            'name' => $title,
            'start_date' => $date_utils->olzDate('jjjj-mm-tt', $start_date),
            'end_date' => $end_date ? $date_utils->olzDate('jjjj-mm-tt', $end_date) : null,
            'location' => $has_location ? [
                'lat' => $lat,
                'lng' => $lng,
                'name' => $location_name,
            ] : null,
        ]);

        $out .= "<div class='olz-termin-detail'>";

        $out .= "<div class='preview'>";
        // Bild anzeigen
        if (count($image_ids) > 0) {
            $out .= $this->imageUtils()->olzImage(
                'termine',
                $id,
                $image_ids[0],
                840
            );
        // Karte zeigen
        } elseif ($has_location) {
            $out .= OlzLocationMap::render([
                'latitude' => $lat,
                'longitude' => $lng,
                'zoom' => 13,
            ]);
        }
        // Date Calendar Icon
        $out .= "<div class='date-calendar-container'>";
        $out .= "<div class='date-calendars'>";
        $out .= "<div class='date-calendar'>";
        $out .= OlzDateCalendar::render(['date' => $start_date]);
        $out .= $this->getTimeText($start_time) ?? '';
        $out .= ($end_time && (!$end_date || $end_date === $start_date))
            ? ' &ndash; '.$this->getTimeText($end_time)
            : '';
        $out .= "</div>";
        $out .= "<div class='date-calendar'>";
        $out .= ($end_date && $end_date !== $start_date)
            ? OlzDateCalendar::render(['date' => $end_date])
            : '';
        $out .= ($end_time && $end_date && $end_date !== $start_date)
            ? $this->getTimeText($end_time)
            : '';
        $out .= "</div>";
        $out .= "</div>";
        $out .= "</div>";

        $out .= "</div>";

        // Editing Tools
        $is_owner = $user && intval($termin->getOwnerUser()?->getId() ?? 0) === intval($user->getId());
        $has_termine_permissions = $this->authUtils()->hasPermission('termine');
        $can_edit = $is_owner || $has_termine_permissions;
        if ($can_edit) {
            $json_id = json_encode($id);
            $out .= <<<ZZZZZZZZZZ
                <div>
                    <button
                        id='edit-termin-button'
                        class='btn btn-primary'
                        onclick='return olz.editTermin({$json_id})'
                    >
                        <img src='{$code_href}assets/icns/edit_white_16.svg' class='noborder' />
                        Bearbeiten
                    </button>
                </div>
                ZZZZZZZZZZ;
        }

        // Date & Title
        $pretty_date = $this->dateUtils()->formatDateTimeRange(
            $start_date->format('Y-m-d'),
            $start_time?->format('H:i:s'),
            $end_date?->format('Y-m-d'),
            $end_time?->format('H:i:s'),
            $format = 'long',
        );
        $maybe_solv_link = '';
        if ($solv_uid) {
            // SOLV-Übersicht-Link zeigen
            $maybe_solv_link .= "<a href='https://www.o-l.ch/cgi-bin/fixtures?&mode=show&unique_id={$solv_uid}' target='_blank' class='linkol' style='margin-left: 20px; font-weight: normal;'>O-L.ch</a>\n";
        }
        $label_imgs = implode('', array_map(function (TerminLabel $label) use ($code_path, $code_href) {
            $ident = $label->getIdent();
            // TODO: Remove fallback mechanism?
            $fallback_path = "{$code_path}assets/icns/termine_type_{$ident}_20.svg";
            $fallback_href = is_file($fallback_path)
                ? "{$code_href}assets/icns/termine_type_{$ident}_20.svg" : null;
            $icon_href = $label->getIcon() ? $label->getFileHref($label->getIcon()) : $fallback_href;
            return $icon_href ? "<img src='{$icon_href}' alt='' class='type-icon'>" : '';
        }, $labels));
        $out .= "<h5>{$pretty_date}{$maybe_solv_link}</h5>";
        $out .= "<h1>{$title} {$label_imgs}</h1>";

        // Text
        // TODO: Temporary fix for broken Markdown
        $text = str_replace("\n", "\n\n", $text);
        $text = str_replace("\n\n\n\n", "\n\n", $text);
        $text_html = $this->htmlUtils()->renderMarkdown($text, [
            'html_input' => 'allow', // TODO: Do NOT allow!
        ]);
        $text_html = $termin->replaceImagePaths($text_html);
        $text_html = $termin->replaceFilePaths($text_html);
        if ($termin->getDeadline() && $termin->getDeadline() != "0000-00-00") {
            $text_html .= ($text_html == "" ? "" : "<br />")."Meldeschluss: ".$date_utils->olzDate("t. MM ", $termin->getDeadline());
        }
        $out .= "<div>".$text_html."</div>";

        // Link
        $link = '';
        if ($solv_uid && $start_date <= $today && !preg_match('/(Rangliste|Resultat)/', $link)) {
            // SOLV Ranglisten-Link zeigen
            $link .= "<div><a href='http://www.o-l.ch/cgi-bin/results?unique_id={$solv_uid}&club=zimmerberg' target='_blank' class='linkol'>Rangliste</a></div>\n";
        }
        $result_filename = "{$termin_year}-termine-{$id}.xml";
        if (is_file("{$data_path}results/{$result_filename}")) {
            // OLZ Ranglisten-Link zeigen
            $link .= "<div><a href='{$code_href}apps/resultate?file={$result_filename}' target='_blank' class='linkext'>Ranglisten</a></div>\n";
        } elseif ($can_edit) {
            // OLZ Rangliste-hochladen-Link zeigen
            $link .= "<div><a href='{$code_href}apps/resultate?file={$result_filename}' target='_blank' class='linkext'>Rangliste hochladen</a></div>\n";
        }
        if ($link == "") {
            $link = "&nbsp;";
        } else {
            $link = str_replace("&", "&amp;", str_replace("&amp;", "&", $link));
        }
        $link = str_replace("www.solv.ch", "www.o-l.ch", $link);
        $out .= "<div class='links'>".$link."</div>";

        // Karte zeigen
        if ($has_location) {
            if ($location_name !== null) {
                $location_maybe_link = $location_name;
                if ($has_termin_location) {
                    $location_maybe_link = "<a href='{$code_href}termine/orte/{$termin_location->getId()}?filter={$enc_back_filter}&id={$id}' class='linkmap'>{$location_name}</a>";
                }
                $out .= "<h3>Ort: {$location_maybe_link}</h3>";
            } else {
                $out .= "<h3>Ort</h3>";
            }
            $out .= OlzLocationMap::render([
                'name' => $location_name ?? '',
                'latitude' => $lat,
                'longitude' => $lng,
                'zoom' => 13,
            ]);
        }

        $out .= "</div>"; // olz-termin-detail
        $out .= "</div>"; // content-middle

        $out .= OlzFooter::render();

        return $out;
    }

    protected function getTerminById(int $id): ?Termin {
        $termin_repo = $this->entityManager()->getRepository(Termin::class);
        return $termin_repo->findOneBy([
            'id' => $id,
            'on_off' => 1,
        ]);
    }

    protected function getTimeText(?\DateTime $time): ?string {
        if (!$time || $time->format('H:i:s') === '00:00:00') {
            return null;
        }
        return $time->format('H:i');
    }
}
