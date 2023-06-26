<?php

// =============================================================================
// Zeigt geplante und vergangene Termine an.
// =============================================================================

namespace Olz\Termine\Components\OlzTermineList;

use Olz\Components\Common\OlzComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Termine\Components\OlzTermineFilter\OlzTermineFilter;
use Olz\Termine\Components\OlzTermineListItem\OlzTermineListItem;
use Olz\Termine\Components\OlzTermineSidebar\OlzTermineSidebar;
use Olz\Termine\Utils\TermineFilterUtils;
use Olz\Utils\FileUtils;
use Olz\Utils\HttpUtils;
use PhpTypeScriptApi\Fields\FieldTypes;

class OlzTermineList extends OlzComponent {
    public function getHtml($args = []): string {
        global $db_table, $monate, $heute;

        require_once __DIR__.'/../../../../_/config/date.php';
        require_once __DIR__.'/../../../../_/library/wgs84_ch1903/wgs84_ch1903.php';

        $db = $this->dbUtils()->getDb();
        $date_utils = $this->dateUtils();
        $data_href = $this->envUtils()->getDataHref();
        $file_utils = FileUtils::fromEnv();
        $http_utils = HttpUtils::fromEnv();
        $http_utils->setLog($this->log());
        $validated_get_params = $http_utils->validateGetParams([
            'filter' => new FieldTypes\StringField(['allow_null' => true]),
            'id' => new FieldTypes\IntegerField(['allow_null' => true]),
            'buttontermine' => new FieldTypes\StringField(['allow_null' => true]),
        ], $_GET);

        $current_filter = json_decode($_GET['filter'] ?? '{}', true);
        $termine_utils = TermineFilterUtils::fromEnv();

        if (!$termine_utils->isValidFilter($current_filter)) {
            $enc_json_filter = urlencode(json_encode($termine_utils->getDefaultFilter()));
            $http_utils->redirect("termine.php?filter={$enc_json_filter}", 308);
        }

        $termine_list_title = $termine_utils->getTitleFromFilter($current_filter);
        $is_not_archived = $termine_utils->isFilterNotArchived($current_filter);
        $allow_robots = $is_not_archived;

        $out = '';

        $out .= OlzHeader::render([
            'title' => $termine_list_title,
            'description' => "Orientierungslauf-Wettkämpfe, OL-Wochen, OL-Weekends, Trainings und Vereinsanlässe der OL Zimmerberg.",
            'norobots' => !$allow_robots,
        ]);

        $enc_current_filter = urlencode(json_encode($current_filter));

        $out .= "
        <div class='content-right optional'>
        <div>";
        $out .= OlzTermineSidebar::render();
        $out .= "</div>
        </div>
        <div class='content-middle'>";

        $out .= OlzTermineFilter::render();

        $has_access = $this->authUtils()->hasPermission('termine');
        if ($has_access) {
            $out .= <<<ZZZZZZZZZZ
            <button
                id='create-termin-button'
                class='btn btn-secondary create-termin-container'
                onclick='return olz.initOlzEditTerminModal()'
            >
                <img src='{$data_href}assets/icns/new_white_16.svg' class='noborder' />
                Neuer Termin
            </button>
            ZZZZZZZZZZ;
        }

        $out .= "<h1>{$termine_list_title}</h1>";

        // -------------------------------------------------------------
        //  VORSCHAU - LISTE
        $filter_where = $termine_utils->getSqlFromFilter($current_filter);
        $sql_where = <<<ZZZZZZZZZZ
        (
            (t.datum_off>='{$date_utils->getIsoToday()}')
            OR (t.datum_off='0000-00-00')
            OR t.datum_off IS NULL
        )
        AND (t.on_off = '1')
        AND {$filter_where}
        ZZZZZZZZZZ;

        $sql = <<<ZZZZZZZZZZ
        (
            SELECT
                'termin' as item_type,
                t.datum as datum,
                t.datum_end as datum_end,
                t.zeit as zeit,
                t.zeit_end as zeit_end,
                t.titel as titel,
                t.text as text,
                t.link as link,
                t.solv_event_link as solv_event_link,
                t.id as id,
                t.typ as typ,
                t.on_off as on_off,
                t.newsletter as newsletter,
                t.xkoord as xkoord,
                t.ykoord as ykoord,
                t.go2ol as go2ol,
                t.solv_uid as solv_uid,
                t.last_modified_by_user_id as last_modified_by_user_id,
                t.image_ids as image_ids
            FROM termine t
            WHERE {$sql_where}
        ) UNION ALL (
            SELECT
                'solv_deadline' as item_type,
                se.deadline as datum,
                NULL as datum_end,
                '00:00:00' as zeit,
                NULL as zeit_end,
                CONCAT('Meldeschluss für ', t.titel) as titel,
                '' as text,
                '' as link,
                '' as solv_event_link,
                t.id as id,
                'meldeschluss' as typ,
                t.on_off as on_off,
                NULL as newsletter,
                NULL as xkoord,
                NULL as ykoord,
                t.go2ol as go2ol,
                t.solv_uid as solv_uid,
                t.last_modified_by_user_id as last_modified_by_user_id,
                t.image_ids as image_ids
            FROM termine t JOIN solv_events se ON (t.solv_uid = se.solv_uid)
            WHERE se.deadline IS NOT NULL AND {$sql_where}
        ) UNION ALL (
            SELECT
                'olz_deadline' as item_type,
                DATE(t.deadline) as datum,
                NULL as datum_end,
                TIME(t.deadline) as zeit,
                NULL as zeit_end,
                CONCAT('Meldeschluss für ', t.titel) as titel,
                '' as text,
                '' as link,
                '' as solv_event_link,
                t.id as id,
                'meldeschluss' as typ,
                t.on_off as on_off,
                NULL as newsletter,
                NULL as xkoord,
                NULL as ykoord,
                t.go2ol as go2ol,
                t.solv_uid as solv_uid,
                t.last_modified_by_user_id as last_modified_by_user_id,
                t.image_ids as image_ids
            FROM termine t
            WHERE t.deadline IS NOT NULL AND {$sql_where}
        )
        ORDER BY datum ASC
        ZZZZZZZZZZ;

        $result = $db->query($sql);

        $last_month = null;
        while ($row = $result->fetch_assoc()) {
            $this_month = substr($row['datum'], 0, 7);
            if ($this_month !== $last_month) {
                $pretty_month = $this->dateUtils()->olzDate("MM jjjj", "{$this_month}-01");
                $out .= "<h3 class='tablebar'>{$pretty_month}</h3>";
            }
            $last_month = $this_month;

            $out .= OlzTermineListItem::render([
                'item_type' => $row['item_type'],
                'id' => $row['id'],
                'start_date' => $row['datum'],
                'start_time' => $row['zeit'],
                'end_date' => $row['datum_end'],
                'end_time' => $row['zeit_end'],
                'title' => $row['titel'],
                'text' => $row['text'],
                'link' => $row['link'],
                'solv_uid' => $row['solv_uid'],
                'types' => explode(' ', $row['typ']),
                'image_ids' => $row['image_ids'] ? json_decode($row['image_ids'], true) : null,
            ]);
        }
        $out .= "</div>";

        $out .= OlzFooter::render();

        return $out;
    }
}
