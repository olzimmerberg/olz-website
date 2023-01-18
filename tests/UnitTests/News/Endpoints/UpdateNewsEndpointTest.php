<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\News\Endpoints;

use Olz\Entity\News\NewsEntry;
use Olz\Entity\Role;
use Olz\Entity\User;
use Olz\News\Endpoints\UpdateNewsEndpoint;
use Olz\Tests\Fake;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\FixedDateUtils;
use PhpTypeScriptApi\HttpError;

class FakeUpdateNewsEndpointNewsRepository {
    public function findOneBy($where) {
        if ($where === ['id' => 123]) {
            $entry = new NewsEntry();
            $entry->setId(123);
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
        $auth_utils = new Fake\FakeAuthUtils();
        $auth_utils->has_permission_by_query = ['any' => false];
        $env_utils = new Fake\FakeEnvUtils();
        $logger = Fake\FakeLogger::create();
        $endpoint = new UpdateNewsEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setEnvUtils($env_utils);
        $endpoint->setLog($logger);

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
                    'author' => 't.u.',
                    'authorUserId' => 2,
                    'authorRoleId' => 2,
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
            ], $logger->handler->getPrettyRecords());
            $this->assertSame(403, $err->getCode());
        }
    }

    public function testUpdateNewsEndpointNoSuchEntity(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $news_repo = new FakeUpdateNewsEndpointNewsRepository();
        $entity_manager->repositories[NewsEntry::class] = $news_repo;
        $auth_utils = new Fake\FakeAuthUtils();
        $auth_utils->has_permission_by_query = ['any' => true, 'all' => false];
        $entity_utils = new Fake\FakeEntityUtils();
        $entity_utils->can_update_olz_entity = true;
        $env_utils = new Fake\FakeEnvUtils();
        $logger = Fake\FakeLogger::create();
        $endpoint = new UpdateNewsEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setEntityUtils($entity_utils);
        $endpoint->setEnvUtils($env_utils);
        $endpoint->setLog($logger);

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
                    'author' => 't.u.',
                    'authorUserId' => 2,
                    'authorRoleId' => 2,
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
            ], $logger->handler->getPrettyRecords());
            $this->assertSame(404, $err->getCode());
        }
    }

    public function testUpdateNewsEndpointNoEntityAccess(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $news_repo = new FakeUpdateNewsEndpointNewsRepository();
        $entity_manager->repositories[NewsEntry::class] = $news_repo;
        $auth_utils = new Fake\FakeAuthUtils();
        $auth_utils->has_permission_by_query = ['any' => true, 'all' => false];
        $entity_utils = new Fake\FakeEntityUtils();
        $entity_utils->can_update_olz_entity = false;
        $env_utils = new Fake\FakeEnvUtils();
        $logger = Fake\FakeLogger::create();
        $endpoint = new UpdateNewsEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setEntityUtils($entity_utils);
        $endpoint->setEnvUtils($env_utils);
        $endpoint->setLog($logger);

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
                    'author' => 't.u.',
                    'authorUserId' => 2,
                    'authorRoleId' => 2,
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
            ], $logger->handler->getPrettyRecords());
            $this->assertSame(403, $err->getCode());
        }
    }

    public function testUpdateNewsEndpoint(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $news_repo = new FakeUpdateNewsEndpointNewsRepository();
        $entity_manager->repositories[NewsEntry::class] = $news_repo;
        $auth_utils = new Fake\FakeAuthUtils();
        $auth_utils->has_permission_by_query = ['any' => true, 'all' => false];
        $entity_utils = new Fake\FakeEntityUtils();
        $entity_utils->can_update_olz_entity = true;
        $env_utils = new Fake\FakeEnvUtils();
        $upload_utils = new Fake\FakeUploadUtils();
        $logger = Fake\FakeLogger::create();
        $endpoint = new UpdateNewsEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setDateUtils(new FixedDateUtils('2020-03-13 19:30:00'));
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setEntityUtils($entity_utils);
        $endpoint->setEnvUtils($env_utils);
        $endpoint->setUploadUtils($upload_utils);
        $endpoint->setLog($logger);

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
                'author' => 't.u.',
                'authorUserId' => 2,
                'authorRoleId' => 2,
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
        ], $logger->handler->getPrettyRecords());

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
        $this->assertSame('t.u.', $news_entry->getAuthor());
        $this->assertSame($user_repo->admin_user, $news_entry->getAuthorUser());
        $this->assertSame($role_repo->admin_role, $news_entry->getAuthorRole());
        $this->assertSame('2020-03-13', $news_entry->getDate()->format('Y-m-d'));
        $this->assertSame('19:30:00', $news_entry->getTime()->format('H:i:s'));
        $this->assertSame('Test Titel', $news_entry->getTitle());
        $this->assertSame('Das muss man gelesen haben!', $news_entry->getTeaser());
        $this->assertSame('Sehr viel Inhalt.', $news_entry->getContent());
        $this->assertSame(null, $news_entry->getExternalUrl());
        $this->assertSame(' test unit ', $news_entry->getTags());
        $this->assertSame(0, $news_entry->getTermin());

        $this->assertSame([
            [$news_entry, 1, 1, 1],
        ], $entity_utils->update_olz_entity_calls);

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
        ], $upload_utils->move_uploads_calls);
    }
}
