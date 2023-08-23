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

        require_once __DIR__.'/../../../../_/library/wgs84_ch1903/wgs84_ch1903.php';
        require_once __DIR__.'/../../../../_/admin/olz_functions.php';

        $code_href = $this->envUtils()->getCodeHref();
        $data_path = $this->envUtils()->getDataPath();
        $date_utils = $this->dateUtils();
        $today = $date_utils->getIsoToday();
        $db = $this->dbUtils()->getDb();
        $entityManager = $this->dbUtils()->getEntityManager();
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

        $sql = "SELECT * FROM termine WHERE (id = '{$id}') AND (on_off = '1') ORDER BY datum DESC";
        $result = $db->query($sql);
        $row = $result->fetch_assoc();

        if (!$row) {
            $http_utils->dieWithHttpError(404);
        }

        $title = $row['titel'] ?? '';
        $back_filter = urlencode($_GET['filter'] ?? '{}');
        $out .= OlzHeader::render([
            'back_link' => "{$code_href}termine?filter={$back_filter}",
            'title' => "{$title} - Termine",
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

        $datum = $row['datum'] ?? '';
        $datum_end = $row['datum_end'] ?? '';
        $zeit = $row['zeit'] ?? '';
        $zeit_end = $row['zeit_end'] ?? '';
        $titel = $row['titel'] ?? '';
        $text = $row['text'] ?? '';
        $link = $row['link'] ?? '';
        $event_link = $row['solv_event_link'] ?? '';
        $typ = $row['typ'] ?? '';
        $types = explode(' ', $typ);
        $newsletter = $row['newsletter'] ?? '';
        $xkoord = $row['xkoord'] ?? '';
        $ykoord = $row['ykoord'] ?? '';
        $go2ol = $row['go2ol'] ?? '';
        $solv_uid = $row['solv_uid'] ?? '';
        $row_solv = false;
        if ($solv_uid) {
            $sane_solv_uid = intval($solv_uid);
            $result_solv = $db->query("SELECT * FROM solv_events WHERE solv_uid='{$sane_solv_uid}'");
            $row_solv = $result_solv->fetch_assoc();
        }
        $has_olz_location = ($xkoord > 0 && $ykoord > 0);
        $has_solv_location = (
            $typ != 'meldeschluss'
            && $row_solv
            && $row_solv['coord_x'] > 0
            && $row_solv['coord_y'] > 0
        );
        $lat = $has_olz_location ? CHtoWGSlat($xkoord, $ykoord) :
            ($has_solv_location ? CHtoWGSlat($row_solv['coord_x'], $row_solv['coord_y']) : null);
        $lng = $has_olz_location ? CHtoWGSlng($xkoord, $ykoord) :
            ($has_solv_location ? CHtoWGSlat($row_solv['coord_x'], $row_solv['coord_y']) : null);
        $location_name = $has_olz_location ? null :
            ($has_solv_location ? $row_solv['location'] : null);
        $has_location = $has_olz_location || $has_solv_location;
        $image_ids = json_decode($row['image_ids'] ?? 'null', true);

        $out .= OlzEventData::render([
            'name' => $titel,
            'start_date' => $date_utils->olzDate('jjjj-mm-tt', $datum),
            'end_date' => $datum_end ? $date_utils->olzDate('jjjj-mm-tt', $datum_end) : null,
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
        } elseif ($has_olz_location) {
            $out .= OlzLocationMap::render([
                'xkoord' => $xkoord,
                'ykoord' => $ykoord,
                'zoom' => 13,
                'width' => 840,
                'height' => 240,
            ]);
            // SOLV-Karte zeigen
        } elseif ($has_solv_location) {
            $out .= OlzLocationMap::render([
                'xkoord' => $row_solv['coord_x'],
                'ykoord' => $row_solv['coord_y'],
                'zoom' => 13,
                'width' => 840,
                'height' => 240,
            ]);
        }
        // Date Calendar Icon
        $out .= "<div class='date-calendar-container'>";
        $out .= OlzDateCalendar::render(['date' => $datum]);
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
        if ($datum_end == "0000-00-00" || !$datum_end) {
            $datum_end = $datum;
        }
        if (($datum_end == $datum) or ($datum_end == "0000-00-00") or !$datum_end) {
            $datum_tmp = $date_utils->olzDate("t. MM ", $datum).$date_utils->olzDate(" (W)", $datum);
            // Tagesanlass
            if ($zeit != "00:00:00") {
                $datum_tmp .= " ".date("H:i", strtotime($zeit));
                if ($zeit_end != "00:00:00") {
                    $datum_tmp .= " &ndash; ".date("H:i", strtotime($zeit_end));
                }
            }
        } elseif ($date_utils->olzDate("m", $datum) == $date_utils->olzDate("m", $datum_end)) {
            // Mehrtägig innerhalb Monat
            $datum_tmp = $date_utils->olzDate("t.-", $datum).$date_utils->olzDate("t. ", $datum_end).$date_utils->olzDate("MM", $datum).$date_utils->olzDate(" (W-", $datum).$date_utils->olzDate("W)", $datum_end);
        } else {
            // Mehrtägig monatsübergreifend
            $datum_tmp = $date_utils->olzDate("t.m.-", $datum).$date_utils->olzDate("t.m. ", $datum_end).$date_utils->olzDate("jjjj", $datum).$date_utils->olzDate(" (W-", $datum).$date_utils->olzDate("W)", $datum_end);
        }
        if ($row_solv) {
            // SOLV-Übersicht-Link zeigen
            $datum_tmp .= "<a href='https://www.o-l.ch/cgi-bin/fixtures?&mode=show&unique_id=".$row_solv['solv_uid']."' target='_blank' class='linkol' style='margin-left: 20px; font-weight: normal;'>O-L.ch</a>\n";
        }
        $type_imgs = implode('', array_map(function ($type) use ($code_href) {
            $icon_basename = self::$iconBasenameByType[$type] ?? '';
            $icon = "{$code_href}assets/icns/{$icon_basename}";
            return "<img src='{$icon}' alt='' class='type-icon'>";
        }, $types));
        $out .= "<h2>{$datum_tmp}</h2>";
        $out .= "<h1>{$titel} {$type_imgs}</h1>";

        // Text
        $text = \olz_br(olz_mask_email($text, "", ""));
        if ($typ != 'meldeschluss' && $row_solv && isset($row_solv['deadline']) && $row_solv['deadline'] && $row_solv['deadline'] != "0000-00-00") {
            $text .= ($text == "" ? "" : "<br />")."Meldeschluss: ".$date_utils->olzDate("t. MM ", $row_solv['deadline']);
        }
        if ($typ != 'meldeschluss' && isset($row['deadline']) && $row['deadline'] && $row['deadline'] != "0000-00-00") {
            $text .= ($text == "" ? "" : "<br />")."Meldeschluss: ".$date_utils->olzDate("t. MM ", $row['deadline']);
        }
        $out .= "<div>".$text."</div>";

        // Link
        $link = $file_utils->replaceFileTags($link, 'termine', $id);
        if ($go2ol > "" and $datum >= $today) {
            $link .= "<div class='linkext'><a href='https://go2ol.ch/".$go2ol."/' target='_blank'>Anmeldung</a></div>\n";
        } elseif ($row_solv && $row_solv['entryportal'] == 1 and $datum >= $today) {
            $link .= "<div class='linkext'><a href='https://www.go2ol.ch/index.asp?lang=de' target='_blank'>Anmeldung</a></div>\n";
        } elseif ($row_solv && $row_solv['entryportal'] == 2 and $datum >= $today) {
            $link .= "<div class='linkext'><a href='https://entry.picoevents.ch/' target='_blank'>Anmeldung</a></div>\n";
        }
        if (strpos($link, 'Ausschreibung') == 0 and ($row['solv_event_link'] ?? '') > "") {
            $class = strpos($row['solv_event_link'], ".pdf") > 0 ? 'linkpdf' : 'linkext';
            $link .= "<div class='{$class}'><a href='".$row['solv_event_link']."' target='_blank'>Ausschreibung</a></div>";
        }
        if ($solv_uid > 0 and $datum <= $today and strpos($link, "Rangliste") == "" and strpos($link, "Resultat") == "" and strpos($typ, "ol") >= 0) {
            // Ranglisten-Link zeigen
            $link .= "<div><a href='http://www.o-l.ch/cgi-bin/results?unique_id=".$solv_uid."&club=zimmerberg' target='_blank' class='linkol'>Rangliste</a></div>\n";
        }
        if ($row_solv && ($row_solv["event_link"] ?? false) and strpos($link, "Ausschreibung") == "" and strpos($typ, "ol") >= 0 and $datum <= $today) {
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
        if ($has_olz_location) {
            $out .= OlzLocationMap::render([
                'xkoord' => $xkoord,
                'ykoord' => $ykoord,
                'zoom' => 13,
                'width' => 720,
                'height' => 420,
            ]);
            // SOLV-Karte zeigen
        } elseif ($has_solv_location) {
            $out .= OlzLocationMap::render([
                'xkoord' => $row_solv['coord_x'],
                'ykoord' => $row_solv['coord_y'],
                'zoom' => 13,
                'width' => 720,
                'height' => 420,
            ]);
        }

        $out .= "</div>"; // olz-termin-detail
        $out .= "</div>"; // content-middle

        $out .= OlzFooter::render();

        return $out;
    }
}
