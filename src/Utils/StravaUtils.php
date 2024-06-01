<?php

namespace Olz\Utils;

use Olz\Fetchers\StravaFetcher;

class StravaUtils {
    use WithUtilsTrait;

    protected ?string $client_id = null;
    protected ?string $client_secret = null;
    protected ?string $redirect_url = null;
    protected ?StravaFetcher $strava_fetcher = null;

    public static function fromEnv(): self {
        $env_utils = EnvUtils::fromEnv();
        $base_href = $env_utils->getBaseHref();
        $code_href = $env_utils->getCodeHref();
        $redirect_url = "{$base_href}{$code_href}konto_strava";
        $strava_fetcher = new StravaFetcher();

        $instance = new self();
        $instance->setClientId($env_utils->getStravaClientId());
        $instance->setClientSecret($env_utils->getStravaClientSecret());
        $instance->setRedirectUrl($redirect_url);
        $instance->setStravaFetcher($strava_fetcher);
        return $instance;
    }

    public function setClientId(?string $client_id): void {
        $this->client_id = $client_id;
    }

    public function setClientSecret(?string $client_secret): void {
        $this->client_secret = $client_secret;
    }

    public function setRedirectUrl(?string $redirect_url): void {
        $this->redirect_url = $redirect_url;
    }

    public function setStravaFetcher(?StravaFetcher $strava_fetcher): void {
        $this->strava_fetcher = $strava_fetcher;
    }

    public function getAuthUrl(): string {
        $strava_auth_url = 'https://www.strava.com/oauth/authorize';
        $data = [
            'client_id' => $this->client_id,
            'redirect_uri' => $this->redirect_url,
            'response_type' => 'code',
            'approval_prompt' => 'auto',
            'scope' => 'profile:read_all',
        ];
        return "{$strava_auth_url}?".http_build_query($data);
    }

    /** @return ?array<string, mixed> */
    public function getTokenDataForCode(string $code): ?array {
        $token_request_data = [
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'code' => $code,
            'grant_type' => 'authorization_code',
        ];
        $token_response = $this->strava_fetcher->fetchTokenDataForCode($token_request_data);

        if (!isset($token_response['token_type'])) {
            return null;
        }

        return [
            'token_type' => $token_response['token_type'],
            'expires_at' => $token_response['expires_at'],
            'refresh_token' => $token_response['refresh_token'],
            'access_token' => $token_response['access_token'],
            'user_identifier' => $token_response['athlete']['id'],
            'first_name' => $token_response['athlete']['firstname'],
            'last_name' => $token_response['athlete']['lastname'],
            'gender' => $token_response['athlete']['sex'],
            'city' => $token_response['athlete']['city'],
            'region' => $token_response['athlete']['state'],
            'country' => $token_response['athlete']['country'],
            'profile_picture_url' => $token_response['athlete']['profile'],
        ];
    }

    /**
     * @param array<string, mixed> $token_data
     *
     * @return array<string, mixed>
     */
    public function getUserData(array $token_data): array {
        return $token_data;
    }
}
