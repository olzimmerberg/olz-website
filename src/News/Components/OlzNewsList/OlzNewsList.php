<?php

namespace Olz\News\Components\OlzNewsList;

use Olz\Entity\News\NewsEntry;
use Olz\News\Components\OlzNewsArticle\OlzNewsArticle;
use Olz\News\Components\OlzNewsFilter\OlzNewsFilter;
use Olz\News\Components\OlzNewsListItem\OlzNewsListItem;
use Olz\News\Utils\NewsFilterUtils;
use Olz\Utils\HttpUtils;

class OlzNewsList {
    public static function render($args = []) {
        global $db_table, $db, $_SESSION;

        $db_table = 'aktuell';

        require_once __DIR__.'/../../../../_/components/common/olz_posting_list_item/olz_posting_list_item.php';

        $http_utils = HttpUtils::fromEnv();
        $news_utils = NewsFilterUtils::fromEnv();
        $current_filter = json_decode($_GET['filter'] ?? '{}', true);

        if (!$news_utils->isValidFilter($current_filter)) {
            $enc_json_filter = urlencode(json_encode($news_utils->getDefaultFilter()));
            $http_utils->redirect("?filter={$enc_json_filter}", 308);
        }

        $is_not_archived = $news_utils->isFilterNotArchived($current_filter);
        $allow_robots = $is_not_archived;

        $out = '';

        require_once __DIR__.'/../../../../_/components/page/olz_header/olz_header.php';
        $out .= olz_header([
            'title' => "Aktuell",
            'description' => "Aktuelle Beiträge, Berichte von Anlässen und weitere Neuigkeiten von der OL Zimmerberg.",
            'norobots' => !$allow_robots,
        ]);

        $zugriff = ((($_SESSION['auth'] ?? null) == 'all') or (in_array($db_table, preg_split('/ /', $_SESSION['auth'] ?? '')))) ? '1' : '0';

        $out .= "<div id='content_rechts'>";
        $out .= "<h2>Filter</h2>";
        $out .= OlzNewsFilter::render([]);
        $out .= "</div>";
        $out .= "<div id='content_mitte'>";
        $out .= "<form method='post' action='aktuell.php#id_edit".($_SESSION['id_edit'] ?? '')."' enctype='multipart/form-data'>";

        // -------------------------------------------------------------
        // DATENSATZ EDITIEREN
        if ($zugriff) {
            $functions = [
                'neu' => 'Neuer Eintrag',
                'edit' => 'Bearbeiten',
                'abbruch' => 'Abbrechen',
                'vorschau' => 'Vorschau',
                'save' => 'Speichern',
                'delete' => 'Löschen',
                'start' => 'start',
                'upload' => 'Upload',
                'deletebild1' => 'BILD 1 entfernen',
                'deletebild2' => 'BILD 2 entfernen',
                'deletebild3' => 'BILD 3 entfernen',
                'undo' => 'undo',
            ];
        } else {
            $functions = [];
        }
        $button_name = 'button'.$db_table;
        $function = array_search($_POST[$button_name] ?? null, $functions);
        if ($function != "") {
            ob_start();
            include __DIR__.'/../../../../_/admin/admin_db.php';
            $out .= ob_get_contents();
            ob_end_clean();
        }
        if (($_SESSION['edit']['table'] ?? null) == $db_table) {
            $db_edit = "1";
        } else {
            $db_edit = "0";
        }

        // -------------------------------------------------------------
        // MENÜ
        if ($zugriff and ($db_edit == '0')) {
            $out .= <<<'ZZZZZZZZZZ'
            <div class='feature news'>
                <button
                    id='create-news-button'
                    class='btn btn-primary'
                    onclick='return initOlzEditNewsModal()'
                >
                    <img src='icns/new_white_16.svg' class='noborder' />
                    Neuer Eintrag
                </button>
                &nbsp; &lt;-- Neu! &mdash; Falls es nicht funktioniert, "Neuer Eintrag" unten (grün) klicken.
            </div>
            ZZZZZZZZZZ;
            $out .= "<div class='buttonbar'>\n".olz_buttons("button".$db_table, [["Neuer Eintrag", "0"]], "")."</div>";
        }

        if (($do ?? null) == 'vorschau') {
            $id = $_SESSION[$db_table."id"];
            $out .= OlzNewsArticle::render([
                'id' => $id,
                'row' => $vorschau,
                'can_edit' => $zugriff,
                'is_preview' => true,
            ]);
            $out .= "<hr /><br/><br/>";
        }

        $filter_where = $news_utils->getSqlFromFilter($current_filter);
        $sql = <<<ZZZZZZZZZZ
        SELECT
            id,
            datum,
            zeit,
            titel,
            text,
            image_ids
        FROM aktuell n
        WHERE
            {$filter_where}
            AND n.on_off='1'
            AND n.typ NOT LIKE 'box%'
        ORDER BY datum DESC, zeit DESC
        ZZZZZZZZZZ;
        $res = $db->query($sql);

        while ($row = $res->fetch_assoc()) {
            // TODO: Directly use doctrine to run the DB query.
            $news_entry = new NewsEntry();
            $news_entry->setDate($row['datum']);
            $news_entry->setTitle($row['titel']);
            $news_entry->setTeaser($row['text']);
            $news_entry->setId($row['id']);
            $news_entry->setImageIds(json_decode($row['image_ids'] ?? '[]', true));

            $out .= OlzNewsListItem::render(['news_entry' => $news_entry]);
        }

        $out .= "</form>";
        $out .= "</div>";

        require_once __DIR__.'/../../../../_/components/page/olz_footer/olz_footer.php';
        $out .= olz_footer();

        return $out;
    }
}
