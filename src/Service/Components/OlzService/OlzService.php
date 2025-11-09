<?php

namespace Olz\Service\Components\OlzService;

use Olz\Components\Apps\OlzAppsList\OlzAppsList;
use Olz\Components\Common\OlzRootComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Entity\Service\Download;
use Olz\Entity\Service\Link;
use Olz\Service\Components\OlzDownloads\OlzDownloads;
use Olz\Service\Components\OlzLinks\OlzLinks;
use Olz\Utils\HttpParams;

/** @extends HttpParams<array{}> */
class OlzServiceParams extends HttpParams {
}

/** @extends OlzRootComponent<array<string, mixed>> */
class OlzService extends OlzRootComponent {
    public function hasAccess(): bool {
        return true;
    }

    public function getSearchTitle(): string {
        return 'Service';
    }

    public function getSearchResults(array $terms): array {
        $results = [];
        $code_href = $this->envUtils()->getCodeHref();
        $download_repo = $this->entityManager()->getRepository(Download::class);
        $downloads = $download_repo->search($terms);
        foreach ($downloads as $download) {
            $results[] = $this->searchUtils()->getScoredSearchResult([
                'link' => "{$code_href}service",
                'icon' => "{$code_href}assets/icns/link_internal_16.svg", // TODO better icon
                'date' => null,
                'title' => $download->getName() ?: '?',
                'text' => null,
            ], $terms);
        }
        $link_repo = $this->entityManager()->getRepository(Link::class);
        $links = $link_repo->search($terms);
        foreach ($links as $link) {
            $results[] = $this->searchUtils()->getScoredSearchResult([
                'link' => "{$code_href}service",
                'icon' => "{$code_href}assets/icns/link_internal_16.svg", // TODO better icon
                'date' => null,
                'title' => $link->getName() ?: '?',
                'text' => $link->getUrl() ?: null,
            ], $terms);
        }
        return $results;
    }

    public static string $title = "Service";
    public static string $description = "Diverse Online-Tools rund um OL und die OL Zimmerberg.";

    public function getHtmlWhenHasAccess(mixed $args): string {
        $this->httpUtils()->validateGetParams(OlzServiceParams::class);

        $out = OlzHeader::render([
            'title' => self::$title,
            'description' => self::$description,
        ]);

        $out .= "<div class='content-full'>";

        $out .= "<h1>Service</h1>";
        $out .= "<h2>Apps</h2>";
        $out .= OlzAppsList::render();
        $out .= "<br /><br />";

        $out .= "<div class='responsive-flex'>";
        $out .= "<div class='responsive-flex-2'>";
        $out .= OlzLinks::render();
        $out .= "</div>";
        $out .= "<div class='responsive-flex-2'>";
        $out .= OlzDownloads::render();
        $out .= "</div></div><br><br>";

        $out .= "</div>";

        $out .= OlzFooter::render();

        return $out;
    }
}
