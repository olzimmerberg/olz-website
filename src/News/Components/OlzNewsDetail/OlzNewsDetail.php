<?php

// =============================================================================
// Alle Neuigkeiten rund um die OL Zimmerberg
// =============================================================================

namespace Olz\News\Components\OlzNewsDetail;

use Doctrine\Common\Collections\Criteria;
use Olz\Components\Common\OlzRootComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Entity\News\NewsEntry;
use Olz\News\Components\OlzArticleMetadata\OlzArticleMetadata;
use Olz\News\Components\OlzAuthorBadge\OlzAuthorBadge;
use Olz\News\Utils\NewsUtils;
use Olz\Utils\HttpParams;

/** @extends HttpParams<array{von?: ?string}> */
class OlzNewsDetailParams extends HttpParams {
}

/** @extends OlzRootComponent<array<string, mixed>> */
class OlzNewsDetail extends OlzRootComponent {
    public function hasAccess(): bool {
        return true;
    }

    public function getSearchTitle(): string {
        return 'News';
    }

    public function getSearchResults(array $terms): array {
        $results = [];
        $code_href = $this->envUtils()->getCodeHref();
        $news_repo = $this->entityManager()->getRepository(NewsEntry::class);
        $news = $news_repo->search($terms);
        foreach ($news as $news_entry) {
            $id = $news_entry->getId();
            $results[] = $this->searchUtils()->getScoredSearchResult([
                'link' => "{$code_href}news/{$id}",
                'icon' => $this->newsUtils()->getNewsFormatIcon($news_entry) ?: null,
                'date' => $news_entry->getPublishedDate(),
                'title' => $news_entry->getTitle() ?: '?',
                'text' => strip_tags("{$news_entry->getTeaser()} {$news_entry->getContent()}") ?: null,
            ], $terms);
        }
        return $results;
    }

