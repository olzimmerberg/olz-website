<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\News\Endpoints;

use Olz\Entity\Role;
use Olz\Entity\User;
use Olz\News\Endpoints\CreateNewsEndpoint;
use Olz\Tests\Fake;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\FixedDateUtils;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\News\Endpoints\CreateNewsEndpoint
 */
final class CreateNewsEndpointTest extends UnitTestCase {
    public function testCreateNewsEndpointIdent(): void {
        $endpoint = new CreateNewsEndpoint();
        $this->assertSame('CreateNewsEndpoint', $endpoint->getIdent());
    }

    public function testCreateNewsEndpointNoAccess(): void {
        $auth_utils = new Fake\FakeAuthUtils();
        $auth_utils->has_permission_by_query = ['any' => false];
        $env_utils = new Fake\FakeEnvUtils();
        $logger = Fake\FakeLogger::create();
        $endpoint = new CreateNewsEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setEnvUtils($env_utils);
        $endpoint->setLog($logger);

        try {
            $endpoint->call([
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
                    'authorEmail' => 'tu@test.olzimmerberg.ch',
                    'title' => 'Test Titel',
                    'teaser' => 'Das muss man gelesen haben!',
                    'content' => 'Sehr viel Inhalt.',
                    'externalUrl' => null,
                    'tags' => ['test', 'unit'],
                    'terminId' => null,
                    'imageIds' => [],
                    'fileIds' => [],
                ],
                'custom' => [
                    'recaptchaToken' => null,
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

    public function testCreateNewsEndpoint(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $auth_utils = new Fake\FakeAuthUtils();
        $auth_utils->has_permission_by_query = ['any' => true, 'all' => false];
        $entity_utils = new Fake\FakeEntityUtils();
        $env_utils = new Fake\FakeEnvUtils();
        $upload_utils = new Fake\FakeUploadUtils();
        $logger = Fake\FakeLogger::create();
        $endpoint = new CreateNewsEndpoint();
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
                'authorEmail' => 'tu@test.olzimmerberg.ch',
                'title' => 'Test Titel',
                'teaser' => 'Das muss man gelesen haben!',
                'content' => 'Sehr viel Inhalt.',
                'externalUrl' => null,
                'tags' => ['test', 'unit'],
                'terminId' => null,
                'imageIds' => ['uploaded_image.jpg', 'inexistent.jpg'],
                'fileIds' => ['uploaded_file.pdf', 'inexistent.txt'],
            ],
            'custom' => [
                'recaptchaToken' => null,
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
            'id' => Fake\FakeEntityManager::AUTO_INCREMENT_ID,
        ], $result);
        $this->assertSame(1, count($entity_manager->persisted));
        $this->assertSame(1, count($entity_manager->flushed_persisted));
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
        $news_entry = $entity_manager->persisted[0];
        $this->assertSame(Fake\FakeEntityManager::AUTO_INCREMENT_ID, $news_entry->getId());
        $this->assertSame('t.u.', $news_entry->getAuthorName());
        $this->assertSame('tu@test.olzimmerberg.ch', $news_entry->getAuthorEmail());
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
        $this->assertSame(['uploaded_image.jpg', 'inexistent.jpg'], $news_entry->getImageIds());

        $this->assertSame([
            [$news_entry, 1, 1, 1],
        ], $entity_utils->create_olz_entity_calls);

        $id = Fake\FakeEntityManager::AUTO_INCREMENT_ID;

        $this->assertSame([
            [
                ['uploaded_image.jpg', 'inexistent.jpg'],
                realpath(__DIR__.'/../../../Fake/')."/../UnitTests/tmp/img/news/{$id}/img/",
            ],
            [
                ['uploaded_file.pdf', 'inexistent.txt'],
                realpath(__DIR__.'/../../../Fake/')."/../UnitTests/tmp/files/news/{$id}/",
            ],
        ], $upload_utils->move_uploads_calls);
    }
}
