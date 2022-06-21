<?php

declare(strict_types=1);

use Olz\Api\Endpoints\UpdateUserEndpoint;

require_once __DIR__.'/../../common/IntegrationTestCase.php';

/**
 * @internal
 * @coversNothing
 */
class UpdateUserEndpointForIntegrationTest extends UpdateUserEndpoint {
    public function testOnlyUnlink($path) {
        return $this->unlink($path);
    }

    public function testOnlyRename($source_path, $destination_path) {
        return $this->rename($source_path, $destination_path);
    }
}

/**
 * @internal
 * @covers \Olz\Api\Endpoints\UpdateUserEndpoint
 */
final class UpdateUserEndpointIntegrationTest extends IntegrationTestCase {
    public function testUnlink(): void {
        $endpoint = new UpdateUserEndpointForIntegrationTest();
        $path = __DIR__.'/../../document-root/temp/update_user_endpoint.txt';
        file_put_contents($path, 'some content');
        $this->assertSame(true, is_file($path));
        $endpoint->testOnlyUnlink($path);
        $this->assertSame(false, is_file($path));
    }

    public function testRename(): void {
        $endpoint = new UpdateUserEndpointForIntegrationTest();
        $from_path = __DIR__.'/../../document-root/temp/update_user_endpoint_from.txt';
        $to_path = __DIR__.'/../../document-root/temp/update_user_endpoint_to.txt';
        file_put_contents($from_path, 'some content');
        $this->assertSame(true, is_file($from_path));
        $this->assertSame(false, is_file($to_path));
        $endpoint->testOnlyRename($from_path, $to_path);
        $this->assertSame(false, is_file($from_path));
        $this->assertSame(true, is_file($to_path));
        unlink($to_path);
    }
}
