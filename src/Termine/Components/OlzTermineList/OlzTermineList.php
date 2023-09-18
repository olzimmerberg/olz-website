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
        require_once __DIR__.'/../../../../_/library/wgs84_ch1903/wgs84_ch1903.php';

        $db = $this->dbUtils()->getDb();
        $date_utils = $this->dateUtils();
        $code_href = $this->envUtils()->getCodeHref();
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
            $http_utils->redirect("?filter={$enc_json_filter}", 308);
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

        $sidebar = OlzTermineSidebar::render();
        $out .= <<<ZZZZZZZZZZ
        <div class='content-right optional'>
            <div>{$sidebar}</div>
        </div>
        <div class='content-middle'>
        ZZZZZZZZZZ;

        $out .= OlzTermineFilter::render();

        $has_access = $this->authUtils()->hasPermission('termine');
        if ($has_access) {
            $out .= <<<ZZZZZZZZZZ
            <button
                id='create-termin-button'
                class='btn btn-secondary create-termin-container'
                onclick='return olz.initOlzEditTerminModal()'
            >
                <img src='{$code_href}assets/icns/new_white_16.svg' class='noborder' />
                Neuer Termin
            </button>
            ZZZZZZZZZZ;
        }

        $out .= "<h1>{$termine_list_title}</h1>";

        // -------------------------------------------------------------
        //  VORSCHAU - LISTE
        $inner_date_filter = $termine_utils->getSqlDateRangeFilter($current_filter, 't');
        $inner_sql_where = <<<ZZZZZZZZZZ
        (t.on_off = '1')
        AND ({$inner_date_filter})
        ZZZZZZZZZZ;
        $type_filter = $termine_utils->getSqlTypeFilter($current_filter, 'c');
        $outer_date_filter = $termine_utils->getSqlDateRangeFilter($current_filter, 'c');
        $outer_sql_where = "({$type_filter}) AND ({$outer_date_filter})";

        $sql = <<<ZZZZZZZZZZ
        SELECT * FROM ((
            SELECT
                'termin' as item_type,
                t.owner_user_id as owner_user_id,
                t.start_date as start_date,
                t.start_time as start_time,
                t.end_date as end_date,
                t.end_time as end_time,
                t.title as title,
                t.text as text,
                t.link as link,
                t.id as id,
                t.typ as typ,
                t.on_off as on_off,
                t.newsletter as newsletter,
                t.xkoord as xkoord,
                t.ykoord as ykoord,
                t.go2ol as go2ol,
                t.solv_uid as solv_uid,
                t.last_modified_by_user_id as last_modified_by_user_id,
                t.image_ids as image_ids,
                t.location_id as location_id
            FROM termine t
            WHERE ({$inner_sql_where})
        ) UNION ALL (
            SELECT
                'solv_deadline' as item_type,
                t.owner_user_id as owner_user_id,
                se.deadline as start_date,
                '00:00:00' as start_time,
                NULL as end_date,
                NULL as end_time,
                CONCAT('Meldeschluss für ', t.title) as title,
                '' as text,
                '' as link,
                t.id as id,
                'meldeschluss' as typ,
                t.on_off as on_off,
                NULL as newsletter,
                NULL as xkoord,
                NULL as ykoord,
                t.go2ol as go2ol,
                t.solv_uid as solv_uid,
                t.last_modified_by_user_id as last_modified_by_user_id,
                t.image_ids as image_ids,
                NULL as location_id
            FROM termine t JOIN solv_events se ON (t.solv_uid = se.solv_uid)
            WHERE (se.deadline IS NOT NULL) AND ({$inner_sql_where})
        ) UNION ALL (
            SELECT
                'olz_deadline' as item_type,
                t.owner_user_id as owner_user_id,
                DATE(t.deadline) as start_date,
                TIME(t.deadline) as start_time,
                NULL as end_date,
                NULL as end_time,
                CONCAT('Meldeschluss für ', t.title) as title,
                '' as text,
                '' as link,
                t.id as id,
                'meldeschluss' as typ,
                t.on_off as on_off,
                NULL as newsletter,
                NULL as xkoord,
                NULL as ykoord,
                t.go2ol as go2ol,
                t.solv_uid as solv_uid,
                t.last_modified_by_user_id as last_modified_by_user_id,
                t.image_ids as image_ids,
                NULL as location_id
            FROM termine t
            WHERE (t.deadline IS NOT NULL) AND ({$inner_sql_where})
        )) AS c
        WHERE ({$outer_sql_where})
        ORDER BY c.start_date ASC
        ZZZZZZZZZZ;

        $result = $db->query($sql);

        $last_month = null;
        while ($row = $result->fetch_assoc()) {
            $this_month = substr($row['start_date'], 0, 7);
            if ($this_month !== $last_month) {
                $pretty_month = $this->dateUtils()->olzDate("MM jjjj", "{$this_month}-01");
                $out .= "<h3 class='tablebar'>{$pretty_month}</h3>";
            }
            $last_month = $this_month;

            $out .= OlzTermineListItem::render([
                'item_type' => $row['item_type'],
                'id' => $row['id'],
                'owner_user_id' => $row['owner_user_id'],
                'start_date' => $row['start_date'],
                'start_time' => $row['start_time'],
                'end_date' => $row['end_date'],
                'end_time' => $row['end_time'],
                'title' => $row['title'],
                'text' => $row['text'],
                'link' => $row['link'],
                'solv_uid' => $row['solv_uid'],
                'types' => explode(' ', $row['typ']),
                'image_ids' => $row['image_ids'] ? json_decode($row['image_ids'], true) : null,
                'location_id' => $row['location_id'],
            ]);
        }
        $out .= "</div>";

        $out .= OlzFooter::render();

        return $out;
    }
}
