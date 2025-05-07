<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Termine\Endpoints;

use Olz\Termine\Endpoints\ListTerminLabelsEndpoint;
use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Termine\Endpoints\ListTerminLabelsEndpoint
 */
final class ListTerminLabelsEndpointTest extends UnitTestCase {
    public function testListTerminLabelsEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => false];
        $endpoint = new ListTerminLabelsEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                "INFO Valid user request",
                "NOTICE HTTP error 403",
            ], $this->getLogs());
            $this->assertSame(403, $err->getCode());
        }
    }

    public function testListTerminLabelsEndpoint(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new ListTerminLabelsEndpoint();
        $endpoint->runtimeSetup();

        $id = FakeOlzRepository::MAXIMAL_ID;
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

        $result = $endpoint->call([]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame([
            'items' => [
                [
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
                ],
                [
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
                ],
                [
                    'id' => 1234,
                    'meta' => [
                        'ownerUserId' => 1,
                        'ownerRoleId' => 1,
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
                ],
            ],
        ], $result);
    }
}
