<?php

// =============================================================================
// Aktuelle Berichte von offiziellen Vereinsorganen.
// =============================================================================

namespace Olz\News\Components\OlzNewsDetail;

use Doctrine\Common\Collections\Criteria;
use Olz\Components\Common\OlzAuthorBadge\OlzAuthorBadge;
use Olz\Components\Common\OlzComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Entity\News\NewsEntry;
use Olz\Entity\Role;
use Olz\Entity\User;
use Olz\News\Components\OlzArticleMetadata\OlzArticleMetadata;
use Olz\News\Components\OlzNewsArticle\OlzNewsArticle;
use Olz\News\Utils\NewsFilterUtils;
use Olz\Utils\HttpUtils;
use PhpTypeScriptApi\Fields\FieldTypes;

class OlzNewsDetail extends OlzComponent {
    public function getHtml($args = []): string {
        global $_GET, $_POST, $_SERVER;

        require_once __DIR__.'/../../../../_/config/init.php';
        require_once __DIR__.'/../../../../_/config/date.php';

        session_start_if_cookie_set();

        require_once __DIR__.'/../../../../_/admin/olz_functions.php';

        $http_utils = HttpUtils::fromEnv();
        $http_utils->setLog($this->log());
        $http_utils->validateGetParams([
            'filter' => new FieldTypes\StringField(['allow_null' => true]),
        ], $_GET);

        $db = $this->dbUtils()->getDb();
        $entityManager = $this->dbUtils()->getEntityManager();
        $code_href = $this->envUtils()->getCodeHref();
        $db_table = 'aktuell';
        $id = $args['id'] ?? null;
        $host = str_replace('www.', '', $_SERVER['HTTP_HOST']);
        $canonical_url = "https://{$host}{$code_href}news/{$id}";

        $http_utils = HttpUtils::fromEnv();
        $article_metadata = "";
        try {
            $article_metadata = OlzArticleMetadata::render(['id' => $id]);
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

        $sql = "SELECT * FROM {$db_table} WHERE (id = '{$id}') ORDER BY datum DESC";
        $result = $db->query($sql);
        $row = $result->fetch_assoc();

        $title = $row['titel'] ?? '';
        $back_filter = urlencode($_GET['filter'] ?? '{}');
        $out .= OlzHeader::render([
            'back_link' => "{$code_href}news?filter={$back_filter}",
            'title' => "{$title} - Aktuell",
            'description' => "Aktuelle Beiträge, Berichte von Anlässen und weitere Neuigkeiten von der OL Zimmerberg.",
            'norobots' => $no_robots,
            'canonical_url' => $canonical_url,
            'additional_headers' => [
                $article_metadata,
            ],
        ]);

        $pretty_date = $this->dateUtils()->olzDate("tt.mm.jjjj", $row['datum']);
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
        <div class='content-right'>
            <div style='padding:4px 3px 10px 3px;'>
                <b>Datum: </b>{$pretty_date}<br />
                <b>Autor: </b>{$pretty_author}
            </div>
        </div>
        <div class='content-middle'>
        ZZZZZZZZZZ;

        $out .= OlzNewsArticle::render(['id' => $id]);

        $out .= "</div>";

        $out .= OlzFooter::render();

        return $out;
    }
}
