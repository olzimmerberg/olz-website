<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Termine\Endpoints;

use Olz\Termine\Endpoints\EditTerminLabelEndpoint;
use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;
use Olz\Tests\Fake\Entity\Termine\FakeTerminLabel;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Termine\Endpoints\EditTerminLabelEndpoint
 */
final class EditTerminLabelEndpointTest extends UnitTestCase {
    public function testEditTerminLabelEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['termine' => false];
        $endpoint = new EditTerminLabelEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                'id' => 123,
            ]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                "INFO Valid user request",
                "NOTICE HTTP error 403",
            ], $this->getLogs());
            $this->assertSame(403, $err->getCode());
        }
    }

    public function testEditTerminLabelEndpointNoSuchEntity(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['termine' => true];
        $endpoint = new EditTerminLabelEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                'id' => 9999,
            ]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                "INFO Valid user request",
                "NOTICE HTTP error 404",
            ], $this->getLogs());
            $this->assertSame(404, $err->getCode());
        }
    }

    public function testEditTerminLabelEndpointMinimal(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['termine' => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new EditTerminLabelEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'id' => 12,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());

        $this->assertSame([
            [FakeTerminLabel::minimal(), null, null, null, null, 'termine_admin'],
        ], WithUtilsCache::get('entityUtils')->can_update_olz_entity_calls);

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
                'position' => 0.0,
                'imageIds' => [],
                'fileIds' => [],
            ],
        ], $result);
    }

    public function testEditTerminLabelEndpointEmpty(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['termine' => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new EditTerminLabelEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'id' => 123,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());

        $this->assertSame([
            [FakeTerminLabel::empty(), null, null, null, null, 'termine_admin'],
        ], WithUtilsCache::get('entityUtils')->can_update_olz_entity_calls);

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
                'position' => 0.0,
                'imageIds' => [],
                'fileIds' => [],
            ],
        ], $result);
    }

    public function testEditTerminLabelEndpointMaximal(): void {
        $id = FakeOlzRepository::MAXIMAL_ID;
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['termine' => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new EditTerminLabelEndpoint();
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
            [FakeTerminLabel::maximal(), 'default', 'default', 'role', null, 'termine_admin'],
        ], WithUtilsCache::get('entityUtils')->can_update_olz_entity_calls);

        $this->assertSame([
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
                'position' => 1234.0,
                'imageIds' => ['picture________________A.jpg', 'picture________________B.jpg'],
                'fileIds' => ['aaaaaaaaaaaaaaaaaaaaaaaa.svg', 'file___________________1.pdf', 'file___________________2.txt'],
            ],
        ], $result);
    }
}
