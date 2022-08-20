<?php

namespace Olz\Apps\Quiz\Components\OlzQuiz;

use Olz\Apps\Quiz\Metadata;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Utils\EnvUtils;
use Olz\Utils\HttpUtils;

class OlzQuiz {
    public static function render($args = []) {
        require_once __DIR__.'/../../../../../_/config/init.php';

        session_start_if_cookie_set();

        $env_utils = EnvUtils::fromEnv();
        $logger = $env_utils->getLogsUtils()->getLogger('Quiz');
        $http_utils = HttpUtils::fromEnv();
        $http_utils->setLogger($logger);
        $http_utils->validateGetParams([], $_GET);

        $out = '';

        $out .= OlzHeader::render([
            'title' => "Quiz",
            'norobots' => true,
        ]);

        $out .= <<<'ZZZZZZZZZZ'
        <div id='content_double'>
            <iframe class='quiz-iframe' src='https://quiz.bitter.li/'></iframe>
        </div>
        ZZZZZZZZZZ;

        $metadata = new Metadata();
        $out .= $metadata->getJsCssImports();

        $out .= OlzFooter::render();

        return $out;
    }
}
