<?php

namespace Olz\Apps\Quiz\Components\OlzQuiz;

use Olz\Apps\Quiz\Metadata;
use Olz\Components\Common\OlzComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Utils\HttpUtils;

class OlzQuiz extends OlzComponent {
    public function getHtml($args = []): string {
        $code_href = $this->envUtils()->getCodeHref();
        $http_utils = HttpUtils::fromEnv();
        $http_utils->setLog($this->log());
        $http_utils->validateGetParams([], $_GET);

        $out = '';

        $out .= OlzHeader::render([
            'back_link' => "{$code_href}apps/",
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
