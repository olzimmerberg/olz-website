<?php

declare(strict_types=1);

use Olz\Api\Endpoints\ResetPasswordEndpoint;

require_once __DIR__.'/../../common/IntegrationTestCase.php';

/**
 * @internal
 * @coversNothing
 */
class ResetPasswordEndpointForIntegrationTest extends ResetPasswordEndpoint {
    public function testOnlyGetRandomPassword() {
        return $this->getRandomPassword();
    }
}

/**
 * @internal
 * @covers \ResetPasswordEndpoint
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
