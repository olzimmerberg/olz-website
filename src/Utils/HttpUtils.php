<?php

namespace Olz\Utils;

use Olz\Components\Error\OlzErrorPage\OlzErrorPage;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeaderWithoutRouting\OlzHeaderWithoutRouting;
use Olz\Entity\Counter;
use PhpTypeScriptApi\Fields\ValidationError;
use Symfony\Component\HttpFoundation\Request;

class HttpUtils {
    use WithUtilsTrait;

    public function countRequest(Request $request, $get_params = []) {
        $user_agent = $this->server()['HTTP_USER_AGENT'] ?? '';
        if (
            preg_match('/bingbot/i', $user_agent)
            || preg_match('/googlebot/i', $user_agent)
            || preg_match('/facebookexternalhit/i', $user_agent)
            || preg_match('/applebot/i', $user_agent)
            || preg_match('/yandexbot/i', $user_agent)
            || preg_match('/bot\//i', $user_agent)
        ) {
            return;
        }
        $path = "{$request->getBasePath()}{$request->getPathInfo()}";
        $query = array_map(function ($key) use ($request) {
            $value = $request->query->get($key);
            return "{$key}={$value}";
        }, $get_params);
        $pretty_query = empty($query) ? '' : '?'.implode('&', $query);
        $counter_repo = $this->entityManager()->getRepository(Counter::class);
        $counter_repo->record("{$path}{$pretty_query}");
    }

    public function dieWithHttpError(int $http_status_code) {
        $this->sendHttpResponseCode($http_status_code);

        $out = OlzErrorPage::render([
            'http_status_code' => $http_status_code,
        ]);

        $this->sendHttpBody($out);
        $this->exitExecution();
    }

    public function redirect($redirect_url, $http_status_code = 301) {
        $this->sendHttpResponseCode($http_status_code);
        $this->sendHeader("Location: {$redirect_url}");

        $out = "";
        $out .= OlzHeaderWithoutRouting::render([
            'title' => "Weiterleitung...",
        ]);

        $out .= <<<ZZZZZZZZZZ
            <div class='content-full'>
                <h2>Automatische Weiterleitung...</h2>
                <p>Falls die automatische Weiterleitung nicht funktionieren sollte, kannst du auch diesenLink anklicken:</p>
                <p><b><a href='{$redirect_url}' class='linkint'>{$redirect_url}</a></b></p>
            </div>
            ZZZZZZZZZZ;

        $out .= OlzFooter::render();
        $this->sendHttpBody($out);
        $this->exitExecution();
    }

    public function validateGetParams($fields, $get_params = null, $options = []) {
        if ($get_params === null) {
            $get_params = $this->getParams();
        }
        $validated_get_params = [];
        $has_error = false;
        foreach ($get_params as $key => $value) {
            $field = $fields[$key] ?? null;
            if (!$field) {
                $this->log()->notice("Unknown GET param '{$key}'", $get_params);
                $has_error = true;
            } else {
                try {
                    $validated_get_params[$key] = $this->fieldUtils()->validate(
                        $field,
                        $get_params[$key] ?? null,
                        ['parse' => true]
                    );
                } catch (ValidationError $verr) {
                    $this->log()->notice("Bad GET param '{$key}'", $verr->getStructuredAnswer());
                    $has_error = true;
                }
            }
        }
        if ($has_error && ($options['just_log'] ?? false) === false) {
            $this->dieWithHttpError(400);
        }
        return $validated_get_params;
    }

    // @codeCoverageIgnoreStart
    // Reason: Mock functions for tests.

    protected function sendHttpResponseCode($http_response_code) {
        http_response_code($http_response_code);
    }

    protected function sendHeader($http_header_line) {
        header($http_header_line);
    }

    protected function sendHttpBody($http_body) {
        echo $http_body;
    }

    protected function exitExecution() {
        exit('');
    }

    // @codeCoverageIgnoreEnd

    public static function fromEnv(): self {
        return new self();
    }
}
