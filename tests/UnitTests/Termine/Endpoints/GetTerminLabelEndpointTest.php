<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Termine\Endpoints;

use Olz\Termine\Endpoints\GetTerminLabelEndpoint;
use Olz\Tests\Fake;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Termine\Endpoints\GetTerminLabelEndpoint
 */
final class GetTerminLabelEndpointTest extends UnitTestCase {
    public function testGetTerminLabelEndpointIdent(): void {
        $endpoint = new GetTerminLabelEndpoint();
        $this->assertSame('GetTerminLabelEndpoint', $endpoint->getIdent());
    }

    public function testGetTerminLabelEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => false];
        $endpoint = new GetTerminLabelEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                'id' => 123,
            ]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                "INFO Valid user request",
                "WARNING HTTP error 403",
            ], $this->getLogs());
            $this->assertSame(403, $err->getCode());
        }
    }

    public function testGetTerminLabelEndpointMinimal(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new GetTerminLabelEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'id' => 12,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame([
            'id' => 12,
            'meta' => [
                'ownerUserId' => null,
                'ownerRoleId' => null,
                'onOff' => true,
            ],
            'data' => [
                'ident' => '-',
                'name' => '-',
                'details' => '',
                'icon' => null,
                'position' => 0,
                'imageIds' => [],
                'fileIds' => [],
            ],
        ], $result);
    }

    public function testGetTerminLabelEndpointEmpty(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new GetTerminLabelEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'id' => 123,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame([
            'id' => 123,
            'meta' => [
                'ownerUserId' => null,
                'ownerRoleId' => null,
                'onOff' => false,
            ],
            'data' => [
                'ident' => '-',
                'name' => '-',
                'details' => '',
                'icon' => null,
                'position' => 0,
                'imageIds' => [],
                'fileIds' => [],
            ],
        ], $result);
    }

    public function testGetTerminLabelEndpointMaximal(): void {
        $id = Fake\FakeOlzRepository::MAXIMAL_ID;
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new GetTerminLabelEndpoint();
        $endpoint->runtimeSetup();

        mkdir(__DIR__.'/../../tmp/temp/');
        mkdir(__DIR__.'/../../tmp/files/');
        mkdir(__DIR__.'/../../tmp/files/termin_labels/');
        mkdir(__DIR__."/../../tmp/files/termin_labels/{$id}/");
        file_put_contents(__DIR__."/../../tmp/files/termin_labels/{$id}/aaaaaaaaaaaaaaaaaaaaaaaa.svg", '');
        file_put_contents(__DIR__."/../../tmp/files/termin_labels/{$id}/file___________________1.pdf", '');
        file_put_contents(__DIR__."/../../tmp/files/termin_labels/{$id}/file___________________2.txt", '');
        mkdir(__DIR__.'/../../tmp/img/');
        mkdir(__DIR__.'/../../tmp/img/termin_labels/');
        mkdir(__DIR__."/../../tmp/img/termin_labels/{$id}/");
        mkdir(__DIR__."/../../tmp/img/termin_labels/{$id}/img");
        file_put_contents(__DIR__."/../../tmp/img/termin_labels/{$id}/img/picture________________A.jpg", '');
        file_put_contents(__DIR__."/../../tmp/img/termin_labels/{$id}/img/picture________________B.jpg", '');

        $result = $endpoint->call([
            'id' => 1234,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame([
            'id' => 1234,
            'meta' => [
                'ownerUserId' => null,
                'ownerRoleId' => null,
                'onOff' => true,
            ],
            'data' => [
                'ident' => 'test-label',
                'name' => 'Test Termin-Label',
                'details' => 'Test Termin-Label Detail',
                'icon' => 'aaaaaaaaaaaaaaaaaaaaaaaa.svg',
                'position' => 1234,
                'imageIds' => ['picture________________A.jpg', 'picture________________B.jpg'],
                'fileIds' => ['aaaaaaaaaaaaaaaaaaaaaaaa.svg', 'file___________________1.pdf', 'file___________________2.txt'],
            ],
        ], $result);
    }
}
