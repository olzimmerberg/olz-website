<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\News\Endpoints;

use Olz\Entity\Role;
use Olz\Entity\User;
use Olz\News\Endpoints\CreateNewsEndpoint;
use Olz\Tests\Fake;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
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
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => false];
        $endpoint = new CreateNewsEndpoint();
        $endpoint->runtimeSetup();

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
                'custom' => [
                    'recaptchaToken' => null,
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

    public function testCreateNewsEndpointAnonymous(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        WithUtilsCache::get('authUtils')->has_permission_by_query = [
            'any' => false,
            'all' => false,
            'kaderblog' => false,
        ];
        $endpoint = new CreateNewsEndpoint();
        $endpoint->setRecaptchaUtils(new Fake\FakeRecaptchaUtils());
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'meta' => [
                'ownerUserId' => null,
                'ownerRoleId' => null,
                'onOff' => true,
            ],
            'data' => [
                'format' => 'anonymous',
                'authorUserId' => null,
                'authorRoleId' => null,
                'authorName' => 'Anonymous',
                'authorEmail' => 'anonymous@staging.olzimmerberg.ch',
                'title' => 'Test Titel',
                'teaser' => '',
                'content' => 'Sehr viel Inhalt.',
                'externalUrl' => null,
                'tags' => ['test', 'unit'],
                'terminId' => null,
                'imageIds' => [],
                'fileIds' => [],
            ],
            'custom' => [
                'recaptchaToken' => 'valid-token',
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
            'id' => Fake\FakeEntityManager::AUTO_INCREMENT_ID,
        ], $result);
        $this->assertSame(1, count($entity_manager->persisted));
        $this->assertSame(1, count($entity_manager->flushed_persisted));
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
        $news_entry = $entity_manager->persisted[0];
        $this->assertSame(Fake\FakeEntityManager::AUTO_INCREMENT_ID, $news_entry->getId());
        $this->assertSame('Anonymous', $news_entry->getAuthorName());
        $this->assertSame('anonymous@staging.olzimmerberg.ch', $news_entry->getAuthorEmail());
        $this->assertSame(null, $news_entry->getAuthorUser());
        $this->assertSame(null, $news_entry->getAuthorRole());
        $this->assertSame('2020-03-13', $news_entry->getDate()->format('Y-m-d'));
        $this->assertSame('19:30:00', $news_entry->getTime()->format('H:i:s'));
        $this->assertSame('Test Titel', $news_entry->getTitle());
        $this->assertSame('', $news_entry->getTeaser());
        $this->assertSame('Sehr viel Inhalt.', $news_entry->getContent());
        $this->assertSame(null, $news_entry->getExternalUrl());
        $this->assertSame(' test unit ', $news_entry->getTags());
        $this->assertSame(0, $news_entry->getTermin());
        $this->assertSame([], $news_entry->getImageIds());

        $this->assertSame([
            [$news_entry, 1, null, null],
        ], WithUtilsCache::get('entityUtils')->create_olz_entity_calls);

        $id = Fake\FakeEntityManager::AUTO_INCREMENT_ID;

        $this->assertSame([
            [
                [],
                realpath(__DIR__.'/../../../Fake/')."/../UnitTests/tmp/img/news/{$id}/img/",
            ],
            [
                [],
                realpath(__DIR__.'/../../../Fake/')."/../UnitTests/tmp/files/news/{$id}/",
            ],
        ], WithUtilsCache::get('uploadUtils')->move_uploads_calls);

        $expected_text = <<<'ZZZZZZZZZZ'
        Hallo Anonymous,

        Du hast soeben auf [http://fake-base-url](http://fake-base-url) einen [anonymen Forumseintrag](http://fake-base-url/_/news/270) erstellt.

        Falls du deinen Eintrag wieder *löschen* willst, klicke [hier](http://fake-base-url/_/email_reaktion.php?token=eyJhY3Rpb24iOiJkZWxldGVfbmV3cyIsIm5ld3NfaWQiOjI3MH0) oder auf folgenden Link:

        http://fake-base-url/_/email_reaktion.php?token=eyJhY3Rpb24iOiJkZWxldGVfbmV3cyIsIm5ld3NfaWQiOjI3MH0

        ZZZZZZZZZZ;
        $emails_sent = WithUtilsCache::get('emailUtils')->olzMailer->emails_sent;
        $this->assertSame(1, count($emails_sent));
        $this->assertSame('Anonymous', $emails_sent[0]['user']->getFirstName());
        $this->assertSame('', $emails_sent[0]['user']->getLastName());
        $this->assertSame('anonymous@staging.olzimmerberg.ch', $emails_sent[0]['user']->getEmail());
        $this->assertSame(['fake@staging.olzimmerberg.ch', 'OL Zimmerberg'], $emails_sent[0]['from']);
        $this->assertSame(null, $emails_sent[0]['replyTo']);
        $this->assertSame('[OLZ] Dein Forumseintrag', $emails_sent[0]['subject']);
        $this->assertSame($expected_text, $emails_sent[0]['body']);
        $this->assertSame($expected_text, $emails_sent[0]['altBody']);
    }

    public function testCreateNewsEndpoint(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        WithUtilsCache::get('authUtils')->has_permission_by_query = [
            'any' => true,
            'all' => false,
            'kaderblog' => false,
        ];
        $endpoint = new CreateNewsEndpoint();
        $endpoint->runtimeSetup();

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
            'custom' => [
                'recaptchaToken' => null,
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
            'id' => Fake\FakeEntityManager::AUTO_INCREMENT_ID,
        ], $result);
        $this->assertSame(1, count($entity_manager->persisted));
        $this->assertSame(1, count($entity_manager->flushed_persisted));
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
        $news_entry = $entity_manager->persisted[0];
        $this->assertSame(Fake\FakeEntityManager::AUTO_INCREMENT_ID, $news_entry->getId());
        $this->assertSame('t.u.', $news_entry->getAuthorName());
        $this->assertSame('tu@staging.olzimmerberg.ch', $news_entry->getAuthorEmail());
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
        ], WithUtilsCache::get('entityUtils')->create_olz_entity_calls);

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
        ], WithUtilsCache::get('uploadUtils')->move_uploads_calls);

        $this->assertSame([], WithUtilsCache::get('emailUtils')->olzMailer->emails_sent);
    }
}
