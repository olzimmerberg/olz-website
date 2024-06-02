<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity;

use Olz\Entity\AuthRequest;
use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;

/**
 * @extends FakeOlzRepository<AuthRequest>
 */
class FakeAuthRequestRepository extends FakeOlzRepository {
    public string $olzEntityClass = AuthRequest::class;
    public string $fakeOlzEntityClass = FakeAuthRequest::class;

    /** @var array<mixed> */
    public array $auth_requests = [];
    public int $num_remaining_attempts = 3;
    public bool $can_validate_access_token = true;

    public function addAuthRequest(
        string $ip_address,
        string $action,
        string $username,
        ?\DateTime $timestamp = null,
    ): void {
        $this->auth_requests[] = [
            'ip_address' => $ip_address,
            'action' => $action,
            'timestamp' => $timestamp,
            'username' => $username,
        ];
    }

    public function numRemainingAttempts(string $ip_address, ?\DateTime $timestamp = null): int {
        return $this->num_remaining_attempts;
    }

    public function canValidateAccessToken(string $ip_address, ?\DateTime $timestamp = null): bool {
        return $this->can_validate_access_token;
    }
}
