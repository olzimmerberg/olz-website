<?php

// =============================================================================
// Zeigt geplante und vergangene Termine an.
// =============================================================================

namespace Olz\Termine\Components\OlzTermineList;

use Olz\Components\Common\OlzEditableText\OlzEditableText;
use Olz\Components\Common\OlzRootComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Entity\Termine\Termin;
use Olz\Entity\Termine\TerminLabel;
use Olz\Termine\Components\OlzTermineFilter\OlzTermineFilter;
use Olz\Termine\Components\OlzTermineListItem\OlzTermineListItem;
use Olz\Utils\HttpParams;

/** @extends HttpParams<array{filter?: ?string, von?: ?string}> */
class OlzTermineListParams extends HttpParams {
}

/** @extends OlzRootComponent<array<string, mixed>> */
class OlzTermineList extends OlzRootComponent {
    public function getSearchTitle(): string {
        return 'Termin-Listen';
    }

    public function getSearchResults(array $terms): array {
        return [];
    }

    public static string $title = "Termine";
    public static string $description = "Orientierungslauf-Wettkämpfe, OL-Wochen, OL-Weekends, Trainings und Vereinsanlässe der OL Zimmerberg.";

    public function getHtml(mixed $args): string {
        /** @return array{filter?: ?string} */
        $params = $this->httpUtils()->validateGetParams(OlzTermineListParams::class);
        $db = $this->dbUtils()->getDb();
        $code_href = $this->envUtils()->getCodeHref();

        $current_filter = json_decode($params['filter'] ?? '{}', true);
        $termine_utils = $this->termineUtils()->loadTypeOptions();

        if (!$termine_utils->isValidFilter($current_filter)) {
            $valid_filter = $termine_utils->getValidFilter($current_filter);
            $enc_json_filter = urlencode(json_encode($valid_filter) ?: '{}');
            $this->httpUtils()->redirect("{$code_href}termine?filter={$enc_json_filter}", 410);
        }

        $termine_list_title = $termine_utils->getTitleFromFilter($current_filter);
        $enc_json_filter = urlencode(json_encode($current_filter) ?: '{}');

        $out = OlzHeader::render([
            'title' => $termine_list_title,
            'description' => self::$description, // TODO: Filter-specific description?
            'canonical_url' => "{$code_href}termine?filter={$enc_json_filter}",
        ]);

        $admin_menu_out = '';
        $has_termine_permissions = $this->authUtils()->hasPermission('termine');
        if ($has_termine_permissions) {
            $admin_menu_out = <<<ZZZZZZZZZZ
                <div class='termine-list-admin-menu'>
                    <span class='entry'>
                        <a href='{$code_href}termine/orte' class='linkmap'>
                            Termin-Orte
                        </a>
                    </span>
                    <span class='entry'>
                        <a href='{$code_href}termine/vorlagen' class='linkint'>
                            Termin-Vorlagen
                        </a>
                    </span>
                </div>
                ZZZZZZZZZZ;
        }
        $filter_out = OlzTermineFilter::render();
        $downloads_links_out = OlzEditableText::render(['snippet_id' => 2]);
        $newsletter_out = OlzEditableText::render(['snippet_id' => 3]);
        $out .= <<<ZZZZZZZZZZ
            <div class='content-right'>
                {$admin_menu_out}
                <h2 class='optional'>Filter</h2>
                {$filter_out}
                <div class='optional'>
                    <h2>Downloads und Links</h2>
                    {$downloads_links_out}
                </div>
                <div class='optional'>
                    <h2>Newsletter</h2>
                    {$newsletter_out}
                </div>
            </div>
            <div class='content-middle olz-termine-list-middle'>
            ZZZZZZZZZZ;

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

        $termin_repo = $this->entityManager()->getRepository(Termin::class);
        $termin_label_repo = $this->entityManager()->getRepository(TerminLabel::class);
        $termin_label = $termin_label_repo->findOneBy(['ident' => $current_filter['typ']]);
        $edit_admin = '';
        if ($termin_label) {
            $has_termine_permissions = $this->authUtils()->hasPermission('termine');
            if ($has_termine_permissions) {
                $json_id = json_encode($termin_label->getId());
                $edit_admin = <<<ZZZZZZZZZZ
                    <button
                        id='edit-termin-label-button'
                        class='btn btn-secondary-outline btn-sm'
                        onclick='return olz.termineListEditTerminLabel({$json_id})'
                    >
                        <img src='{$code_href}assets/icns/edit_16.svg' class='noborder' />
                    </button>
                    ZZZZZZZZZZ;
            }
        }
        $out .= "<h1>{$termine_list_title}{$edit_admin}</h1>";
        $details = $termin_label?->getDetails();
        if ($details) {
            $details_html = $this->htmlUtils()->renderMarkdown($details);
            $details_html = $termin_label->replaceImagePaths($details_html);
            $details_html = $termin_label->replaceFilePaths($details_html);
            $out .= $details_html;
        }

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
                    t.id as id,
                    (
                        SELECT GROUP_CONCAT(l.ident ORDER BY l.position ASC SEPARATOR ' ')
                        FROM
                            termin_label_map tl
                            JOIN termin_labels l ON (l.id = tl.label_id)
                        WHERE tl.termin_id = t.id
                        GROUP BY t.id
                    ) as typ,
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
                    'deadline' as item_type,
                    t.owner_user_id as owner_user_id,
                    DATE(t.deadline) as start_date,
                    TIME(t.deadline) as start_time,
                    NULL as end_date,
                    NULL as end_time,
                    CONCAT('Meldeschluss für ', t.title) as title,
                    '' as text,
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
        $today = $this->dateUtils()->getCurrentDateInFormat('Y-m-d');
        $meldeschluss_label = new TerminLabel();
        $meldeschluss_label->setIdent('meldeschluss');
        $meldeschluss_label->setIcon(null);
        $last_date = null;
        // @phpstan-ignore-next-line
        while ($row = $result->fetch_assoc()) {
            $this_date = $row['start_date'];
            // @phpstan-ignore-next-line
            $this_month_start = $this->getMonth($this_date).'-01';

            if ($today < $this_month_start && $today > $last_date) {
                $out .= "<div class='bar today'>Heute</div>";
            }
            // @phpstan-ignore-next-line
            if ($this->getMonth($this_date) !== $this->getMonth($last_date)) {
                // @phpstan-ignore-next-line
                $pretty_month = $this->dateUtils()->olzDate("MM jjjj", $this_date);
                $out .= "<h3 class='bar green'>{$pretty_month}</h3>";
            }
            if ($today <= $this_date && $today > $last_date && $today >= $this_month_start) {
                $out .= "<div class='bar today'>Heute</div>";
            }
            $labels = [$meldeschluss_label];
            if ($row['item_type'] === 'termin') {
                $termin = $termin_repo->findOneBy(['id' => $row['id']]);
                $labels = [...($termin?->getLabels() ?? [])];
            }

            $out .= OlzTermineListItem::render([
                'id' => $row['id'],
                'owner_user_id' => $row['owner_user_id'],
                'start_date' => $row['start_date'],
                'start_time' => $row['start_time'],
                'end_date' => $row['end_date'],
                'end_time' => $row['end_time'],
                'title' => $row['title'],
                'text' => $row['text'],
                'solv_uid' => $row['solv_uid'],
                'labels' => $labels,
                // @phpstan-ignore-next-line
                'image_ids' => $row['image_ids'] ? json_decode($row['image_ids'], true) : null,
                'location_id' => $row['location_id'],
            ]);

            $last_date = $this_date;
        }
        if ($last_date === null) {
            $out .= "<div class='no-entries'>Keine Einträge. Bitte Filter anpassen.</div>";
        }
        $out .= "</div>";

        $out .= OlzFooter::render();

        return $out;
    }

    protected function getMonth(?string $date): ?string {
        if ($date === null) {
            return null;
        }
        return substr($date, 0, 7);
    }
}
