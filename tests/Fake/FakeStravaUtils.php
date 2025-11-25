<?php

declare(strict_types=1);

namespace Olz\Tests\Fake;

use Olz\Entity\StravaLink;
use Olz\Utils\StravaUtils;

class FakeStravaUtils extends StravaUtils {
    /** @var array<array{0: string}> */
    public array $linkStravaCalls = [];

    public ?StravaLink $linkStravaLinkToReturn = null;

    public function linkStrava(string $code): ?StravaLink {
        $this->linkStravaCalls[] = [$code];
        return $this->linkStravaLinkToReturn;
    }

    /**
     * @param array<string, mixed> $token_request_data
     *
     * @return ?array<string, mixed>
     */
    public function fetchTokenDataForCode(array $token_request_data): ?array {
        return [
            'access_token' => 'fake-new-access-token',
            'refresh_token' => 'fake-new-refresh-token',
            'expires_at' => 1764003600,
        ];
    }

    /**
     * @param 'GET'|'POST'          $method
     * @param array<string, string> $query
     */
    public function callStravaApi(
        string $method,
        string $path,
        array $query,
        string $access_token,
    ): mixed {
        return [];
    }
}
