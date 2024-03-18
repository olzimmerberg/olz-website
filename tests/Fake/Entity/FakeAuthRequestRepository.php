<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity;

use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;

class FakeAuthRequestRepository extends FakeOlzRepository {
    public $fakeOlzEntityClass = FakeAuthRequest::class;

    public $auth_requests = [];
    public $num_remaining_attempts = 3;
    public $can_validate_access_token = true;

    public function addAuthRequest($ip_address, $action, $username, $timestamp = null) {
        $this->auth_requests[] = [
            'ip_address' => $ip_address,
            'action' => $action,
            'timestamp' => $timestamp,
            'username' => $username,
        ];
    }

    public function numRemainingAttempts($ip_address, $timestamp = null) {
        return $this->num_remaining_attempts;
    }

    public function canValidateAccessToken($ip_address, $timestamp = null) {
        return $this->can_validate_access_token;
    }
}
