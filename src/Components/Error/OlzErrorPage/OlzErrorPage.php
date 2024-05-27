<?php

namespace Olz\Components\Error\OlzErrorPage;

use Olz\Components\Common\OlzComponent;
use Olz\Components\Error\Olz400BadRequest\Olz400BadRequest;
use Olz\Components\Error\Olz401Unauthorized\Olz401Unauthorized;
use Olz\Components\Error\Olz403Forbidden\Olz403Forbidden;
use Olz\Components\Error\Olz404NotFound\Olz404NotFound;
use Olz\Components\Error\Olz500ServerInternalError\Olz500ServerInternalError;
use Olz\Components\Error\OlzOtherError\OlzOtherError;

class OlzErrorPage extends OlzComponent {
    /** @param array<string, mixed> $args */
    public function getHtml(array $args = []): string {
        $http_status_code = $args['http_status_code'] ?? 500;
        if ($http_status_code === 400) {
            return Olz400BadRequest::render([], $this);
        }
        if ($http_status_code === 401) {
            return Olz401Unauthorized::render([], $this);
        }
        if ($http_status_code === 403) {
            return Olz403Forbidden::render([], $this);
        }
        if ($http_status_code === 404) {
            return Olz404NotFound::render([], $this);
        }
        if ($http_status_code === 500) {
            return Olz500ServerInternalError::render([], $this);
        }
        return OlzOtherError::render([
            'http_status_code' => $http_status_code,
        ], $this);
    }
}
