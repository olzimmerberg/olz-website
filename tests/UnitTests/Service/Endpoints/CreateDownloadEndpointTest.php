<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Service\Endpoints;

use Olz\Service\Endpoints\CreateDownloadEndpoint;
use Olz\Tests\Fake;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;
use Symfony\Component\Mailer\MailerInterface;

/**
 * @internal
 *
 * @covers \Olz\Service\Endpoints\CreateDownloadEndpoint
 */
final class CreateDownloadEndpointTest extends UnitTestCase {
    public function testCreateDownloadEndpointIdent(): void {
        $endpoint = new CreateDownloadEndpoint();
        $this->assertSame('CreateDownloadEndpoint', $endpoint->getIdent());
    }

    public function testCreateDownloadEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => false];
        $endpoint = new CreateDownloadEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                'meta' => [
                    'ownerUserId' => 1,
                    'ownerRoleId' => 1,
                    'onOff' => true,
                ],
                'data' => [
                    'name' => 'Test Download',
                    'position' => 3,
                    'fileId' => null,
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

    public function testCreateDownloadEndpoint(): void {
        $mailer = $this->createStub(MailerInterface::class);
        $entity_manager = WithUtilsCache::get('entityManager');
        WithUtilsCache::get('authUtils')->has_permission_by_query = [
            'any' => true,
            'all' => false,
            'kaderblog' => false,
        ];
        $endpoint = new CreateDownloadEndpoint();
        $endpoint->setMailer($mailer);
        $endpoint->runtimeSetup();
        $mailer->expects($this->exactly(0))->method('send');

        mkdir(__DIR__.'/../../tmp/temp/');
        file_put_contents(__DIR__.'/../../tmp/temp/uploaded_file.pdf', '');
        mkdir(__DIR__.'/../../tmp/files/');
        mkdir(__DIR__.'/../../tmp/files/downloads/');

        $result = $endpoint->call([
            'meta' => [
                'ownerUserId' => 1,
                'ownerRoleId' => 1,
                'onOff' => true,
            ],
            'data' => [
                'name' => 'Test Download',
                'position' => 3,
                'fileId' => 'uploaded_file.pdf',
            ],
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());

        $this->assertSame([
            'status' => 'OK',
            'id' => Fake\FakeEntityManager::AUTO_INCREMENT_ID,
        ], $result);
        $this->assertSame(1, count($entity_manager->persisted));
        $this->assertSame(1, count($entity_manager->flushed_persisted));
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
        $download = $entity_manager->persisted[0];
        $this->assertSame(Fake\FakeEntityManager::AUTO_INCREMENT_ID, $download->getId());
        $this->assertSame('Test Download', $download->getName());
        $this->assertSame(3, $download->getPosition());
        $this->assertSame('uploaded_file.pdf', $download->getFileId());

        $this->assertSame([
            [$download, 1, 1, 1],
        ], WithUtilsCache::get('entityUtils')->create_olz_entity_calls);

        $id = Fake\FakeEntityManager::AUTO_INCREMENT_ID;

        $this->assertSame([
            [
                ['uploaded_file.pdf'],
                realpath(__DIR__.'/../../../Fake/')."/../UnitTests/tmp/files/downloads/{$id}/",
            ],
        ], WithUtilsCache::get('uploadUtils')->move_uploads_calls);
    }
}
