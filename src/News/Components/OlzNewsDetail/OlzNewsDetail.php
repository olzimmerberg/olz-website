<?php

// =============================================================================
// Alle Neuigkeiten rund um die OL Zimmerberg
// =============================================================================

namespace Olz\News\Components\OlzNewsDetail;

use Doctrine\Common\Collections\Criteria;
use Olz\Components\Common\OlzAuthorBadge\OlzAuthorBadge;
use Olz\Components\Common\OlzComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Entity\News\NewsEntry;
use Olz\Entity\Roles\Role;
use Olz\Entity\User;
use Olz\News\Components\OlzArticleMetadata\OlzArticleMetadata;
use Olz\News\Utils\NewsFilterUtils;
use Olz\Utils\FileUtils;
use Olz\Utils\HtmlUtils;
use Olz\Utils\ImageUtils;
use PhpTypeScriptApi\Fields\FieldTypes;

class OlzNewsDetail extends OlzComponent {
    public function getHtml($args = []): string {
        $this->httpUtils()->validateGetParams([
            'filter' => new FieldTypes\StringField(['allow_null' => true]),
        ]);
        $code_href = $this->envUtils()->getCodeHref();
        $db = $this->dbUtils()->getDb();
        $entityManager = $this->dbUtils()->getEntityManager();
        $file_utils = FileUtils::fromEnv();
        $image_utils = ImageUtils::fromEnv();
        $user = $this->authUtils()->getCurrentUser();
        $html_utils = HtmlUtils::fromEnv();
        $id = $args['id'] ?? null;

        $news_utils = NewsFilterUtils::fromEnv();
        $news_repo = $entityManager->getRepository(NewsEntry::class);
        $role_repo = $entityManager->getRepository(Role::class);
        $user_repo = $entityManager->getRepository(User::class);
        $is_not_archived = $news_utils->getIsNotArchivedCriteria();
        $criteria = Criteria::create()
            ->where(Criteria::expr()->andX(
                $is_not_archived,
                Criteria::expr()->eq('id', $id),
                Criteria::expr()->eq('on_off', 1),
            ))
            ->setFirstResult(0)
            ->setMaxResults(1)
        ;
        $news_entries = $news_repo->matching($criteria);
        $num_news_entries = $news_entries->count();
        $no_robots = $num_news_entries !== 1;
        $host = str_replace('www.', '', $_SERVER['HTTP_HOST']);
        $canonical_url = "https://{$host}{$code_href}news/{$id}";

        $article_metadata = "";
        try {
            $article_metadata = OlzArticleMetadata::render(['id' => $id]);
        } catch (\Exception $exc) {
            $this->httpUtils()->dieWithHttpError(404);
        }

        $sql = "SELECT * FROM news WHERE (id = '{$id}') AND (on_off = '1') ORDER BY published_date DESC";
        $result = $db->query($sql);
        $row = $result->fetch_assoc();

        if (!$row) {
            $this->httpUtils()->dieWithHttpError(404);
        }

        $title = $row['title'] ?? '';
        $back_filter = urlencode($_GET['filter'] ?? '{}');
        $out = OlzHeader::render([
            'back_link' => "{$code_href}news?filter={$back_filter}",
            'title' => "{$title} - News",
            'description' => "Aktuelle Beiträge, Berichte von Anlässen und weitere Neuigkeiten von der OL Zimmerberg.",
            'norobots' => $no_robots,
            'canonical_url' => $canonical_url,
            'additional_headers' => [
                $article_metadata,
            ],
        ]);

        $pretty_date = $this->dateUtils()->olzDate("tt.mm.jjjj", $row['published_date']);
        $author_user = $row['author_user_id'] ?
            $user_repo->findOneBy(['id' => $row['author_user_id']]) : null;
        $author_role = $row['author_role_id'] ?
            $role_repo->findOneBy(['id' => $row['author_role_id']]) : null;
        $author_name = $row['author_name'];
        $author_email = $row['author_email'];
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

        $db->query("UPDATE news SET `counter`=`counter` + 1 WHERE `id`='{$id}'");

        $format = $row['format'];
        $title = $row['title'];
        $teaser = $row['teaser'];
        $content = $row['content'];
        $published_date = $row['published_date'];

        $image_ids = json_decode($row['image_ids'] ?? 'null', true);

        $published_date = $this->dateUtils()->olzDate("tt.mm.jj", $published_date);

        $is_owner = $user && intval($row['owner_user_id'] ?? 0) === intval($user->getId());
        $has_all_permissions = $this->authUtils()->hasPermission('all');
        $can_edit = $is_owner || $has_all_permissions;
        $edit_admin = '';
        if ($can_edit) {
            $json_id = json_encode(intval($id));
            $has_blog = $this->authUtils()->hasPermission('kaderblog', $user);
            $json_mode = htmlentities(json_encode($has_blog ? 'account_with_blog' : 'account'));
            $edit_admin = <<<ZZZZZZZZZZ
            <div>
                <button
                    id='edit-news-button'
                    class='btn btn-primary'
                    onclick='return olz.editNews({$json_id}, {$json_mode})'
                >
                    <img src='{$code_href}assets/icns/edit_white_16.svg' class='noborder' />
                    Bearbeiten
                </button>
                <button
                    id='delete-news-button'
                    class='btn btn-danger'
                    onclick='return olz.deleteNewsArticle({$json_id})'
                >
                    <img src='{$code_href}assets/icns/delete_white_16.svg' class='noborder' />
                    Löschen
                </button>
            </div>
            ZZZZZZZZZZ;
        }

        // TODO: Temporary fix for broken Markdown
        $content = str_replace("\n", "\n\n", $content);
        $content = str_replace("\n\n\n\n", "\n\n", $content);

        // Bildercode einfügen
        preg_match_all('/<bild([0-9]+)(\\s+size=([0-9]+))?([^>]*)>/i', $teaser, $matches);
        for ($i = 0; $i < count($matches[0]); $i++) {
            $teaser = str_replace($matches[0][$i], '', $teaser);
        }
        $content = $image_utils->replaceImageTags(
            $content,
            $id,
            $image_ids,
            "gallery[myset]",
            " class='box' style='float:left;clear:left;margin:3px 5px 3px 0px;'"
        );

        // Dateicode einfügen
        $teaser = $file_utils->replaceFileTags($teaser, 'aktuell', $id);
        $content = $file_utils->replaceFileTags($content, 'aktuell', $id);

        // Markdown
        $teaser = $html_utils->renderMarkdown($teaser, [
            'html_input' => 'allow', // TODO: Do NOT allow!
        ]);
        $content = $html_utils->renderMarkdown($content, [
            'html_input' => 'allow', // TODO: Do NOT allow!
        ]);

        $out .= "<h1>{$edit_admin}{$title}</h1>";

        if ($format === 'aktuell') {
            $out .= "<div class='lightgallery'><p><b>{$teaser}</b><p>{$content}</p></div>\n";
        } elseif ($format === 'kaderblog') {
            $gallery = '';
            $num_images = count($image_ids);
            if ($num_images > 0) {
                $gallery .= "<br/><br/><div class='lightgallery gallery-container'>";
                foreach ($image_ids as $image_id) {
                    $gallery .= "<div class='gallery-image'>";
                    $gallery .= $image_utils->olzImage(
                        'news', $id, $image_id, 110, 'gallery[myset]');
                    $gallery .= "</div>";
                }
                $gallery .= "</div>";
            }
            $out .= "<p>{$content}</p>{$gallery}\n";
        } elseif ($format === 'forum') {
            $gallery = '';
            $num_images = count($image_ids);
            if ($num_images > 0) {
                $gallery .= "<br/><br/><div class='lightgallery gallery-container'>";
                foreach ($image_ids as $image_id) {
                    $gallery .= "<div class='gallery-image'>";
                    $gallery .= $image_utils->olzImage(
                        'news', $id, $image_id, 110, 'gallery[myset]');
                    $gallery .= "</div>";
                }
                $gallery .= "</div>";
            }
            $out .= "<p><b>{$teaser}</b><p>{$content}</p>{$gallery}\n";
        } elseif ($format === 'galerie') {
            $out .= "<div class='lightgallery gallery-container'>";
            $size = count($image_ids);
            for ($index = 0; $index < $size; $index++) {
                $out .= "<div class='gallery-image'>";
                $out .= $image_utils->olzImage("news", $id, $image_ids[$index], 110, 'gallery[myset]');
                $out .= "</div>";
            }
            $out .= "</div>\n";
        } elseif ($format === 'video') {
            $youtube_url = $row['content'];
            $res0 = preg_match("/^https\\:\\/\\/(www\\.)?youtu\\.be\\/([a-zA-Z0-9]{6,})/", $youtube_url, $matches0);
            $res1 = preg_match("/^https\\:\\/\\/(www\\.)?youtube\\.com\\/watch\\?v\\=([a-zA-Z0-9]{6,})/", $youtube_url, $matches1);
            $youtube_match = null;
            if ($res0) {
                $youtube_match = $matches0[2];
            }
            if ($res1) {
                $youtube_match = $matches1[2];
            }

            $content_to_show = $youtube_match ? "<a href='{$content}'>Link zu YouTube, falls das Video nicht abgespielt werden kann</a>" : $content;
            $out .= "<div class='video-container'>";
            $out .= "<div style='background-image:url({$code_href}assets/icns/movie_dot.gif);background-repeat:repeat-x;margin:0px;padding:0px;height:24px;'></div>\n";
            if ($youtube_match != null) {
                $out .= "<iframe width='560' height='315' src='https://www.youtube.com/embed/{$youtube_match}' frameborder='0' allow='accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture' allowfullscreen></iframe>";
            } else {
                $this->log()->error("Invalid YouTube link (ID:{$id}): {$youtube_url}");
                $out .= "Fehlerhafter YouTube-Link!";
            }
            $out .= "<div style='background-image:url({$code_href}assets/icns/movie_dot.gif);background-repeat:repeat-x;margin:0px;padding:0px;height:24px;'></div>";
            $out .= "</div>";
        } else {
            $out .= "<div class='lightgallery'><p><b>{$teaser}</b><p>{$content}</p></div>\n";
        }
        $out .= "</div>";

        $out .= OlzFooter::render();

        return $out;
    }
}
