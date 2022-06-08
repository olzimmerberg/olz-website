<?php

declare(strict_types=1);

require_once __DIR__.'/../../../../_/api/endpoints/DeleteUserEndpoint.php';
require_once __DIR__.'/../../common/IntegrationTestCase.php';

/**
 * @internal
 * @coversNothing
 */
class DeleteUserEndpointForIntegrationTest extends DeleteUserEndpoint {
    public function testOnlyIsFile($path) {
        return $this->isFile($path);
    }

    public function testOnlyUnlink($path) {
        return $this->unlink($path);
    }

    public function testOnlyRename($source_path, $destination_path) {
        return $this->rename($source_path, $destination_path);
    }
}

/**
 * @internal
 * @covers \DeleteUserEndpoint
 */
final class DeleteUserEndpointIntegrationTest extends IntegrationTestCase {
    public function testIsFile(): void {
        $endpoint = new DeleteUserEndpointForIntegrationTest();
        $this->assertSame(true, $endpoint->testOnlyIsFile(__FILE__));
        $this->assertSame(false, $endpoint->testOnlyIsFile(__DIR__.'/does_not_exist.txt'));
    }

    public function testUnlink(): void {
        $endpoint = new DeleteUserEndpointForIntegrationTest();
        $path = __DIR__.'/../../document-root/temp/delete_user_endpoint.txt';
        file_put_contents($path, 'some content');
        $this->assertSame(true, is_file($path));
        $endpoint->testOnlyUnlink($path);
        $this->assertSame(false, is_file($path));
    }

    public function testRename(): void {
        $endpoint = new DeleteUserEndpointForIntegrationTest();
        $from_path = __DIR__.'/../../document-root/temp/delete_user_endpoint_from.txt';
        $to_path = __DIR__.'/../../document-root/temp/delete_user_endpoint_to.txt';
        file_put_contents($from_path, 'some content');
        $this->assertSame(true, is_file($from_path));
        $this->assertSame(false, is_file($to_path));
        $endpoint->testOnlyRename($from_path, $to_path);
        $this->assertSame(false, is_file($from_path));
        $this->assertSame(true, is_file($to_path));
        unlink($to_path);
    }
}
