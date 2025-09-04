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
    public function getSearchTitle(): string {
        return 'TODO';
    }

    public function getSearchResults(array $terms): array {
        return [];
    }

    public function getHtml(mixed $args): string {
        $this->httpUtils()->validateGetParams(OlzQuizParams::class);
        $code_href = $this->envUtils()->getCodeHref();

        $out = OlzHeader::render([
            'back_link' => "{$code_href}service/",
            'title' => "Quiz",
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
