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
use Olz\News\Components\OlzArticleMetadata\OlzArticleMetadata;
use Olz\News\Utils\NewsFilterUtils;
use PhpTypeScriptApi\Fields\FieldTypes;

class OlzNewsDetail extends OlzComponent {
    /** @param array<string, mixed> $args */
    public function getHtml(array $args = []): string {
        $this->httpUtils()->validateGetParams([
            'filter' => new FieldTypes\StringField(['allow_null' => true]),
        ]);
        $code_href = $this->envUtils()->getCodeHref();
        $db = $this->dbUtils()->getDb();
        $entityManager = $this->dbUtils()->getEntityManager();
        $user = $this->authUtils()->getCurrentUser();
        $id = $args['id'] ?? null;

        $news_utils = NewsFilterUtils::fromEnv();
        $news_repo = $entityManager->getRepository(NewsEntry::class);
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
        $is_archived = $num_news_entries !== 1;
        $host = str_replace('www.', '', $_SERVER['HTTP_HOST']);
        $canonical_url = "https://{$host}{$code_href}news/{$id}";

        if ($is_archived && !$this->authUtils()->hasPermission('any')) {
            $this->httpUtils()->dieWithHttpError(401);
        }

        $article_metadata = "";
        try {
            $article_metadata = OlzArticleMetadata::render(['id' => $id]);
        } catch (\Exception $exc) {
            $this->httpUtils()->dieWithHttpError(404);
        }

        $news_entry = $this->getNewsEntryById($id);

        if (!$news_entry) {
            $this->httpUtils()->dieWithHttpError(404);
        }

        $title = $news_entry->getTitle();
        $back_filter = urlencode($_GET['filter'] ?? '{}');
        $out = OlzHeader::render([
            'back_link' => "{$code_href}news?filter={$back_filter}",
            'title' => "{$title} - News",
            'description' => "Aktuelle Beiträge, Berichte von Anlässen und weitere Neuigkeiten von der OL Zimmerberg.",
            'norobots' => $is_archived,
            'canonical_url' => $canonical_url,
            'additional_headers' => [
                $article_metadata,
            ],
        ]);

        $pretty_date = $this->dateUtils()->olzDate("tt.mm.jjjj", $news_entry->getPublishedDate());
        $author_user = $news_entry->getAuthorUser();
        $author_role = $news_entry->getAuthorRole();
        $author_name = $news_entry->getAuthorName();
        $author_email = $news_entry->getAuthorEmail();
        $pretty_author = OlzAuthorBadge::render([
            'user' => $author_user,
            'role' => $author_role,
            'name' => $author_name,
            'email' => $author_email,
        ]);
        $image_ids = $news_entry->getImageIds();
        $num_images = count($image_ids);
        $download_all_link = $this->authUtils()->hasPermission('any')
            ? "<a href='{$code_href}news/{$id}/all.zip'>Alle herunterladen</a>" : '';

        $out .= <<<ZZZZZZZZZZ
            <div class='content-right'>
                <div style='padding:4px 3px 10px 3px;'>
                    <div><b>Datum: </b>{$pretty_date}</div>
                    <div><b>Autor: </b>{$pretty_author}</div>
                    <div><b>Anzahl Bilder: </b>{$num_images}</div>
                    <div class='pretty'>{$download_all_link}</div>
                </div>
            </div>
            <div class='content-middle'>
            ZZZZZZZZZZ;

        $db->query("UPDATE news SET `counter`=`counter` + 1 WHERE `id`='{$id}'");

        $format = $news_entry->getFormat();
        $title = $news_entry->getTitle();
        $teaser = $news_entry->getTeaser();
        $content = $news_entry->getContent();
        $published_date = $news_entry->getPublishedDate();

        $published_date = $this->dateUtils()->olzDate("tt.mm.jj", $published_date);

        $is_owner = $user && intval($news_entry->getOwnerUser()?->getId() ?? 0) === intval($user->getId());
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

        // Markdown
        $teaser = $this->htmlUtils()->renderMarkdown($teaser, [
            'html_input' => 'allow', // TODO: Do NOT allow!
        ]);
        $content = $this->htmlUtils()->renderMarkdown($content, [
            'html_input' => 'allow', // TODO: Do NOT allow!
        ]);

        // Datei- & Bildpfade
        $teaser = $news_entry->replaceImagePaths($teaser);
        $teaser = $news_entry->replaceFilePaths($teaser);
        $content = $news_entry->replaceImagePaths($content);
        $content = $news_entry->replaceFilePaths($content);

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
                    $gallery .= $this->imageUtils()->olzImage(
                        'news',
                        $id,
                        $image_id,
                        110,
                        'gallery[myset]'
                    );
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
                    $gallery .= $this->imageUtils()->olzImage(
                        'news',
                        $id,
                        $image_id,
                        110,
                        'gallery[myset]'
                    );
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
                $out .= $this->imageUtils()->olzImage("news", $id, $image_ids[$index], 110, 'gallery[myset]');
                $out .= "</div>";
            }
            $out .= "</div>\n";
        } elseif ($format === 'video') {
            $youtube_url = $news_entry->getContent();
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

    protected function getNewsEntryById(int $id): ?NewsEntry {
        $news_repo = $this->entityManager()->getRepository(NewsEntry::class);
        return $news_repo->findOneBy([
            'id' => $id,
            'on_off' => 1,
        ]);
    }
}
