<?php

namespace Olz\Termine\Components\OlzTerminDetail;

use Doctrine\Common\Collections\Criteria;
use Olz\Components\Common\OlzComponent;
use Olz\Components\Common\OlzLocationMap\OlzLocationMap;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Components\Schema\OlzEventData\OlzEventData;
use Olz\Entity\Termine\Termin;
use Olz\Termine\Components\OlzDateCalendar\OlzDateCalendar;
use Olz\Termine\Utils\TermineFilterUtils;
use Olz\Utils\FileUtils;
use Olz\Utils\HtmlUtils;
use Olz\Utils\HttpUtils;
use Olz\Utils\ImageUtils;
use PhpTypeScriptApi\Fields\FieldTypes;

class OlzTerminDetail extends OlzComponent {
    protected static $iconBasenameByType = [
        'programm' => 'termine_type_programm_20.svg',
        'weekend' => 'termine_type_weekend_20.svg',
        'ol' => 'termine_type_ol_20.svg',
        'training' => 'termine_type_training_20.svg',
        'club' => 'termine_type_club_20.svg',
        'meldeschluss' => 'termine_type_meldeschluss_20.svg',
    ];

    public function getHtml($args = []): string {
        global $_GET;

        $code_href = $this->envUtils()->getCodeHref();
        $date_utils = $this->dateUtils();
        $today = $date_utils->getIsoToday();
        $db = $this->dbUtils()->getDb();
        $entityManager = $this->dbUtils()->getEntityManager();
        $html_utils = HtmlUtils::fromEnv();
        $http_utils = HttpUtils::fromEnv();
        $http_utils->setLog($this->log());
        $file_utils = FileUtils::fromEnv();
        $image_utils = ImageUtils::fromEnv();
        $user = $this->authUtils()->getCurrentUser();
        $http_utils->validateGetParams([
            'filter' => new FieldTypes\StringField(['allow_null' => true]),
            'id' => new FieldTypes\IntegerField(['allow_null' => true]),
            'buttontermine' => new FieldTypes\StringField(['allow_null' => true]),
        ], $_GET);
        $id = $args['id'] ?? null;

        $termine_utils = TermineFilterUtils::fromEnv();
        $termin_repo = $entityManager->getRepository(Termin::class);
        $is_not_archived = $termine_utils->getIsNotArchivedCriteria();
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
        $no_robots = $num_termine !== 1;
        $host = str_replace('www.', '', $_SERVER['HTTP_HOST']);
        $canonical_url = "https://{$host}{$code_href}termine/{$id}";

        $out = '';

        $sql = "SELECT * FROM termine WHERE (id = '{$id}') AND (on_off = '1') ORDER BY start_date DESC";
        $result = $db->query($sql);
        $row = $result->fetch_assoc();

        if (!$row) {
            $http_utils->dieWithHttpError(404);
        }

        $title = $row['title'] ?? '';
        $termin_year = date('Y', strtotime($row['start_date']));
        $this_year = $this->dateUtils()->getCurrentDateInFormat('Y');
        $maybe_date = ($termin_year !== $this_year) ? " {$termin_year}" : '';
        $back_filter = urlencode($_GET['filter'] ?? '{}');
        $out .= OlzHeader::render([
            'back_link' => "{$code_href}termine?filter={$back_filter}",
            'title' => "{$title}{$maybe_date} - Termine",
            'description' => "Orientierungslauf-Wettkämpfe, OL-Wochen, OL-Weekends, Trainings und Vereinsanlässe der OL Zimmerberg.",
            'norobots' => $no_robots,
            'canonical_url' => $canonical_url,
        ]);

        $out .= <<<'ZZZZZZZZZZ'
        <div class='content-right optional'>
            <div style='padding:4px 3px 10px 3px;'>
            </div>
        </div>
        <div class='content-middle'>
        ZZZZZZZZZZ;

        $start_date = $row['start_date'] ?? '';
        $end_date = $row['end_date'] ?? '';
        $start_time = $row['start_time'] ?? '';
        $end_time = $row['end_time'] ?? '';
        $title = $row['title'] ?? '';
        $text = $row['text'] ?? '';
        $link = $row['link'] ?? '';
        $typ = $row['typ'] ?? '';
        $types = explode(' ', $typ);
        $newsletter = $row['newsletter'] ?? '';
        $xkoord = $row['xkoord'] ?? '';
        $ykoord = $row['ykoord'] ?? '';
        $go2ol = $row['go2ol'] ?? '';
        $solv_uid = $row['solv_uid'] ?? '';
        $termin_location_id = $row['location_id'] ?? '';
        $row_solv = false;
        if ($solv_uid) {
            $sane_solv_uid = intval($solv_uid);
            $result_solv = $db->query("SELECT * FROM solv_events WHERE solv_uid='{$sane_solv_uid}'");
            $row_solv = $result_solv->fetch_assoc();
        }
        $row_location = false;
        if ($termin_location_id) {
            $sane_termin_location_id = intval($termin_location_id);
            $result_location = $db->query("SELECT * FROM termin_locations WHERE id='{$sane_termin_location_id}'");
            $row_location = $result_location->fetch_assoc();
        }
        $has_olz_location = ($xkoord > 0 && $ykoord > 0);
        $has_termin_location = (
            $typ != 'meldeschluss'
            && $row_location
            && $row_location['latitude'] > 0
            && $row_location['longitude'] > 0
        );
        $has_solv_location = (
            $typ != 'meldeschluss'
            && $row_solv
            && $row_solv['coord_x'] > 0
            && $row_solv['coord_y'] > 0
        );
        $lat = null;
        $lng = null;
        $location_name = null;
        if ($has_solv_location) {
            $lat = $this->mapUtils()->CHtoWGSlat($row_solv['coord_x'], $row_solv['coord_y']);
            $lng = $this->mapUtils()->CHtoWGSlng($row_solv['coord_x'], $row_solv['coord_y']);
            $location_name = $row_solv['location'];
        }
        if ($has_termin_location) {
            $lat = $row_location['latitude'];
            $lng = $row_location['longitude'];
            $location_name = $row_location['name'];
        }
        if ($has_olz_location) {
            $lat = $this->mapUtils()->CHtoWGSlat($xkoord, $ykoord);
            $lng = $this->mapUtils()->CHtoWGSlng($xkoord, $ykoord);
            $location_name = null;
        }
        $has_location = $has_olz_location || $has_termin_location || $has_solv_location;
        $image_ids = json_decode($row['image_ids'] ?? 'null', true);

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
        if ($image_ids && count($image_ids) > 0) {
            $out .= $image_utils->olzImage(
                'termine', $id, $image_ids[0], 840);
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
        $out .= OlzDateCalendar::render(['date' => $start_date]);
        $out .= "</div>";

        $out .= "</div>";

        // Editing Tools
        $is_owner = $user && intval($row['owner_user_id'] ?? 0) === intval($user->getId());
        $has_termine_permissions = $this->authUtils()->hasPermission('termine');
        $can_edit = $is_owner || $has_termine_permissions;
        if ($can_edit) {
            $json_id = json_encode(intval($id));
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
                <button
                    id='delete-termin-button'
                    class='btn btn-danger'
                    onclick='return olz.deleteTermin({$json_id})'
                >
                    <img src='{$code_href}assets/icns/delete_white_16.svg' class='noborder' />
                    Löschen
                </button>
            </div>
            ZZZZZZZZZZ;
        }

        // Date & Title
        $pretty_date = $this->dateUtils()->formatDateTimeRange($start_date, $start_time, $end_date, $end_time, $format = 'long');
        $maybe_solv_link = '';
        if ($row_solv) {
            // SOLV-Übersicht-Link zeigen
            $maybe_solv_link .= "<a href='https://www.o-l.ch/cgi-bin/fixtures?&mode=show&unique_id=".$row_solv['solv_uid']."' target='_blank' class='linkol' style='margin-left: 20px; font-weight: normal;'>O-L.ch</a>\n";
        }
        $type_imgs = implode('', array_map(function ($type) use ($code_href) {
            $icon_basename = self::$iconBasenameByType[$type] ?? '';
            $icon = "{$code_href}assets/icns/{$icon_basename}";
            return "<img src='{$icon}' alt='' class='type-icon'>";
        }, $types));
        $out .= "<h2>{$pretty_date}{$maybe_solv_link}</h2>";
        $out .= "<h1>{$title} {$type_imgs}</h1>";

        // Text
        // TODO: Temporary fix for broken Markdown
        $text = str_replace("\n", "\n\n", $text);
        $text = str_replace("\n\n\n\n", "\n\n", $text);
        $text = $html_utils->renderMarkdown($text, [
            'html_input' => 'allow', // TODO: Do NOT allow!
        ]);
        if ($typ != 'meldeschluss' && $row_solv && isset($row_solv['deadline']) && $row_solv['deadline'] && $row_solv['deadline'] != "0000-00-00") {
            $text .= ($text == "" ? "" : "<br />")."Meldeschluss: ".$date_utils->olzDate("t. MM ", $row_solv['deadline']);
        }
        if ($typ != 'meldeschluss' && isset($row['deadline']) && $row['deadline'] && $row['deadline'] != "0000-00-00") {
            $text .= ($text == "" ? "" : "<br />")."Meldeschluss: ".$date_utils->olzDate("t. MM ", $row['deadline']);
        }
        $out .= "<div>".$text."</div>";

        // Link
        $link = $file_utils->replaceFileTags($link, 'termine', $id);
        if ($go2ol > "" and $start_date >= $today) {
            $link .= "<div class='linkext'><a href='https://go2ol.ch/".$go2ol."/' target='_blank'>Anmeldung</a></div>\n";
        } elseif ($row_solv && $row_solv['entryportal'] == 1 and $start_date >= $today) {
            $link .= "<div class='linkext'><a href='https://www.go2ol.ch/index.asp?lang=de' target='_blank'>Anmeldung</a></div>\n";
        } elseif ($row_solv && $row_solv['entryportal'] == 2 and $start_date >= $today) {
            $link .= "<div class='linkext'><a href='https://entry.picoevents.ch/' target='_blank'>Anmeldung</a></div>\n";
        }
        if ($solv_uid > 0 and $start_date <= $today and strpos($link, "Rangliste") == "" and strpos($link, "Resultat") == "" and strpos($typ, "ol") >= 0) {
            // Ranglisten-Link zeigen
            $link .= "<div><a href='http://www.o-l.ch/cgi-bin/results?unique_id=".$solv_uid."&club=zimmerberg' target='_blank' class='linkol'>Rangliste</a></div>\n";
        }
        if ($row_solv && ($row_solv["event_link"] ?? false) and strpos($link, "Ausschreibung") == "" and strpos($typ, "ol") >= 0 and $start_date <= $today) {
            // SOLV-Ausschreibungs-Link zeigen
            $ispdf = preg_match("/\\.pdf$/", $row_solv["event_link"]);
            $link .= "<div><a href='".$row_solv["event_link"]."' target='_blank' class='link".($ispdf ? "pdf" : "ext")."'>Ausschreibung</a></div>\n";
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
                    $location_maybe_link = "<a href='{$code_href}termine/orte/{$termin_location_id}?filter={$back_filter}&id={$id}' class='linkmap'>{$location_name}</a>";
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
}
