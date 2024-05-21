<?php

namespace Olz\News\Components\OlzNewsList;

use Olz\Components\Common\OlzComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Entity\News\NewsEntry;
use Olz\Entity\Roles\Role;
use Olz\Entity\User;
use Olz\News\Components\OlzNewsFilter\OlzNewsFilter;
use Olz\News\Components\OlzNewsListItem\OlzNewsListItem;
use Olz\News\Utils\NewsFilterUtils;
use PhpTypeScriptApi\Fields\FieldTypes;

class OlzNewsList extends OlzComponent {
    public static $title = "News";
    public static $description = "Aktuelle Beiträge, Berichte von Anlässen und weitere Neuigkeiten von der OL Zimmerberg.";

    public function getHtml($args = []): string {
        $this->httpUtils()->validateGetParams([
            'filter' => new FieldTypes\StringField(['allow_null' => true]),
        ]);
        $db = $this->dbUtils()->getDb();
        $entityManager = $this->dbUtils()->getEntityManager();

        $news_utils = NewsFilterUtils::fromEnv();
        $current_filter = json_decode($this->getParams()['filter'] ?? '{}', true);

        if (!$news_utils->isValidFilter($current_filter)) {
            $valid_filter = $news_utils->getValidFilter($current_filter);
            $enc_json_filter = urlencode(json_encode($valid_filter));
            $this->httpUtils()->redirect("?filter={$enc_json_filter}", 308);
        }

        $is_not_archived = $news_utils->isFilterNotArchived($current_filter);
        $allow_robots = $is_not_archived;

        $host = str_replace('www.', '', $this->server()['HTTP_HOST']);
        $code_href = $this->envUtils()->getCodeHref();
        $enc_json_filter = urlencode(json_encode($current_filter));
        $canonical_url = "https://{$host}{$code_href}news?filter={$enc_json_filter}";
        $news_list_title = $news_utils->getTitleFromFilter($current_filter);
        $out = OlzHeader::render([
            'title' => $news_list_title,
            'description' => self::$description, // TODO: Filter-specific description?
            'norobots' => !$allow_robots,
            'canonical_url' => $canonical_url,
        ]);

        $out .= "<div class='content-right'>";
        $out .= "<h2 class='optional'>Filter</h2>";
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
                <img src='{$code_href}assets/icns/new_white_16.svg' class='noborder' />
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
                        onclick='return olz.initOlzLoginModal({})'
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

        $out .= "<h1>{$news_list_title}</h1>";

        $filter_where = $news_utils->getSqlFromFilter($current_filter);
        $sql = <<<ZZZZZZZZZZ
            SELECT
                id,
                owner_user_id,
                owner_role_id,
                published_date,
                published_time,
                format,
                author_user_id,
                author_role_id,
                author_name,
                author_email,
                title,
                teaser,
                content,
                image_ids
            FROM news n
            WHERE
                {$filter_where}
                AND n.on_off='1'
                AND n.format NOT LIKE 'box%'
            ORDER BY published_date DESC, published_time DESC
            ZZZZZZZZZZ;
        $res = $db->query($sql);

        $user_repo = $entityManager->getRepository(User::class);
        $role_repo = $entityManager->getRepository(Role::class);

        $has_archive_access = $this->authUtils()->hasPermission('verified_email');
        if ($is_not_archived || $has_archive_access) {
            $row = true;
            $invisible_page_contents = [];
            for ($page = 0; $row; $page++) {
                $page_content = '';
                for ($index = 0; $index < 25; $index++) {
                    $row = $res->fetch_assoc();
                    if (!$row) {
                        break;
                    }
                    // TODO: Directly use doctrine to run the DB query.
                    $owner_user = $row['owner_user_id'] ?
                    $user_repo->findOneBy(['id' => $row['owner_user_id']]) : null;
                    $owner_role = $row['owner_role_id'] ?
                    $role_repo->findOneBy(['id' => $row['owner_role_id']]) : null;
                    $author_user = $row['author_user_id'] ?
                    $user_repo->findOneBy(['id' => $row['author_user_id']]) : null;
                    $author_role = $row['author_role_id'] ?
                    $role_repo->findOneBy(['id' => $row['author_role_id']]) : null;

                    $news_entry = new NewsEntry();
                    $news_entry->setOwnerUser($owner_user);
                    $news_entry->setOwnerRole($owner_role);
                    $news_entry->setPublishedDate($row['published_date']);
                    $news_entry->setFormat($row['format']);
                    $news_entry->setAuthorUser($author_user);
                    $news_entry->setAuthorRole($author_role);
                    $news_entry->setAuthorName($row['author_name']);
                    $news_entry->setAuthorEmail($row['author_email']);
                    $news_entry->setTitle($row['title']);
                    $news_entry->setTeaser($row['teaser']);
                    $news_entry->setContent($row['content']);
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
        } else {
            $out .= <<<ZZZZZZZZZZ
                <div class='olz-no-access'>
                    <div>Das Archiv ist nur für Vereins-Mitglieder verfügbar.</div>
                    <div class='auth-buttons'>
                        <a class='btn btn-primary' href='#login-dialog' role='button'>Login</a>
                        <a class='btn btn-secondary' href='{$code_href}konto_passwort' role='button'>Konto erstellen</a>
                    </div>
                </div>
                ZZZZZZZZZZ;
        }

        $out .= "</div>";

        $out .= OlzFooter::render();

        return $out;
    }
}
