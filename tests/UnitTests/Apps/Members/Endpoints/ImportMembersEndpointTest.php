<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Apps\Members\Endpoints;

use Olz\Apps\Members\Endpoints\ImportMembersEndpoint;
use Olz\Common\Utils\WithUtilsCache;
use Olz\Entity\Members\Member;
use Olz\Tests\Fake\Entity\Members\FakeMemberRepository;
use Olz\Tests\Fake\Entity\Users\FakeUser;
use Olz\Tests\Fake\FakeEntityManager;
use Olz\Tests\UnitTests\Common\UnitTestCase;
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
            [Id];Benutzer-Id;Vorname;Nachname;Firma;Status;Eintritt;Austritt
            10000123;empty-user;Empty;User;;Aktivmitglied;13.01.2006;
            10001234;maximal-user;Max;User;;Ehemaliges Mitglied;13.01.2006;31.12.2006
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
                    'firstName' => 'Empty',
                    'lastName' => 'User',
                    'company' => '',
                    'matchingUsername' => 'empty-user',
                    'user' => null,
                    'updates' => [],
                ],
                [
                    'ident' => '10001234',
                    'action' => 'UPDATE',
                    'username' => 'maximal-user',
                    'firstName' => 'Max',
                    'lastName' => 'User',
                    'company' => '',
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
                    'firstName' => null,
                    'lastName' => null,
                    'company' => null,
                    'matchingUsername' => null,
                    'user' => null,
                    'updates' => [],
                ],
            ],
        ], $result);
        $this->assertSame([
            "INFO Valid user request",
            "INFO Members import by admin.",
            "INFO User maximal-user: left_on was updated.",
            "INFO Field Vorname was updated.",
            "INFO Field Adresse was updated.",
            "INFO Field PLZ was updated.",
            "INFO Field Ort was updated.",
            "INFO Valid user response",
        ], $this->getLogs());

        $entity_utils = WithUtilsCache::get('entityUtils');
        // Status 'Aktivmitglied' (active) => member is created.
        $this->assertCount(1, $entity_utils->create_olz_entity_calls);
        // Status 'Ehemaliges Mitglied' (inactive) => member is updated.
        $this->assertCount(1, $entity_utils->update_olz_entity_calls);
        // Member 10000012 is missing from the CSV => member is (soft-)deleted.
        $this->assertCount(1, $entity_utils->delete_olz_entity_calls);
        $this->assertSame([], $entity_utils->restore_olz_entity_calls);
    }

    public function testImportMembersEndpointSkipsAlreadyDeletedMembers(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['vorstand' => true];
        WithUtilsCache::get('authUtils')->current_user = FakeUser::adminUser();
        $member_repo = $this->getMemberRepository();
        // Member 10000012 was deleted by an earlier import, so it is not active anymore.
        $member_repo->activeIdents = [];
        $member_repo->deletedIdents = ['10000012'];
        $endpoint = new ImportMembersEndpoint();
        $endpoint->runtimeSetup();
        $this->writeCsv(<<<'ZZZZZZZZZZ'
            [Id];Benutzer-Id;Vorname;Nachname;Firma;Status;Eintritt;Austritt
            10001234;maximal-user;Max;User;;Ehemaliges Mitglied;13.01.2006;31.12.2006
            ZZZZZZZZZZ);

        $result = $endpoint->call([
            'csvFileId' => 'uploaded_file.csv',
        ]);

        // The already deleted member must neither be reported nor deleted again.
        $this->assertSame('10001234:UPDATE', $this->getActionsSummary($result));
        $entity_utils = WithUtilsCache::get('entityUtils');
        $this->assertSame([], $entity_utils->delete_olz_entity_calls);
        $this->assertSame([], $entity_utils->restore_olz_entity_calls);
    }

    public function testImportMembersEndpointRestoresRejoinedMember(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['vorstand' => true];
        WithUtilsCache::get('authUtils')->current_user = FakeUser::adminUser();
        $member_repo = $this->getMemberRepository();
        // Member 10001234 was deleted by an earlier import, but has now rejoined.
        $member_repo->activeIdents = [];
        $member_repo->deletedIdents = ['10001234'];
        $endpoint = new ImportMembersEndpoint();
        $endpoint->runtimeSetup();
        $this->writeCsv(<<<'ZZZZZZZZZZ'
            [Id];Benutzer-Id;Vorname;Nachname;Firma;Status;Eintritt;Austritt
            10001234;maximal-user;Max;User;;Ehemaliges Mitglied;13.01.2006;31.12.2006
            ZZZZZZZZZZ);

        $result = $endpoint->call([
            'csvFileId' => 'uploaded_file.csv',
        ]);

        $this->assertSame('10001234:UPDATE', $this->getActionsSummary($result));
        $entity_utils = WithUtilsCache::get('entityUtils');
        $this->assertCount(1, $entity_utils->restore_olz_entity_calls);
        $restored_member = $entity_utils->restore_olz_entity_calls[0];
        $this->assertNull($restored_member->getDeletedAt());
        $this->assertNull($restored_member->getDeletedByUser());
        $this->assertSame(
            '2020-03-13 19:30:00',
            $restored_member->getLastModifiedAt()->format('Y-m-d H:i:s'),
        );
        $this->assertSame([], $entity_utils->delete_olz_entity_calls);
        $this->assertContains('INFO Member 10001234 was restored.', $this->getLogs());
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
                "NOTICE HTTP error 403 Kein Zugriff!",
            ], $this->getLogs());
        }
    }

    protected function getMemberRepository(): FakeMemberRepository {
        $entity_manager = WithUtilsCache::get('entityManager');
        assert($entity_manager instanceof FakeEntityManager);
        $member_repo = $entity_manager->repositories[Member::class] ?? null;
        assert($member_repo instanceof FakeMemberRepository);
        return $member_repo;
    }

    protected function writeCsv(string $csv_content): void {
        mkdir(__DIR__.'/../../../tmp/temp/');
        file_put_contents(__DIR__.'/../../../tmp/temp/uploaded_file.csv', $csv_content);
    }

    /**
     * A summary of all member actions in the response, e.g. "10000012:DELETE, 10001234:UPDATE".
     */
    protected function getActionsSummary(mixed $result): string {
        assert(is_array($result));
        $members = $result['members'] ?? null;
        assert(is_array($members));
        $actions = [];
        foreach ($members as $member) {
            assert(is_array($member));
            $ident = $member['ident'] ?? null;
            $action = $member['action'] ?? null;
            assert(is_string($ident));
            assert(is_string($action));
            $actions[] = "{$ident}:{$action}";
        }
        return implode(', ', $actions);
    }
}
