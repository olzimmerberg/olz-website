<?php

namespace Olz\Common\Components\OlzErrorPage;

use Olz\Common\Components\Olz400BadRequest\Olz400BadRequest;
use Olz\Common\Components\Olz401Unauthorized\Olz401Unauthorized;
use Olz\Common\Components\Olz403Forbidden\Olz403Forbidden;
use Olz\Common\Components\Olz404NotFound\Olz404NotFound;
use Olz\Common\Components\Olz500ServerInternalError\Olz500ServerInternalError;
use Olz\Common\Components\OlzComponent;
use Olz\Common\Components\OlzOtherError\OlzOtherError;

/** @extends OlzComponent<array<string, mixed>> */
class OlzErrorPage extends OlzComponent {
    public function getHtml(mixed $args): string {
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
