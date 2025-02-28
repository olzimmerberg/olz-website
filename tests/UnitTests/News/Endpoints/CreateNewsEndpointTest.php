<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\News\Endpoints;

use Olz\Entity\Roles\Role;
use Olz\News\Endpoints\CreateNewsEndpoint;
use Olz\Tests\Fake\Entity\Roles\FakeRole;
use Olz\Tests\Fake\Entity\Users\FakeUser;
use Olz\Tests\Fake\FakeEntityManager;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

/**
 * @internal
 *
 * @covers \Olz\News\Endpoints\CreateNewsEndpoint
 */
final class CreateNewsEndpointTest extends UnitTestCase {
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
                    'publishAt' => '2020-03-13 19:30:00',
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
        $mailer = $this->createMock(MailerInterface::class);
        $entity_manager = WithUtilsCache::get('entityManager');
        WithUtilsCache::get('authUtils')->has_permission_by_query = [
            'any' => false,
            'all' => false,
            'kaderblog' => false,
        ];
        $endpoint = new CreateNewsEndpoint();
        WithUtilsCache::get('emailUtils')->setMailer($mailer);
        $endpoint->runtimeSetup();
        $artifacts = [];
        $mailer->expects($this->exactly(1))->method('send')->with(
            $this->callback(function (Email $email) use (&$artifacts) {
                $artifacts['email'] = [...($artifacts['email'] ?? []), $email];
                return true;
            }),
            null,
        );

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
                'publishAt' => '2020-03-16 09:00:00',
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
            "INFO Forumseintrag email sent to anonymous@staging.olzimmerberg.ch.",
            "INFO Valid user response",
        ], $this->getLogs());

        $this->assertEquals([
            'custom' => ['status' => 'OK'],
            'id' => FakeEntityManager::AUTO_INCREMENT_ID,
        ], $result);
        $this->assertCount(1, $entity_manager->persisted);
        $this->assertCount(1, $entity_manager->flushed_persisted);
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
        $news_entry = $entity_manager->persisted[0];
        $this->assertSame(FakeEntityManager::AUTO_INCREMENT_ID, $news_entry->getId());
        $this->assertSame('Anonymous', $news_entry->getAuthorName());
        $this->assertSame('anonymous@staging.olzimmerberg.ch', $news_entry->getAuthorEmail());
        $this->assertNull($news_entry->getAuthorUser());
        $this->assertNull($news_entry->getAuthorRole());
        $this->assertSame('2020-03-16', $news_entry->getPublishedDate()->format('Y-m-d'));
        $this->assertSame('09:00:00', $news_entry->getPublishedTime()->format('H:i:s'));
        $this->assertSame('Test Titel', $news_entry->getTitle());
        $this->assertSame('', $news_entry->getTeaser());
        $this->assertSame('Sehr viel Inhalt.', $news_entry->getContent());
        $this->assertNull($news_entry->getExternalUrl());
        $this->assertSame(' test unit ', $news_entry->getTags());
        $this->assertSame(0, $news_entry->getTermin());
        $this->assertSame([], $news_entry->getImageIds());

        $this->assertSame([
            [$news_entry, 1, null, null],
        ], WithUtilsCache::get('entityUtils')->create_olz_entity_calls);

        $id = FakeEntityManager::AUTO_INCREMENT_ID;

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
        $this->assertSame([
            [
                [],
                realpath(__DIR__.'/../../../')."/Fake/../UnitTests/tmp/img/news/{$id}/",
            ],
        ], WithUtilsCache::get('imageUtils')->generatedThumbnails);

        $expected_text = <<<'ZZZZZZZZZZ'
            Hallo Anonymous,

            Du hast soeben auf [http://fake-base-url](http://fake-base-url) einen [anonymen Forumseintrag](http://fake-base-url/_/news/270) erstellt.

            Falls du deinen Eintrag wieder *löschen* willst, klicke [hier](http://fake-base-url/_/email_reaktion?token=eyJhY3Rpb24iOiJkZWxldGVfbmV3cyIsIm5ld3NfaWQiOjI3MH0) oder auf folgenden Link:

            http://fake-base-url/_/email_reaktion?token=eyJhY3Rpb24iOiJkZWxldGVfbmV3cyIsIm5ld3NfaWQiOjI3MH0

            ZZZZZZZZZZ;
        $this->assertSame([
            <<<ZZZZZZZZZZ
                From: 
                Reply-To: 
                To: "Anonymous" <anonymous@staging.olzimmerberg.ch>
                Cc: 
                Bcc: 
                Subject: [OLZ] Dein Forumseintrag

                {$expected_text}


                <div style="text-align: right; float: right;">
                    <img src="cid:olz_logo" alt="" style="width:150px;" />
                </div>
                <br /><br /><br />
                {$expected_text}


                olz_logo
                ZZZZZZZZZZ,
        ], array_map(function ($email) {
            return $this->emailUtils()->getComparableEmail($email);
        }, $artifacts['email']));
    }

    public function testCreateNewsEndpointMaximal(): void {
        $mailer = $this->createMock(MailerInterface::class);
        $entity_manager = WithUtilsCache::get('entityManager');
        WithUtilsCache::get('authUtils')->has_permission_by_query = [
            'any' => true,
            'all' => false,
            'kaderblog' => false,
        ];
        WithUtilsCache::get('authUtils')->authenticated_roles = [new Role()];
        $endpoint = new CreateNewsEndpoint();
        $endpoint->setMailer($mailer);
        $endpoint->runtimeSetup();
        $mailer->expects($this->exactly(0))->method('send');

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
                'publishAt' => '2020-03-16 09:00:00',
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

        $this->assertEquals([
            'custom' => ['status' => 'OK'],
            'id' => FakeEntityManager::AUTO_INCREMENT_ID,
        ], $result);
        $this->assertCount(1, $entity_manager->persisted);
        $this->assertCount(1, $entity_manager->flushed_persisted);
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
        $news_entry = $entity_manager->persisted[0];
        $this->assertSame(FakeEntityManager::AUTO_INCREMENT_ID, $news_entry->getId());
        $this->assertSame('t.u.', $news_entry->getAuthorName());
        $this->assertSame('tu@staging.olzimmerberg.ch', $news_entry->getAuthorEmail());
        $this->assertSame(FakeUser::adminUser(), $news_entry->getAuthorUser());
        $this->assertSame(FakeRole::adminRole(), $news_entry->getAuthorRole());
        $this->assertSame('2020-03-16', $news_entry->getPublishedDate()->format('Y-m-d'));
        $this->assertSame('09:00:00', $news_entry->getPublishedTime()->format('H:i:s'));
        $this->assertSame('Test Titel', $news_entry->getTitle());
        $this->assertSame('Das muss man gelesen haben!', $news_entry->getTeaser());
        $this->assertSame('Sehr viel Inhalt.', $news_entry->getContent());
        $this->assertNull($news_entry->getExternalUrl());
        $this->assertSame(' test unit ', $news_entry->getTags());
        $this->assertSame(0, $news_entry->getTermin());
        $this->assertSame(['uploaded_image.jpg', 'inexistent.jpg'], $news_entry->getImageIds());

        $this->assertSame([
            [$news_entry, 1, 1, 1],
        ], WithUtilsCache::get('entityUtils')->create_olz_entity_calls);

        $id = FakeEntityManager::AUTO_INCREMENT_ID;

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
        $this->assertSame([
            [
                ['uploaded_image.jpg', 'inexistent.jpg'],
                realpath(__DIR__.'/../../../')."/Fake/../UnitTests/tmp/img/news/{$id}/",
            ],
        ], WithUtilsCache::get('imageUtils')->generatedThumbnails);
    }

    public function testCreateNewsEndpointMinimal(): void {
        $mailer = $this->createMock(MailerInterface::class);
        $entity_manager = WithUtilsCache::get('entityManager');
        WithUtilsCache::get('authUtils')->has_permission_by_query = [
            'any' => true,
            'all' => false,
            'kaderblog' => false,
        ];
        $endpoint = new CreateNewsEndpoint();
        $endpoint->setMailer($mailer);
        $endpoint->runtimeSetup();
        $mailer->expects($this->exactly(0))->method('send');

        $result = $endpoint->call([
            'meta' => [
                'ownerUserId' => 1,
                'ownerRoleId' => 1,
                'onOff' => true,
            ],
            'data' => [
                'format' => 'forum',
                'authorUserId' => null,
                'authorRoleId' => null,
                'authorName' => null,
                'authorEmail' => null,
                'publishAt' => null,
                'title' => 'Cannot be empty',
                'teaser' => '',
                'content' => '',
                'externalUrl' => null,
                'tags' => [],
                'terminId' => null,
                'imageIds' => [],
                'fileIds' => [],
            ],
            'custom' => [
                'recaptchaToken' => null,
            ],
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());

        $this->assertEquals([
            'custom' => ['status' => 'OK'],
            'id' => FakeEntityManager::AUTO_INCREMENT_ID,
        ], $result);
        $this->assertCount(1, $entity_manager->persisted);
        $this->assertCount(1, $entity_manager->flushed_persisted);
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
        $news_entry = $entity_manager->persisted[0];
        $this->assertSame(FakeEntityManager::AUTO_INCREMENT_ID, $news_entry->getId());
        $this->assertNull($news_entry->getAuthorName());
        $this->assertNull($news_entry->getAuthorEmail());
        $this->assertNull($news_entry->getAuthorUser());
        $this->assertNull($news_entry->getAuthorRole());
        $this->assertSame('2020-03-13', $news_entry->getPublishedDate()->format('Y-m-d'));
        $this->assertSame('19:30:00', $news_entry->getPublishedTime()->format('H:i:s'));
        $this->assertSame('Cannot be empty', $news_entry->getTitle());
        $this->assertSame('', $news_entry->getTeaser());
        $this->assertSame('', $news_entry->getContent());
        $this->assertNull($news_entry->getExternalUrl());
        $this->assertSame('  ', $news_entry->getTags());
        $this->assertSame(0, $news_entry->getTermin());
        $this->assertSame([], $news_entry->getImageIds());

        $this->assertSame([
            [$news_entry, 1, 1, 1],
        ], WithUtilsCache::get('entityUtils')->create_olz_entity_calls);

        $id = FakeEntityManager::AUTO_INCREMENT_ID;

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
        $this->assertSame([
            [
                [],
                realpath(__DIR__.'/../../../')."/Fake/../UnitTests/tmp/img/news/{$id}/",
            ],
        ], WithUtilsCache::get('imageUtils')->generatedThumbnails);
    }
}
