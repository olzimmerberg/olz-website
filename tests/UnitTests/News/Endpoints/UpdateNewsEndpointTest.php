<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\News\Endpoints;

use Olz\Entity\News\NewsEntry;
use Olz\Entity\Role;
use Olz\Entity\User;
use Olz\News\Endpoints\UpdateNewsEndpoint;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

class FakeUpdateNewsEndpointNewsRepository {
    public function findOneBy($where) {
        if ($where === ['id' => 123]) {
            $entry = new NewsEntry();
            $entry->setId(123);
            $datetime = new \DateTime('2020-03-13 19:30:00');
            $entry->setPublishedDate($datetime);
            $entry->setPublishedTime($datetime);
            return $entry;
        }
        if ($where === ['id' => 9999]) {
            return null;
        }
        $where_json = json_encode($where);
        throw new \Exception("Query not mocked in findOneBy: {$where_json}", 1);
    }
}

/**
 * @internal
 *
 * @covers \Olz\News\Endpoints\UpdateNewsEndpoint
 */
final class UpdateNewsEndpointTest extends UnitTestCase {
    public function testUpdateNewsEndpointIdent(): void {
        $endpoint = new UpdateNewsEndpoint();
        $this->assertSame('UpdateNewsEndpoint', $endpoint->getIdent());
    }

