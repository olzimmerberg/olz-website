<?php

namespace Olz\Apps\Quiz\Components\OlzQuiz;

use Olz\Apps\Quiz\Metadata;
use Olz\Components\Common\OlzRootComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Utils\HttpParams;

/** @extends HttpParams<array{}> */
class OlzQuizParams extends HttpParams {
}

/** @extends OlzRootComponent<array<string, mixed>> */
class OlzQuiz extends OlzRootComponent {
    public function hasAccess(): bool {
        return (new Metadata())->isAccessibleToUser($this->authUtils()->getCurrentUser());
    }

    public function searchSqlWhenHasAccess(array $terms): string|array|null {
        $metadata = new Metadata();
        return $this->searchUtils()->getStaticResultQuery([
            'link' => $metadata->getHref(),
            'icon' => $metadata->getIconHref(),
            'title' => "Apps: {$this->getPageTitle()}",
            'text' => $this->getPageDescription(),
        ], $terms);
    }

    public function getPageTitle(): string {
        return "Quiz";
    }

    public function getPageDescription(): string {
        return "Teste dein OL-Wissen.";
    }

    public function getHtmlWhenHasAccess(mixed $args): string {
        $this->httpUtils()->validateGetParams(OlzQuizParams::class);
        $code_href = $this->envUtils()->getCodeHref();

        $out = OlzHeader::render([
            'back_link' => "{$code_href}service/",
            'title' => $this->getPageTitle(),
            'description' => $this->getPageDescription(),
            'norobots' => true,
        ]);

        $out .= <<<'ZZZZZZZZZZ'
            <div class='content-full'>
                <iframe class='quiz-iframe' src='https://quiz.bitter.li/'></iframe>
            </div>
            ZZZZZZZZZZ;

        $metadata = new Metadata();
        $out .= $metadata->getJsCssImports();

        $out .= OlzFooter::render();

        return $out;
    }
}
