<?php

namespace Olz\Utils;

use Olz\Components\Error\OlzErrorPage\OlzErrorPage;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeaderWithoutRouting\OlzHeaderWithoutRouting;
use Olz\Entity\Counter;
use PhpTypeScriptApi\PhpStan\PhpStanUtils;
use PhpTypeScriptApi\PhpStan\ValidateVisitor;
use Symfony\Component\HttpFoundation\Request;

class HttpUtils {
    use WithUtilsTrait;

    /** @return array<string> */
    public function getBotRegexes(): array {
        return [
            '/bingbot/i',
            '/googlebot/i',
            '/google/i',
            '/facebookexternalhit/i',
            '/applebot/i',
            '/yandexbot/i',
            '/ecosia/i',
            '/phpservermon/i',
            '/OlzSystemTest\//i',
            '/bot\//i',
            '/crawler\//i',
        ];
    }

    public function isBot(string $user_agent): bool {
        foreach ($this->getBotRegexes() as $regex) {
            if (preg_match($regex, $user_agent)) {
                return true;
            }
        }
        return false;
    }

    /** @param array<string> $get_params */
    public function countRequest(Request $request, array $get_params = []): void {
        $user_agent = $this->server()['HTTP_USER_AGENT'] ?? '';
        if ($this->isBot($user_agent)) {
            $this->log()->debug("Counter: user agent is bot: {$user_agent}");
            return;
        }
        $path = "{$request->getBasePath()}{$request->getPathInfo()}";
        $query = [];
        foreach ($get_params as $key) {
            $value = $request->query->get($key);
            if ($value !== null) {
                $query[] = "{$key}={$value}";
            }
        }
        $pretty_query = empty($query) ? '' : '?'.implode('&', $query);
        $counter_repo = $this->entityManager()->getRepository(Counter::class);
        $counter_repo->record("{$path}{$pretty_query}");
        $this->log()->debug("Counter: Counted {$path}{$pretty_query} (user agent: {$user_agent})");
    }

    public function dieWithHttpError(int $http_status_code): void {
        $this->sendHttpResponseCode($http_status_code);

        $out = OlzErrorPage::render([
            'http_status_code' => $http_status_code,
        ]);

        $this->sendHttpBody($out);
        $this->exitExecution();
    }

    public function redirect(string $redirect_url, int $http_status_code = 301): void {
        $this->sendHeader("Location: {$redirect_url}");
        $this->sendHttpResponseCode($http_status_code);

        $out = "";
        $out .= OlzHeaderWithoutRouting::render([
            'title' => "Weiterleitung...",
        ]);

        $enc_redirect_url = json_encode($redirect_url);
        $out .= <<<ZZZZZZZZZZ
            <div class='content-full'>
                <h2>Automatische Weiterleitung...</h2>
                <p>Falls die automatische Weiterleitung nicht funktionieren sollte, kannst du auch diesen Link anklicken:</p>
                <p><b><a href='{$redirect_url}' class='linkint' id='redirect-link'>{$redirect_url}</a></b></p>
                <script type='text/javascript'>
                    window.setTimeout(function () {
                        window.location.href = {$enc_redirect_url};
                    }, 1000);
                </script>
            </div>
            ZZZZZZZZZZ;

        $out .= OlzFooter::render();
        $this->sendHttpBody($out);
        $this->exitExecution();
    }

    /**
     * @template T of array
     *
     * @param class-string<HttpParams<T>>             $params_class
     * @param ?array<string, ?(string|array<string>)> $get_params
     * @param array{just_log?: bool}                  $options
     *
     * @return T
     */
    public function validateGetParams(string $params_class, ?array $get_params = null, array $options = []): array {
        if ($get_params === null) {
            $get_params = $this->getParams();
        }
        $class_info = new \ReflectionClass($params_class);
        $utils = new PhpStanUtils();
        $php_doc_node = $utils->parseDocComment(
            $class_info->getDocComment(),
            $class_info->getFileName() ?: null,
        );
        $type = $php_doc_node?->getExtendsTagValues()[0]->type->genericTypes[0];
        $aliases = $utils->getAliases($php_doc_node);
        if (!$type) {
            $this->dieWithHttpError(400);
            throw new \Exception('should already have failed');
        }
        $result = ValidateVisitor::validateDeserialize($utils, $get_params, $type, $aliases);
        if (!$result->isValid() && ($options['just_log'] ?? false) === false) {
            $this->dieWithHttpError(400);
            throw new \Exception('should already have failed');
        }
        return $result->getValue();
    }

    // @codeCoverageIgnoreStart
    // Reason: Mock functions for tests.

    protected function sendHttpResponseCode(int $http_response_code): void {
        http_response_code($http_response_code);
    }

    protected function sendHeader(string $http_header_line): void {
        header($http_header_line);
    }

    protected function sendHttpBody(string $http_body): void {
        echo $http_body;
    }

    protected function exitExecution(): void {
        exit('');
    }

    // @codeCoverageIgnoreEnd

    public static function fromEnv(): self {
        return new self();
    }
}
