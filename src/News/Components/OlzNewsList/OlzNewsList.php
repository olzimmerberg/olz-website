<?php

namespace Olz\News\Components\OlzNewsList;

use Olz\Components\Common\OlzComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Entity\News\NewsEntry;
use Olz\Entity\Role;
use Olz\Entity\User;
use Olz\News\Components\OlzNewsFilter\OlzNewsFilter;
use Olz\News\Components\OlzNewsListItem\OlzNewsListItem;
use Olz\News\Utils\NewsFilterUtils;
use Olz\Utils\HttpUtils;

class OlzNewsList extends OlzComponent {
    public function getHtml($args = []): string {
        global $db_table, $_SESSION;

        $db = $this->dbUtils()->getDb();
        $entityManager = $this->dbUtils()->getEntityManager();
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

        $out .= "<div class='content-right'>";
        $out .= "<h2>Filter</h2>";
        $out .= OlzNewsFilter::render([]);
        $out .= "</div>";
        $out .= "<div class='content-middle'>";

        $is_logged_in = $this->authUtils()->hasPermission('any');
        $has_blog = $this->authUtils()->hasPermission('kaderblog');
        $json_mode = htmlentities(json_encode($has_blog ? 'account_with_blog' : 'account'));
        $class = $is_logged_in ? ' create-news-container' : ' dropdown-toggle';
        $properties = $is_logged_in
            ? <<<ZZZZZZZZZZ
            onclick='return olz.initOlzEditNewsModal({$json_mode})'
            ZZZZZZZZZZ
            : <<<'ZZZZZZZZZZ'
            type='button'
            data-bs-toggle='dropdown'
            aria-expanded='false'
            ZZZZZZZZZZ;
        if (!$is_logged_in) {
            $out .= "<div class='dropdown create-news-container'>";
        }
        $out .= <<<ZZZZZZZZZZ
        <button
            id='create-news-button'
            class='btn btn-secondary{$class}'
            {$properties}
        >
            <img src='icns/new_white_16.svg' class='noborder' />
            Neuer Eintrag
        </button>
        ZZZZZZZZZZ;
        if (!$is_logged_in) {
            $out .= <<<'ZZZZZZZZZZ'
            <div
                class='dropdown-menu dropdown-menu-end'
                aria-labelledby='create-news-button'
            >
                <li><button
                    class='dropdown-item'
                    onclick='return olz.olzLoginModalShow()'
                >
                    Login
                </button></li>
                <li><hr class="dropdown-divider"></li>
                <li><div class='dropdown-item disabled should-login'>
                    <b>Achtung</b>: Bild-Upload und nachträgliches Bearbeiten des Eintrags nur mit Login möglich!
                </div></li>
                <li><button
                    id='create-anonymous-button'
                    class='dropdown-item'
                    onclick='return olz.initOlzEditNewsModal(&quot;anonymous&quot;)'
                >
                    Forumseintrag ohne Login
                </button></li>
            </div>
            ZZZZZZZZZZ;
            $out .= "</div>";
        }

        $news_list_title = $news_utils->getTitleFromFilter($current_filter);
        $out .= "<h1>{$news_list_title}</h1>";

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
            autor_email,
            titel,
            text,
            textlang,
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

        $row = true;
        $invisible_page_contents = [];
        for ($page = 0; $row; $page++) {
            $page_content = '';
            for ($index = 0; $index < 25 && $row; $index++) {
                $row = $res->fetch_assoc();
                if (!$row) {
                    break;
                }
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
                $news_entry->setAuthorName($row['autor']);
                $news_entry->setAuthorEmail($row['autor_email']);
                $news_entry->setTitle($row['titel']);
                $news_entry->setTeaser($row['text']);
                $news_entry->setContent($row['textlang']);
                $news_entry->setId($row['id']);
                $news_entry->setImageIds($row['image_ids'] ? json_decode($row['image_ids'], true) : null);

                $page_content .= OlzNewsListItem::render(['news_entry' => $news_entry]);
            }
            if ($page === 0) {
                if ($page_content === '') {
                    $page_content = "<div class='no-entries'>Keine Einträge. Bitte Filter anpassen.</div>";
                }
                $out .= "<div id='news-list-page-{$page}' class='page'>{$page_content}</div>";
            } else {
                $out .= "<div id='news-list-page-{$page}' class='page'>&nbsp;</div>";
                $invisible_page_contents[] = $page_content;
            }
        }
        if (count($invisible_page_contents) > 0) {
            $json = json_encode($invisible_page_contents);
            $out .= <<<ZZZZZZZZZZ
            <script>
            window.addEventListener('load', () => {
                olz.olzNewsListSetInvisiblePageContents({$json});
            });
            </script>
            ZZZZZZZZZZ;
        }

        $out .= "</div>";

        $out .= OlzFooter::render();

        return $out;
    }
}
