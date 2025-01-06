<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Termine\Endpoints;

use Olz\Termine\Endpoints\GetTerminTemplateEndpoint;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Termine\Endpoints\GetTerminTemplateEndpoint
 */
final class GetTerminTemplateEndpointTest extends UnitTestCase {
    public function testGetTerminTemplateEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => false];
        $endpoint = new GetTerminTemplateEndpoint();
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

    public function testGetTerminTemplateEndpointMinimal(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new GetTerminTemplateEndpoint();
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
                'startTime' => null,
                'durationSeconds' => null,
                'title' => '',
                'text' => '',
                'deadlineEarlierSeconds' => null,
                'deadlineTime' => null,
                'shouldPromote' => false,
                'newsletter' => true,
                'types' => [],
                'locationId' => null,
                'imageIds' => [],
                'fileIds' => [],
            ],
        ], $result);
    }

    public function testGetTerminTemplateEndpointEmpty(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new GetTerminTemplateEndpoint();
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
                'startTime' => null,
                'durationSeconds' => null,
                'title' => '',
                'text' => '',
                'deadlineEarlierSeconds' => null,
                'deadlineTime' => null,
                'shouldPromote' => false,
                'newsletter' => false,
                'types' => [],
                'locationId' => null,
                'imageIds' => [],
                'fileIds' => [],
            ],
        ], $result);
    }

    public function testGetTerminTemplateEndpointMaximal(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new GetTerminTemplateEndpoint();
        $endpoint->runtimeSetup();

        mkdir(__DIR__.'/../../tmp/temp/');
        mkdir(__DIR__.'/../../tmp/img/');
        mkdir(__DIR__.'/../../tmp/img/termin_templates/');
        mkdir(__DIR__.'/../../tmp/img/termin_templates/1234/');
        mkdir(__DIR__.'/../../tmp/img/termin_templates/1234/img/');
        file_put_contents(__DIR__.'/../../tmp/img/termin_templates/1234/img/image__________________1.jpg', '');
        file_put_contents(__DIR__.'/../../tmp/img/termin_templates/1234/img/image__________________2.png', '');
        mkdir(__DIR__.'/../../tmp/files/');
        mkdir(__DIR__.'/../../tmp/files/termin_templates/');
        mkdir(__DIR__.'/../../tmp/files/termin_templates/1234/');
        file_put_contents(__DIR__.'/../../tmp/files/termin_templates/1234/file___________________1.pdf', '');
        file_put_contents(__DIR__.'/../../tmp/files/termin_templates/1234/file___________________2.pdf', '');

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
                'ownerUserId' => 1,
                'ownerRoleId' => 1,
                'onOff' => true,
            ],
            'data' => [
                'startTime' => '09:00:00',
                'durationSeconds' => 7200,
                'title' => 'Fake title',
                'text' => 'Fake text',
                'deadlineEarlierSeconds' => 172800,
                'deadlineTime' => '18:00:00',
                'shouldPromote' => true,
                'newsletter' => true,
                'types' => ['ol', 'club'],
                'locationId' => 12341,
                'imageIds' => ['image__________________1.jpg', 'image__________________2.png'],
                'fileIds' => ['file___________________1.pdf', 'file___________________2.pdf'],
            ],
        ], $result);
    }
}
