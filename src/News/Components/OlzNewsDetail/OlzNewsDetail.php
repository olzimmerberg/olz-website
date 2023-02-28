<?php

// =============================================================================
// Aktuelle Berichte von offiziellen Vereinsorganen.
// =============================================================================

namespace Olz\News\Components\OlzNewsDetail;

use Doctrine\Common\Collections\Criteria;
use Olz\Components\Common\OlzAuthorBadge\OlzAuthorBadge;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Entity\News\NewsEntry;
use Olz\Entity\Role;
use Olz\Entity\User;
use Olz\News\Components\OlzArticleMetadata\OlzArticleMetadata;
use Olz\News\Components\OlzNewsArticle\OlzNewsArticle;
use Olz\News\Utils\NewsFilterUtils;
use Olz\Utils\DbUtils;
use Olz\Utils\EnvUtils;
use Olz\Utils\HttpUtils;

class OlzNewsDetail {
    public static function render($args = []) {
        global $db_table, $id, $_DATE, $_GET, $_POST, $_SESSION, $_SERVER;

        require_once __DIR__.'/../../../../_/config/date.php';

        $env_utils = EnvUtils::fromEnv();
        $db = DbUtils::fromEnv()->getDb();
        $entityManager = DbUtils::fromEnv()->getEntityManager();
        $code_href = $env_utils->getCodeHref();
        $db_table = 'aktuell';
        $id = $_GET['id'] ?? null;
        $host = str_replace('www.', '', $_SERVER['HTTP_HOST']);
        $canonical_url = "https://{$host}{$code_href}aktuell.php?id={$id}";

        $http_utils = HttpUtils::fromEnv();
        $article_metadata = "";
        try {
            $article_metadata = OlzArticleMetadata::render($id);
        } catch (\Exception $exc) {
            $http_utils->dieWithHttpError(404);
        }

        $news_utils = NewsFilterUtils::fromEnv();
        $news_repo = $entityManager->getRepository(NewsEntry::class);
        $role_repo = $entityManager->getRepository(Role::class);
        $user_repo = $entityManager->getRepository(User::class);
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

        $title = $row['titel'] ?? '';
        $back_filter = urlencode($_GET['filter'] ?? '{}');
        $out .= OlzHeader::render([
            'back_link' => "{$code_href}aktuell.php?filter={$back_filter}",
            'title' => "{$title} - Aktuell",
            'description' => "Aktuelle Beiträge, Berichte von Anlässen und weitere Neuigkeiten von der OL Zimmerberg.",
            'norobots' => $no_robots,
            'canonical_url' => $canonical_url,
            'additional_headers' => [
                $article_metadata,
            ],
        ]);

        $id_edit = $_SESSION['id_edit'] ?? ''; // TODO: Entfernen?
        $pretty_date = $_DATE->olzDate("tt.mm.jjjj", $row['datum']);
        $author_user = $row['author_user_id'] ?
            $user_repo->findOneBy(['id' => $row['author_user_id']]) : null;
        $author_role = $row['author_role_id'] ?
            $role_repo->findOneBy(['id' => $row['author_role_id']]) : null;
        $author_name = $row['autor'];
        $author_email = $row['autor_email'];
        $pretty_author = OlzAuthorBadge::render([
            'user' => $author_user,
            'role' => $author_role,
            'name' => $author_name,
            'email' => $author_email,
        ]);

        $out .= <<<ZZZZZZZZZZ
        <div class='content-right optional'>
            <div style='padding:4px 3px 10px 3px;'>
                <b>Datum: </b>{$pretty_date}<br />
                <b>Autor: </b>{$pretty_author}
            </div>
        </div>
        <div class='content-middle'>
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
