<?php

namespace Olz\News\Components\OlzNewsList;

use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Entity\News\NewsEntry;
use Olz\Entity\Role;
use Olz\Entity\User;
use Olz\News\Components\OlzNewsArticle\OlzNewsArticle;
use Olz\News\Components\OlzNewsFilter\OlzNewsFilter;
use Olz\News\Components\OlzNewsListItem\OlzNewsListItem;
use Olz\News\Utils\NewsFilterUtils;
use Olz\Utils\DbUtils;
use Olz\Utils\HttpUtils;

class OlzNewsList {
    public static function render($args = []) {
        global $db_table, $_SESSION;

        $db = DbUtils::fromEnv()->getDb();
        $entityManager = DbUtils::fromEnv()->getEntityManager();
        $db_table = 'aktuell';

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

        $out .= OlzHeader::render([
            'title' => "Aktuell",
            'description' => "Aktuelle Beiträge, Berichte von Anlässen und weitere Neuigkeiten von der OL Zimmerberg.",
            'norobots' => !$allow_robots,
        ]);

        $zugriff = ((($_SESSION['auth'] ?? null) == 'all') or in_array($db_table, preg_split('/ /', $_SESSION['auth'] ?? ''))) ? '1' : '0';

        $out .= "<div class='content-right'>";
        $out .= "<h2>Filter</h2>";
        $out .= OlzNewsFilter::render([]);
        $out .= "</div>";
        $out .= "<div class='content-middle'>";
        $out .= "<form method='post' action='aktuell.php#id_edit".($_SESSION['id_edit'] ?? '')."' enctype='multipart/form-data'>";

        $news_list_title = $news_utils->getTitleFromFilter($current_filter);
        $out .= "<h1>{$news_list_title}</h1>";

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
            <div>
                <button
                    id='create-news-button'
                    class='btn btn-primary'
                    onclick='return olz.initOlzEditNewsModal()'
                >
                    <img src='icns/new_white_16.svg' class='noborder' />
                    Neuer Eintrag
                </button>
                &nbsp; <b>&lt;&ndash; Neu! &mdash;</b> <a href='https://youtu.be/w33dL0ntGWs' target='_blank'>Demo-Video auf YouTube</a>. <a href='#' onclick='document.getElementById(&quot;news-buttonbar&quot;).style.display = &quot;block&quot;' style='color:red; text-decoration:underline;' id='does-not-work-link'>Funktioniert nicht!</a>
            </div>
            ZZZZZZZZZZ;
            $out .= "<div id='news-buttonbar' class='buttonbar' style='display: none;'>\n<div>Bitte dem Sysadmin melden, dass es nicht funktioniert. <b><span style='color:red;'>!!! wird Ende Jahr abgeschaltet !!!</span></b>.</div>".olz_buttons("button".$db_table, [["Neuer Eintrag", "0"]], "")."</div>";
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
            typ,
            author_user_id,
            author_role_id,
            autor,
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

        $user_repo = $entityManager->getRepository(User::class);
        $role_repo = $entityManager->getRepository(Role::class);

        while ($row = $res->fetch_assoc()) {
            // TODO: Directly use doctrine to run the DB query.
            $author_user = $row['author_user_id'] ?
                $user_repo->findOneBy(['id' => $row['author_user_id']]) : null;
            $author_role = $row['author_role_id'] ?
                $role_repo->findOneBy(['id' => $row['author_role_id']]) : null;

            $news_entry = new NewsEntry();
            $news_entry->setDate($row['datum']);
            $news_entry->setFormat($row['typ']);
            $news_entry->setAuthorUser($author_user);
            $news_entry->setAuthorRole($author_role);
            $news_entry->setAuthor($row['autor']);
            $news_entry->setTitle($row['titel']);
            $news_entry->setTeaser($row['text']);
            $news_entry->setId($row['id']);
            $news_entry->setImageIds($row['image_ids'] ? json_decode($row['image_ids'], true) : null);

            $out .= OlzNewsListItem::render(['news_entry' => $news_entry]);
        }

        $out .= "</form>";
        $out .= "</div>";

        $out .= OlzFooter::render();

        return $out;
    }
}