    public function testUpdateNewsEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => false];
        $endpoint = new UpdateNewsEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                'id' => 123,
                'meta' => [
                    'ownerUserId' => 1,
                    'ownerRoleId' => 1,
                    'onOff' => true,
                ],
                'data' => [
                    'format' => 'aktuell',
                    'authorUserId' => 2,
                    'authorRoleId' => 2,
                    'authorName' => 't.u.',
                    'authorEmail' => 'tu@staging.olzimmerberg.ch',
                    'title' => 'Test Titel',
                    'teaser' => 'Das muss man gelesen haben!',
                    'content' => 'Sehr viel Inhalt.',
                    'externalUrl' => null,
                    'tags' => ['test', 'unit'],
                    'terminId' => null,
                    'imageIds' => [],
                    'fileIds' => [],
                ],
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

    public function testUpdateNewsEndpointNoSuchEntity(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $news_repo = new FakeUpdateNewsEndpointNewsRepository();
        $entity_manager->repositories[NewsEntry::class] = $news_repo;
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true, 'all' => false];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new UpdateNewsEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                'id' => 9999,
                'meta' => [
                    'ownerUserId' => 1,
                    'ownerRoleId' => 1,
                    'onOff' => true,
                ],
                'data' => [
                    'format' => 'aktuell',
                    'authorUserId' => 2,
                    'authorRoleId' => 2,
                    'authorName' => 't.u.',
                    'authorEmail' => 'tu@staging.olzimmerberg.ch',
                    'title' => 'Test Titel',
                    'teaser' => 'Das muss man gelesen haben!',
                    'content' => 'Sehr viel Inhalt.',
                    'externalUrl' => null,
                    'tags' => ['test', 'unit'],
                    'terminId' => null,
                    'imageIds' => [],
                    'fileIds' => [],
                ],
            ]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                "INFO Valid user request",
                "WARNING HTTP error 404",
            ], $this->getLogs());
            $this->assertSame(404, $err->getCode());
        }
    }

    public function testUpdateNewsEndpointNoEntityAccess(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $news_repo = new FakeUpdateNewsEndpointNewsRepository();
        $entity_manager->repositories[NewsEntry::class] = $news_repo;
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true, 'all' => false];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = false;
        $endpoint = new UpdateNewsEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                'id' => 123,
                'meta' => [
                    'ownerUserId' => 1,
                    'ownerRoleId' => 1,
                    'onOff' => true,
                ],
                'data' => [
                    'format' => 'aktuell',
                    'authorUserId' => 2,
                    'authorRoleId' => 2,
                    'authorName' => 't.u.',
                    'authorEmail' => 'tu@staging.olzimmerberg.ch',
                    'title' => 'Test Titel',
                    'teaser' => 'Das muss man gelesen haben!',
                    'content' => 'Sehr viel Inhalt.',
                    'externalUrl' => null,
                    'tags' => ['test', 'unit'],
                    'terminId' => null,
                    'imageIds' => [],
                    'fileIds' => [],
                ],
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

    public function testUpdateNewsEndpoint(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $news_repo = new FakeUpdateNewsEndpointNewsRepository();
        $entity_manager->repositories[NewsEntry::class] = $news_repo;
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true, 'all' => false];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new UpdateNewsEndpoint();
        $endpoint->runtimeSetup();

        mkdir(__DIR__.'/../../tmp/temp/');
        file_put_contents(__DIR__.'/../../tmp/temp/uploaded_image.jpg', '');
        file_put_contents(__DIR__.'/../../tmp/temp/uploaded_file.pdf', '');
        mkdir(__DIR__.'/../../tmp/img/');
        mkdir(__DIR__.'/../../tmp/img/news/');
        mkdir(__DIR__.'/../../tmp/files/');
        mkdir(__DIR__.'/../../tmp/files/news/');

        $result = $endpoint->call([
            'id' => 123,
            'meta' => [
                'ownerUserId' => 1,
                'ownerRoleId' => 1,
                'onOff' => true,
            ],
            'data' => [
                'format' => 'aktuell',
                'authorUserId' => 2,
                'authorRoleId' => 2,
                'authorName' => 't.u.',
                'authorEmail' => 'tu@staging.olzimmerberg.ch',
                'title' => 'Test Titel',
                'teaser' => 'Das muss man gelesen haben!',
                'content' => 'Sehr viel Inhalt.',
                'externalUrl' => null,
                'tags' => ['test', 'unit'],
                'terminId' => null,
                'imageIds' => ['uploaded_image.jpg', 'inexistent.jpg'],
                'fileIds' => ['uploaded_file.pdf', 'inexistent.txt'],
            ],
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());

        $user_repo = $entity_manager->repositories[User::class];
        $role_repo = $entity_manager->repositories[Role::class];
        $this->assertSame([
            'status' => 'OK',
            'id' => 123,
        ], $result);
        $this->assertSame(1, count($entity_manager->persisted));
        $this->assertSame(1, count($entity_manager->flushed_persisted));
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
        $news_entry = $entity_manager->persisted[0];
        $this->assertSame(123, $news_entry->getId());
        $this->assertSame('t.u.', $news_entry->getAuthorName());
        $this->assertSame('tu@staging.olzimmerberg.ch', $news_entry->getAuthorEmail());
        $this->assertSame($user_repo->admin_user, $news_entry->getAuthorUser());
        $this->assertSame($role_repo->admin_role, $news_entry->getAuthorRole());
        $this->assertSame('2020-03-13', $news_entry->getPublishedDate()->format('Y-m-d'));
        $this->assertSame('19:30:00', $news_entry->getPublishedTime()->format('H:i:s'));
        $this->assertSame('Test Titel', $news_entry->getTitle());
        $this->assertSame('Das muss man gelesen haben!', $news_entry->getTeaser());
        $this->assertSame('Sehr viel Inhalt.', $news_entry->getContent());
        $this->assertSame(null, $news_entry->getExternalUrl());
        $this->assertSame(' test unit ', $news_entry->getTags());
        $this->assertSame(0, $news_entry->getTermin());

        $this->assertSame([
            [$news_entry, 1, 1, 1],
        ], WithUtilsCache::get('entityUtils')->update_olz_entity_calls);

        $id = 123;

        $this->assertSame([
            [
                ['uploaded_image.jpg', 'inexistent.jpg'],
                realpath(__DIR__.'/../../../')."/Fake/../UnitTests/tmp/img/news/{$id}/img/",
            ],
            [
                ['uploaded_file.pdf', 'inexistent.txt'],
                realpath(__DIR__.'/../../../')."/Fake/../UnitTests/tmp/files/news/{$id}/",
            ],
        ], WithUtilsCache::get('uploadUtils')->move_uploads_calls);
    }
}
