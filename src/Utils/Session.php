<?php

namespace Olz\Utils;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Session extends AbstractSession {
    public function __construct(
        protected ?RequestStack $requestStack = null,
    ) {
    }

    /** @param array{timeout?: int} $config */
    public function resetConfigure(array $config): void {
        // Only called on login: this is the only place a session is started.
        $timeout = $config['timeout'] ?? 2419200; // keep one month
        $session = $this->getCurrentRequest()->getSession();
        if (!$session->isStarted()) {
            $session->start();
        }
        $session->invalidate($timeout);
    }

    public function has(string $key): bool {
        $session = $this->getSession();
        return $session !== null && $session->has($key);
    }

    public function get(string $key): ?string {
        $session = $this->getSession();
        if ($session === null) {
            return null;
        }
        try {
            $value = $session->get($key);
        } catch (\Throwable $th) {
            // The session could not be read (e.g. the session store being
            // unavailable). Treat it as if the attribute were not set, instead
            // of blowing up the current operation.
            return null;
        }
        return is_scalar($value) ? "{$value}" : null;
    }

    public function set(string $key, ?string $new_value): void {
        $session = $this->getSession();
        if ($session === null) {
            return;
        }
        $session->set($key, $new_value);
    }

    public function delete(string $key): void {
        $session = $this->getSession();
        if ($session === null) {
            return;
        }
        $session->remove($key);
    }

    public function clear(): void {
        $session = $this->getSession();
        if ($session !== null) {
            $session->invalidate();
        }
    }

    protected function getCurrentRequest(): Request {
        $request = $this->requestStack?->getCurrentRequest();
        if ($request === null) {
            throw new \LogicException('Cannot access the session without a current request.');
        }
        return $request;
    }

    /**
     * Returns the session if it is already started, or resumes it from a session
     * cookie when one is present. Never starts a fresh session (that only happens
     * on login), and treats a malformed session cookie as "no session".
     */
    protected function getSession(): ?SessionInterface {
        $request = $this->requestStack?->getCurrentRequest();
        if ($request === null || !$request->hasSession()) {
            return null;
        }
        $session = $request->getSession();
        if ($session->isStarted()) {
            return $session;
        }
        if (!$request->hasPreviousSession()) {
            return null;
        }
        try {
            $session->start();
        } catch (\Throwable $th) {
            return null;
        }
        return $session;
    }
}
