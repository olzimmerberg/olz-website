<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Apps\Members\Endpoints;

use Olz\Apps\Members\Endpoints\ExportMembersEndpoint;
use Olz\Tests\Fake\Entity\Users\FakeUser;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Apps\Members\Endpoints\ExportMembersEndpoint
 */
final class ExportMembersEndpointTest extends UnitTestCase {
    public function testExportMembersEndpoint(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['vorstand' => true];
        WithUtilsCache::get('authUtils')->current_user = FakeUser::adminUser();
        $endpoint = new ExportMembersEndpoint();
        $endpoint->runtimeSetup();

        mkdir(__DIR__.'/../../../tmp/temp/');

        $result = $endpoint->call([]);

        $this->assertSame([
            'status' => 'OK',
            'csvFileId' => 'AAAAAAAAAAAAAAAAAAAAAAAA.csv',
        ], $result);
        $this->assertSame(
            <<<'ZZZZZZZZZZ'
                [Id],Benutzer-Id,Vorname,Nachname
                10001234,minimal-user,Maximal,User

                ZZZZZZZZZZ,
            file_get_contents(__DIR__.'/../../../tmp/temp/AAAAAAAAAAAAAAAAAAAAAAAA.csv'),
        );
        $this->assertSame([
            "INFO Valid user request",
            "INFO Members export by admin.",
            "INFO Valid user response",
        ], $this->getLogs());
    }

    public function testExportMembersEndpointNotAuthorized(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['vorstand' => false];
        $endpoint = new ExportMembersEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame('Kein Zugriff!', $httperr->getMessage());
            $this->assertSame([
                "INFO Valid user request",
                "NOTICE HTTP error 403",
            ], $this->getLogs());
        }
    }
}
