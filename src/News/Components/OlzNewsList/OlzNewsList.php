<?php

namespace Olz\News\Components\OlzNewsList;

use Olz\Apps\OlzApps;
use Olz\Components\Common\OlzRootComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Entity\News\NewsEntry;
use Olz\Entity\Roles\Role;
use Olz\Entity\Users\User;
use Olz\News\Components\OlzNewsFilter\OlzNewsFilter;
use Olz\News\Components\OlzNewsListItem\OlzNewsListItem;
use Olz\Utils\HttpParams;

/** @extends HttpParams<array{
 *   filter?: ?string,
 *   seite?: ?numeric-string,
 *   von?: ?string,
 * }> */
class OlzNewsListParams extends HttpParams {
}

/** @extends OlzRootComponent<array<string, mixed>> */
class OlzNewsList extends OlzRootComponent {
    public function getSearchTitle(): string {
        return 'TODO';
    }

    public function getSearchResults(array $terms): array {
        return [];
    }

    public static string $title = "News";
    public static string $description = "Aktuelle Beiträge, Berichte von Anlässen und weitere Neuigkeiten von der OL Zimmerberg.";

    public static int $page_size = 25;

    public function getHtml(mixed $args): string {
        $params = $this->httpUtils()->validateGetParams(OlzNewsListParams::class);
        $db = $this->dbUtils()->getDb();
        $entityManager = $this->dbUtils()->getEntityManager();
        $code_href = $this->envUtils()->getCodeHref();

        $news_utils = $this->newsUtils();
        $current_filter = $news_utils->deserialize($params['filter'] ?? $this->session()->get('news_filter') ?? '');
        $page_number = intval($params['seite'] ?? $this->session()->get('news_page') ?? 1);
        $page_index = $page_number - 1;

        if (!$news_utils->isValidFilter($current_filter)) {
            $valid_filter = $news_utils->getValidFilter($current_filter);
            $serialized_filter = $news_utils->serialize($valid_filter);
            $this->httpUtils()->redirect("{$code_href}news?filter={$serialized_filter}", empty($params['filter']) ? 308 : 410);
        }

        $valid_filter = $news_utils->getValidFilter($current_filter);
        $serialized_filter = $news_utils->serialize($valid_filter);

        $this->session()->set('news_filter', $serialized_filter);
        $this->session()->set('news_page', "{$page_number}");

        $news_list_title = $news_utils->getTitleFromFilter($valid_filter);
        $out = OlzHeader::render([
            'title' => $news_list_title,
            'description' => self::$description, // TODO: Filter-specific description?
            'canonical_url' => "{$code_href}news?filter={$serialized_filter}&seite={$page_number}",
        ]);

        $out .= "<div class='content-right'>";
        $out .= "<h2 class='optional'>Filter</h2>";
        $out .= OlzNewsFilter::render(['currentFilter' => $valid_filter]);
        $out .= "</div>";
        $out .= "<div class='content-middle olz-news-list-middle'>";

        $is_logged_in = $this->authUtils()->hasPermission('any');
        $has_blog = $this->authUtils()->hasPermission('kaderblog');
        $has_roles = !empty($this->authUtils()->getAuthenticatedRoles());
        $json_mode = htmlentities(json_encode($has_roles ? ($has_blog ? 'account_with_all' : 'account_with_aktuell') : ($has_blog ? 'account_with_blog' : 'account')) ?: '');
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

        $newsletter_link = '';
        $newsletter_app = OlzApps::getApp('Newsletter');
        if ($newsletter_app) {
            $newsletter_link = <<<ZZZZZZZZZZ
                <a href='{$code_href}{$newsletter_app->getHref()}' class='newsletter-link'>
                    <img
                        src='{$newsletter_app->getIcon()}'
                        alt='newsletter'
                        class='newsletter-link-icon'
                        title='Newsletter abonnieren!'
                    />
                </a>
                ZZZZZZZZZZ;
        } else {
            $this->log()->error('Newsletter App does not exist!');
        }
        $out .= "<h1>{$news_list_title} {$newsletter_link}</h1>";

        $filter_where = $news_utils->getSqlFromFilter($valid_filter);
        $first_index = $page_index * $this::$page_size;
        $sql = <<<ZZZZZZZZZZ
            SELECT
                COUNT(n.id) as count
            FROM news n
            WHERE
                {$filter_where}
                AND n.on_off='1'
            ORDER BY published_date DESC, published_time DESC
            ZZZZZZZZZZ;
        // @phpstan-ignore-next-line
        $count = intval($db->query($sql)->fetch_assoc()['count']);
        $num_pages = intval($count / $this::$page_size) + 1;

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
            ORDER BY published_date DESC, published_time DESC
            LIMIT {$first_index}, {$this::$page_size}
            ZZZZZZZZZZ;
        $res = $db->query($sql);

        $user_repo = $entityManager->getRepository(User::class);
        $role_repo = $entityManager->getRepository(Role::class);

        $page_content = '';
        // @phpstan-ignore-next-line
        for ($index = 0; $index < $res->num_rows; $index++) {
            // @phpstan-ignore-next-line
            $row = $res->fetch_assoc();

            // TODO: Directly use doctrine to run the DB query.
            // @phpstan-ignore-next-line
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
            // @phpstan-ignore-next-line
            $news_entry->setPublishedDate(new \DateTime($row['published_date']));
            // @phpstan-ignore-next-line
            $news_entry->setFormat($row['format']);
            $news_entry->setAuthorUser($author_user);
            $news_entry->setAuthorRole($author_role);
            // @phpstan-ignore-next-line
            $news_entry->setAuthorName($row['author_name']);
            // @phpstan-ignore-next-line
            $news_entry->setAuthorEmail($row['author_email']);
            // @phpstan-ignore-next-line
            $news_entry->setTitle($row['title']);
            // @phpstan-ignore-next-line
            $news_entry->setTeaser($row['teaser']);
            // @phpstan-ignore-next-line
            $news_entry->setContent($row['content']);
            $news_entry->setId(intval($row['id']));
            // @phpstan-ignore-next-line
            $news_entry->setImageIds($row['image_ids'] ? json_decode($row['image_ids'], true) : null);

            $page_content .= OlzNewsListItem::render([
                'json_mode' => $json_mode,
                'news_entry' => $news_entry,
            ]);
        }
        if ($page_content === '') {
            $page_content = "<div class='no-entries'>Keine Einträge. Bitte Filter anpassen.</div>";
        }
        $out .= $page_content;
        if ($num_pages > 1) {
            $pages = '';
            for ($page_number = 1; $page_number <= $num_pages; $page_number++) {
                $is_current_page = $page_number === $page_index + 1;
                $page_link_class = $is_current_page ? ' active' : '';
                $pages .= <<<ZZZZZZZZZZ
                    <li class='page-item'>
                        <a
                            class='page-link{$page_link_class}'
                            href='?filter={$serialized_filter}&seite={$page_number}'
                        >
                            {$page_number}
                        </a>
                    </li>
                    ZZZZZZZZZZ;
            }
            $out .= "<nav><ul class='no-style pagination justify-content-center'>{$pages}</ul></nav>";
        }

        $out .= "</div>";

        $out .= OlzFooter::render();

        return $out;
    }
}
