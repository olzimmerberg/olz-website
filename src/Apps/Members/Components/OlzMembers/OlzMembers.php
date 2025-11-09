<?php

namespace Olz\Apps\Members\Components\OlzMembers;

use Olz\Apps\Members\Metadata;
use Olz\Components\Common\OlzRootComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Utils\HttpParams;

/** @extends HttpParams<array{}> */
class OlzMembersParams extends HttpParams {
}

/** @extends OlzRootComponent<array<string, mixed>> */
class OlzMembers extends OlzRootComponent {
    public function hasAccess(): bool {
        return true;
    }

    public function getSearchTitle(): string {
        return 'TODO';
    }

    public function getSearchResultsWhenHasAccess(array $terms): array {
        return [];
    }

    public function getHtmlWhenHasAccess(mixed $args): string {
        $this->httpUtils()->validateGetParams(OlzMembersParams::class);
        $code_href = $this->envUtils()->getCodeHref();

        $out = OlzHeader::render([
            'back_link' => "{$code_href}service/",
            'title' => "Members",
            'norobots' => true,
        ]);

        $out .= "<div class='content-full'><div id='react-root'>Lädt...</div></div>";

        $metadata = new Metadata();
        $out .= $metadata->getJsCssImports();

        $out .= OlzFooter::render();

        return $out;
    }
}
