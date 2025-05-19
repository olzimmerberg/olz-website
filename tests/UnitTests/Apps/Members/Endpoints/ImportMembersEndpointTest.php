<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Apps\Members\Endpoints;

use Olz\Apps\Members\Endpoints\ImportMembersEndpoint;
use Olz\Tests\Fake\Entity\Users\FakeUser;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Apps\Members\Endpoints\ImportMembersEndpoint
 */
final class ImportMembersEndpointTest extends UnitTestCase {
    public function testImportMembersEndpoint(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['vorstand' => true];
        WithUtilsCache::get('authUtils')->current_user = FakeUser::adminUser();
        $endpoint = new ImportMembersEndpoint();
        $endpoint->runtimeSetup();

        mkdir(__DIR__.'/../../../tmp/temp/');
        file_put_contents(__DIR__.'/../../../tmp/temp/uploaded_file.csv', <<<'ZZZZZZZZZZ'
            [Id];Benutzer-Id;Vorname;Nachname
            10000123;empty-user;Empty;User
            10001234;maximal-user;Max;User
            ZZZZZZZZZZ);

        $result = $endpoint->call([
            'csvFileId' => 'uploaded_file.csv',
        ]);

        $this->assertSame([
            'status' => 'OK',
            'members' => [
                [
                    'ident' => '10000123',
                    'action' => 'CREATE',
                    'username' => 'empty-user',
                    'matchingUsername' => 'empty-user',
                    'user' => null,
                    'updates' => [],
                ],
                [
                    'ident' => '10001234',
                    'action' => 'UPDATE',
                    'username' => 'maximal-user',
                    'matchingUsername' => null,
                    'user' => ['id' => 1234, 'firstName' => 'Maximal', 'lastName' => 'User'],
                    'updates' => [
                        'Vorname' => ['old' => 'Max', 'new' => 'Maximal'],
                        'Adresse' => ['old' => '', 'new' => 'Data Hwy. 42'],
                        'PLZ' => ['old' => '', 'new' => '19216811'],
                        'Ort' => ['old' => '', 'new' => 'Test'],
                    ],
                ],
                [
                    'ident' => '10000012',
                    'action' => 'DELETE',
                    'username' => null,
                    'matchingUsername' => null,
                    'user' => null,
                    'updates' => [],
                ],
            ],
        ], $result);
        $this->assertSame([
            "INFO Valid user request",
            "INFO Members import by admin.",
            "INFO Field Vorname was updated.",
            "INFO Field Adresse was updated.",
            "INFO Field PLZ was updated.",
            "INFO Field Ort was updated.",
            "INFO Valid user response",
        ], $this->getLogs());
    }

    public function testImportMembersEndpointNotAuthorized(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['vorstand' => false];
        $endpoint = new ImportMembersEndpoint();
        $endpoint->runtimeSetup();

        mkdir(__DIR__.'/../../../tmp/temp/');
        file_put_contents(__DIR__.'/../../../tmp/temp/uploaded_file.csv', '');

        try {
            $endpoint->call([
                'csvFileId' => 'uploaded_file.csv',
            ]);
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
