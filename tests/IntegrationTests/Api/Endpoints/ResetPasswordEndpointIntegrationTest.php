<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests\Api\Endpoints;

use Olz\Api\Endpoints\ResetPasswordEndpoint;
use Olz\Tests\IntegrationTests\Common\IntegrationTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class ResetPasswordEndpointForIntegrationTest extends ResetPasswordEndpoint {
    public function testOnlyGetRandomPassword(): string {
        return $this->getRandomPassword();
    }
}

/**
 * @internal
 *
 * @covers \Olz\Api\Endpoints\ResetPasswordEndpoint
 */
final class ResetPasswordEndpointIntegrationTest extends IntegrationTestCase {
    public function testGetRandomPassword(): void {
        $endpoint = new ResetPasswordEndpointForIntegrationTest();
        $endpoint->setup();
        $this->assertMatchesRegularExpression(
            '/^[a-zA-Z0-9-_]{8}$/',
            $endpoint->testOnlyGetRandomPassword()
        );
    }
}
