<?php

namespace Olz\EventListener;

use Olz\Common\Utils\AuthUtils;
use Olz\Common\Utils\WithUtilsTrait;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

/**
 * Restores the session from the refresh token cookie during the initial page
 * load. The session only lives for a month, while the refresh token lives for
 * a year. Without this, a user whose session has expired would be rendered as
 * logged out, until the next API call triggers a refresh.
 */
#[AsEventListener(event: 'kernel.request', method: 'onKernelRequest')]
#[AsEventListener(event: 'kernel.response', method: 'onKernelResponse')]
class SessionRefreshListener {
    use WithUtilsTrait;

    protected const REFRESH_COOKIE_ATTRIBUTE = '_olz_refresh_cookie';

    public function onKernelRequest(RequestEvent $event): void {
        if (!$event->isMainRequest()) {
            return;
        }
        $request = $event->getRequest();
        if (!$this->isPageRequest($request)) {
            return;
        }
        $raw_token = $request->cookies->get(AuthUtils::REFRESH_TOKEN_COOKIE);
        if ($raw_token === null || $raw_token === '') {
            // Never start a session unless there is a refresh token to restore.
            return;
        }
        if ($this->session()->has('user')) {
            return;
        }
        try {
            $new_raw_token = $this->authUtils()->refreshSessionFromRefreshToken($raw_token);
        } catch (\Exception $exc) {
            // Expected: the refresh token is missing, unknown, expired, or was
            // already rotated. Treat the request as logged out.
            return;
        } catch (\Throwable $th) {
            // Never break the page because the session cannot be restored.
            $this->log()->warning("Could not restore session from refresh token: {$th->getMessage()}", [$th]);
            return;
        }
        if ($new_raw_token === null) {
            return;
        }
        $this->log()->info('Restored session and rotated refresh token on page load.');
        // The (rotated) refresh token cookie is added to the response once it
        // is available, see `onKernelResponse`.
        $request->attributes->set(self::REFRESH_COOKIE_ATTRIBUTE, $new_raw_token);
    }

    public function onKernelResponse(ResponseEvent $event): void {
        if (!$event->isMainRequest()) {
            return;
        }
        $raw_token = $event->getRequest()->attributes->get(self::REFRESH_COOKIE_ATTRIBUTE);
        if (!is_string($raw_token)) {
            return;
        }
        $event->getResponse()->headers->setCookie(
            $this->authUtils()->createRefreshTokenCookie($raw_token)
        );
    }

    protected function isPageRequest(Request $request): bool {
        if (!$request->hasSession()) {
            return false;
        }
        if (!$request->isMethodSafe()) {
            return false;
        }
        if ($request->query->has('access_token')) {
            // Token-based authentication does not use the session.
            return false;
        }
        // API requests refresh the session themselves, see `OlzApi.ts`.
        return !str_starts_with($request->getPathInfo(), '/api/');
    }
}