    public function getHtmlWhenHasAccess(mixed $args): string {
        $this->httpUtils()->validateGetParams(OlzNewsDetailParams::class);
        $code_href = $this->envUtils()->getCodeHref();
        $db = $this->dbUtils()->getDb();
        $entityManager = $this->dbUtils()->getEntityManager();
        $user = $this->authUtils()->getCurrentUser();
        $id = $args['id'] ?? null;

        $news_repo = $entityManager->getRepository(NewsEntry::class);
        $is_not_archived = $this->newsUtils()->getIsNotArchivedCriteria();
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

        if ($is_archived && !$this->authUtils()->hasPermission('any')) {
            $this->httpUtils()->dieWithHttpError(404);
            throw new \Exception('should already have failed');
        }

        $article_metadata = "";
        try {
            $article_metadata = OlzArticleMetadata::render(['id' => $id]);
        } catch (\Exception $exc) {
            $this->httpUtils()->dieWithHttpError(404);
            throw new \Exception('should already have failed');
        }

        $news_entry = $this->getNewsEntryById($id);

        if (!$news_entry) {
            $this->httpUtils()->dieWithHttpError(404);
            throw new \Exception('should already have failed');
        }

        $title = $news_entry->getTitle();
        $out = OlzHeader::render([
            'back_link' => "{$code_href}news",
            'title' => "{$title} - News",
            'description' => "Aktuelle Beiträge, Berichte von Anlässen und weitere Neuigkeiten von der OL Zimmerberg.",
            'norobots' => $is_archived,
            'canonical_url' => "{$code_href}news/{$id}",
            'additional_headers' => [
                $article_metadata,
            ],
        ]);

        $format = $news_entry->getFormat();
        // TODO: Use array_find with PHP 8.4
        $filtered = array_filter(
            NewsUtils::ALL_FORMAT_OPTIONS,
            fn ($entry) => $entry['ident'] === $format
        );
        // @phpstan-ignore-next-line
        $found_entry = $filtered[array_keys($filtered)[0]];
        $name = $found_entry['name'];
        $icon = $found_entry['icon'] ?? null;
        $icon_html = "<img src='{$code_href}assets/icns/{$icon}' alt='' class='format-icon'>";
        $pretty_format = "{$icon_html}{$name}";

        $pretty_date = $this->dateUtils()->olzDate("tt.mm.jjjj", $news_entry->getPublishedDate());
        $author_user = $news_entry->getAuthorUser();
        $author_role = $news_entry->getAuthorRole();
        $author_name = $news_entry->getAuthorName();
        $author_email = $news_entry->getAuthorEmail();
        $pretty_author = OlzAuthorBadge::render([
            'news_id' => $news_entry->getId() ?: 0,
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
                    <div id='format-info'><b>Format: </b>{$pretty_format}</div>
                    <div><b>Datum: </b>{$pretty_date}</div>
                    <div><b>Autor: </b>{$pretty_author}</div>
                    <div><b>Anzahl Bilder: </b>{$num_images}</div>
                    <div class='pretty'>{$download_all_link}</div>
                </div>
            </div>
            <div class='content-middle'>
            ZZZZZZZZZZ;

        $db->query("UPDATE news SET `counter`=`counter` + 1 WHERE `id`='{$id}'");

        $title = $news_entry->getTitle();
        $teaser = $news_entry->getTeaser() ?? '';
        $content = $news_entry->getContent() ?? '';
        $published_date = $news_entry->getPublishedDate();

        $published_date = $this->dateUtils()->olzDate("tt.mm.jj", $published_date);

        $is_owner = $user && intval($news_entry->getOwnerUser()?->getId() ?? 0) === intval($user->getId());
        $has_all_permissions = $this->authUtils()->hasPermission('all');
        $can_edit = $is_owner || $has_all_permissions;
        $edit_admin = '';
        if ($can_edit) {
            $json_id = json_encode($id);
            $has_blog = $this->authUtils()->hasPermission('kaderblog', $user);
            $has_roles = !empty($this->authUtils()->getAuthenticatedRoles());
            $json_mode = htmlentities(json_encode($has_roles ? ($has_blog ? 'account_with_all' : 'account_with_aktuell') : ($has_blog ? 'account_with_blog' : 'account')) ?: '');
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
                </div>
                ZZZZZZZZZZ;
        }

        // TODO: Temporary fix for broken Markdown
        $content = str_replace("\n", "\n\n", $content);
        $content = str_replace("\n\n\n\n", "\n\n", $content);

        // Markdown
        $html_input = $format === 'forum' ? 'escape' : 'allow'; // TODO: Do NOT allow!
        $teaser = $this->htmlUtils()->renderMarkdown($teaser, [
            'html_input' => $html_input,
        ]);
        $content = $this->htmlUtils()->renderMarkdown($content, [
            'html_input' => $html_input,
        ]);

        // Datei- & Bildpfade
        $teaser = $news_entry->replaceImagePaths($teaser);
        $teaser = $news_entry->replaceFilePaths($teaser);
        $content = $news_entry->replaceImagePaths($content);
        $content = $news_entry->replaceFilePaths($content);

        $out .= "<h1>{$edit_admin}{$title}</h1>";

        $gallery = '';
        $num_images = count($image_ids);
        if ($num_images > 0) {
            $gallery .= "<div class='lightgallery gallery-container'>";
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

        if ($format === 'aktuell') {
            $out .= "<p><b>{$teaser}</b><p>{$content}</p><br/><br/>{$gallery}\n";
        } elseif ($format === 'kaderblog') {
            $out .= "<p>{$content}</p><br/><br/>{$gallery}\n";
        } elseif ($format === 'forum') {
            $out .= "<p><b>{$teaser}</b><p>{$content}</p><br/><br/>{$gallery}\n";
        } elseif ($format === 'galerie') {
            $out .= "<p>{$content}</p>{$gallery}\n";
        } elseif ($format === 'video') {
            $youtube_url = $news_entry->getExternalUrl() ?? '';
            $res0 = preg_match("/^https\\:\\/\\/(www\\.)?youtu\\.be\\/([a-zA-Z0-9\\-\\_]{6,})/", $youtube_url, $matches0);
            $res1 = preg_match("/^https\\:\\/\\/(www\\.)?youtube\\.com\\/watch\\?v\\=([a-zA-Z0-9\\-\\_]{6,})/", $youtube_url, $matches1);
            $youtube_match = null;
            if ($res0) {
                $youtube_match = $matches0[2];
            }
            if ($res1) {
                $youtube_match = $matches1[2];
            }

            $out .= "<div class='video-container'>";
            $out .= "<div style='background-image:url({$code_href}assets/icns/movie_dot.svg);background-repeat:repeat-x;margin:0px;padding:0px;height:24px;'></div>\n";
            if ($youtube_match != null) {
                $out .= "<iframe width='560' height='315' src='https://www.youtube.com/embed/{$youtube_match}' frameborder='0' allow='accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture' allowfullscreen></iframe>";
            } else {
                $this->log()->error("Invalid YouTube link (ID:{$id}): {$youtube_url}");
                $out .= "Fehlerhafter YouTube-Link!";
            }
            $out .= "<div style='background-image:url({$code_href}assets/icns/movie_dot.svg);background-repeat:repeat-x;margin:0px;padding:0px;height:24px;'></div>";
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
