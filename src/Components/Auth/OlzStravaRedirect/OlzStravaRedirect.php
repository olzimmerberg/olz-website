<?php

namespace Olz\Components\Auth\OlzStravaRedirect;

use Olz\Components\Common\OlzRootComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Utils\HttpParams;

/** @extends HttpParams<array{
 *   redirect_url?: ?string,
 *   state?: ?string,
 *   code?: ?string,
 *   scope?: ?string,
 * }> */
class OlzStravaRedirectParams extends HttpParams {
}

/** @extends OlzRootComponent<array<string, mixed>> */
class OlzStravaRedirect extends OlzRootComponent {
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
        $params = $this->httpUtils()->validateGetParams(OlzStravaRedirectParams::class);
        $code = $params['code'] ?? '';
        $js_code = json_encode($code) ?: '""';
        $js_redirect_url = json_encode($params['redirect_url'] ?? null) ?: 'null';

        $out = OlzHeader::render([
            'title' => "Mit Strava verbinden",
            'description' => "Mit Strava verbinden.",
            'norobots' => true,
        ]);

        $out .= "<div class='content-full'>";
        $out .= <<<ZZZZZZZZZZ
            <script>
            const code = {$js_code};
            const redirectUrl = {$js_redirect_url};
            window.addEventListener('load', function () {
                olz.olzLinkStravaWithCode(code).then(function () {
                    window.location.href = redirectUrl;
                }).catch(function () {
                    window.location.href = redirectUrl;
                });
            });
            </script>
            ZZZZZZZZZZ;
        $out .= "</div>";

        $out .= OlzFooter::render();
        return $out;
    }
}
