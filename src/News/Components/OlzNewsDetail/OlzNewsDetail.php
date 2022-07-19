<?php

// =============================================================================
// Aktuelle Berichte von offiziellen Vereinsorganen.
// =============================================================================

namespace Olz\News\Components\OlzNewsDetail;

use Doctrine\Common\Collections\Criteria;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Entity\News\NewsEntry;
use Olz\News\Components\OlzArticleMetadata\OlzArticleMetadata;
use Olz\News\Components\OlzNewsArticle\OlzNewsArticle;
use Olz\News\Utils\NewsFilterUtils;
use Olz\Utils\HttpUtils;

class OlzNewsDetail {
    public static function render($args = []) {
        global $db_table, $id, $db, $_DATE, $_GET, $_POST, $_SESSION;

        require_once __DIR__.'/../../../../_/config/database.php';
        require_once __DIR__.'/../../../../_/config/date.php';
        require_once __DIR__.'/../../../../_/config/doctrine_db.php';

        $db_table = 'aktuell';
        $id = $_GET['id'] ?? null;

        $http_utils = HttpUtils::fromEnv();
        $article_metadata = "";
        try {
            $article_metadata = OlzArticleMetadata::render($id);
        } catch (\Exception $exc) {
            $http_utils->dieWithHttpError(404);
        }

        $news_utils = NewsFilterUtils::fromEnv();
        $news_repo = $entityManager->getRepository(NewsEntry::class);
        $is_not_archived = $news_utils->getIsNotArchivedCriteria();
        $criteria = Criteria::create()
            ->where(Criteria::expr()->andX(
                $is_not_archived,
                Criteria::expr()->eq('id', $id),
            ))
            ->setFirstResult(0)
            ->setMaxResults(1)
        ;
        $news_entries = $news_repo->matching($criteria);
        $num_news_entries = $news_entries->count();
        $no_robots = $num_news_entries !== 1;

        $out = '';

        $out .= OlzHeader::render([
            'title' => "Aktuell",
            'description' => "Aktuelle Beiträge, Berichte von Anlässen und weitere Neuigkeiten von der OL Zimmerberg.",
            'norobots' => $no_robots,
            'additional_headers' => [
                $article_metadata,
            ],
        ]);

        $button_name = 'button'.$db_table;
        if (isset($_GET[$button_name])) {
            $_POST[$button_name] = $_GET[$button_name];
        }
        if (isset($_POST[$button_name])) {
            $_SESSION['edit']['db_table'] = $db_table;
        }

        $zugriff = ((($_SESSION['auth'] ?? null) == 'all') or in_array($db_table, preg_split('/ /', $_SESSION['auth'] ?? ''))) ? '1' : '0';

        $sql = "SELECT * FROM {$db_table} WHERE (id = '{$id}') ORDER BY datum DESC";
        $result = $db->query($sql);
        $row = $result->fetch_assoc();
        $pretty_date = $_DATE->olzDate("tt.mm.jjjj", $row['datum']);
        $pretty_author = $row['autor'];

        $id_edit = $_SESSION['id_edit'] ?? ''; // TODO: Entfernen?
        $out .= <<<ZZZZZZZZZZ
        <div id='content_rechts' class='optional'>
            <div style='padding:4px 3px 10px 3px;'>
                <b>Datum: </b>{$pretty_date}<br />
                <b>Autor: </b>{$pretty_author}
            </div>
        </div>
        <div id='content_mitte'>
        <form name='Formularl' method='post' action='aktuell.php?id={$id}#id_edit{$id_edit}' enctype='multipart/form-data'>
        ZZZZZZZZZZ;

        // -------------------------------------------------------------
        // DATENSATZ EDITIEREN
        if ($zugriff) {
            $functions = ['neu' => 'Neuer Eintrag',
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
                'undo' => 'undo', ];
        } else {
            $functions = [];
        }
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
            $out .= "<div class='buttonbar'>\n".olz_buttons("button".$db_table, [["Neuer Eintrag", "0"]], "")."</div>";
        }

        // -------------------------------------------------------------
        // AKTUELL - VORSCHAU
        if (($db_edit == "0") or (($do ?? null) == 'vorschau')) {
            $out .= OlzNewsArticle::render([
                'id' => $id,
                'can_edit' => $zugriff,
                'is_preview' => (($do ?? null) == 'vorschau'),
            ]);
        }

        $out .= "</form>
        </div>";

        $out .= OlzFooter::render();

        return $out;
    }
}
