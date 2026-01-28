<?php

namespace Olz\Utils;

use Olz\Entity\StravaLink;
use PhpTypeScriptApi\HttpError;

class StravaUtils {
    use WithUtilsTrait;

    public function getClientId(): string {
        return $this->envUtils()->getStravaClientId();
    }

    protected function getClientSecret(): string {
        return $this->envUtils()->getStravaClientSecret();
    }

    /** @param array<string> $scopes */
    public function getRegistrationUrl(
        array $scopes = ['read'],
        ?string $redirect_url = null,
    ): string {
        $client_id = $this->getClientId();
        $base_href = $this->envUtils()->getBaseHref();
        $code_href = $this->envUtils()->getCodeHref();
        $redirect_url_suffix = $redirect_url ? "?redirect_url=".urlencode($redirect_url) : '';
        $strava_redirect_url = "{$base_href}{$code_href}strava_redirect{$redirect_url_suffix}";
        $enc_strava_redirect_url = urlencode($strava_redirect_url);
        $enc_scopes = urlencode(implode(',', $scopes));
        return "https://www.strava.com/oauth/authorize?client_id={$client_id}&response_type=code&redirect_uri={$enc_strava_redirect_url}&approval_prompt=force&scope={$enc_scopes}";
    }

    public function linkStrava(string $code): ?StravaLink {
        $data = $this->fetchTokenDataForCode([
            'client_id' => $this->getClientId(),
            'client_secret' => $this->getClientSecret(),
            'code' => $code,
            'grant_type' => 'authorization_code',
        ]);
        $athlete_id = $data['athlete']['id'] ?? null;
        $access_token = $data['access_token'] ?? null;
        $refresh_token = $data['refresh_token'] ?? null;
        $expires_at = $data['expires_at'] ?? null;
        if ($athlete_id === null || $access_token === null || $refresh_token === null || $expires_at === null) {
            return null;
        }

        $now = new \DateTime($this->dateUtils()->getIsoNow());
        $user = $this->authUtils()->getCurrentUser();
        if (!$user) {
            throw new HttpError(401, 'Nicht eingeloggt!');
        }

        $strava_link_repo = $this->entityManager()->getRepository(StravaLink::class);
        $strava_link = $strava_link_repo->findOneBy(['strava_user' => $athlete_id]);
        if ($strava_link === null) {
            $strava_link = new StravaLink();
            $strava_link->setCreatedAt($now);
        } else {
            $previous_user_id = $strava_link->getUser()->getId();
            $new_user_id = $user->getId();
            if ($previous_user_id !== $new_user_id) {
                $this->log()->notice("{$strava_link} changed user: {$previous_user_id} => {$new_user_id}");
            }
        }
        $strava_link->setUser($user);
        $strava_link->setAccessToken($access_token);
        $strava_link->setRefreshToken($refresh_token);
        $strava_link->setExpiresAt(new \DateTime(date('Y-m-d H:i:s', $expires_at)));
        $strava_link->setStravaUser($athlete_id);
        $strava_link->setLinkedAt($now);

        $this->entityManager()->persist($strava_link);
        $this->entityManager()->flush();
        return $strava_link;
    }

    public function getAccessToken(StravaLink $strava_link): ?string {
        $now_iso = $this->dateUtils()->getIsoNow();
        $expires_at_iso = $strava_link->getExpiresAt()->format('Y-m-d H:i:s');
        $access_token = $strava_link->getAccessToken();
        if ($access_token && $expires_at_iso > $now_iso) {
            return $access_token;
        }
        $this->log()->debug('Strava token refresh...');
        $refresh_token = $strava_link->getRefreshToken();

        $data = $this->fetchTokenDataForCode([
            'client_id' => $this->getClientId(),
            'client_secret' => $this->getClientSecret(),
            'refresh_token' => $refresh_token,
            'grant_type' => 'refresh_token',
        ]);
        $this->log()->debug('Strava token refresh response:', [$data]);
        $access_token = $data['access_token'] ?? null;
        $refresh_token = $data['refresh_token'] ?? null;
        $expires_at = $data['expires_at'] ?? null;

        if (!$access_token || !$refresh_token || !$expires_at) {
            $json_data = json_encode($data) ?: '';
            $this->log()->notice("Refreshing strava token failed: {$json_data}");
            return null;
        }
        $this->log()->debug('Strava token refreshed');
        $strava_link->setAccessToken($access_token);
        $strava_link->setRefreshToken($refresh_token);
        $strava_link->setExpiresAt(new \DateTime(date('Y-m-d H:i:s', $expires_at)));
        $this->entityManager()->flush();
        return $access_token;
    }

    /**
     * @param array<string, mixed> $token_request_data
     *
     * @return ?array<string, mixed>
     */
    public function fetchTokenDataForCode(array $token_request_data): ?array {
        $strava_token_url = 'https://www.strava.com/api/v3/oauth/token';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $strava_token_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($token_request_data, '', '&'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $token_result = curl_exec($ch);
        return json_decode(!is_bool($token_result) ? $token_result : '', true);
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
        $strava_url = 'https://www.strava.com/api/v3';
        $query_string = http_build_query($query, '', '&');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "{$strava_url}{$path}");
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer {$access_token}",
            "Content-Type: application/x-www-form-urlencoded",
        ]);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_POST, $method === 'POST');
        if ($query_string) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $query_string);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        return json_decode(!is_bool($result) ? $result : '', true);
    }
}
