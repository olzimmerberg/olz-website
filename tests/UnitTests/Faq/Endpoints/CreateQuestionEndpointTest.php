<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Faq\Endpoints;

use Olz\Faq\Endpoints\CreateQuestionEndpoint;
use Olz\Tests\Fake\Entity\Faq\FakeQuestionCategory;
use Olz\Tests\Fake\Entity\Users\FakeUser;
use Olz\Tests\Fake\FakeEntityManager;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Faq\Endpoints\CreateQuestionEndpoint
 */
final class CreateQuestionEndpointTest extends UnitTestCase {
    /** @return array<string, mixed> */
    protected function getValidInput(): array {
        return [
            'meta' => [
                'ownerUserId' => 1,
                'ownerRoleId' => 1,
                'onOff' => true,
            ],
            'data' => [
                'ident' => 'test',
                'question' => 'Test Question',
                'categoryId' => FakeQuestionCategory::maximal()->getId(),
                'positionWithinCategory' => 2,
                'answer' => 'Test Answer',
                'imageIds' => ['uploaded_imageA.jpg', 'uploaded_imageB.jpg'],
                'fileIds' => ['uploaded_file1.pdf', 'uploaded_file2.txt'],
            ],
        ];
    }

    public function testCreateQuestionEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['faq' => false];
        $endpoint = new CreateQuestionEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call($this->getValidInput());
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                "INFO Valid user request",
                "WARNING HTTP error 403",
            ], $this->getLogs());
            $this->assertSame(403, $err->getCode());
        }
    }

    public function testCreateQuestionEndpoint(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['faq' => true];
        WithUtilsCache::get('authUtils')->current_user = FakeUser::defaultUser();
        $endpoint = new CreateQuestionEndpoint();
        $endpoint->runtimeSetup();

        mkdir(__DIR__.'/../../tmp/temp/');
        file_put_contents(__DIR__.'/../../tmp/temp/uploaded_imageA.jpg', '');
        file_put_contents(__DIR__.'/../../tmp/temp/uploaded_imageB.jpg', '');
        file_put_contents(__DIR__.'/../../tmp/temp/uploaded_file1.pdf', '');
        file_put_contents(__DIR__.'/../../tmp/temp/uploaded_file2.txt', '');
        mkdir(__DIR__.'/../../tmp/files/');
        mkdir(__DIR__.'/../../tmp/files/questions/');
        mkdir(__DIR__.'/../../tmp/img/');
        mkdir(__DIR__.'/../../tmp/img/questions/');

        $result = $endpoint->call($this->getValidInput());

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());

        $this->assertSame([
            'id' => FakeEntityManager::AUTO_INCREMENT_ID,
        ], $result);
        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertCount(1, $entity_manager->persisted);
        $this->assertCount(1, $entity_manager->flushed_persisted);
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
        $entity = $entity_manager->persisted[0];
        $this->assertSame(FakeEntityManager::AUTO_INCREMENT_ID, $entity->getId());
        $this->assertSame('test', $entity->getIdent());
        $this->assertSame('Test Question', $entity->getQuestion());
        $this->assertSame(
            FakeQuestionCategory::maximal()->getId(),
            $entity->getCategory()->getId(),
        );
        $this->assertSame(2, $entity->getPositionWithinCategory());
        $this->assertSame('Test Answer', $entity->getAnswer());

        $this->assertSame([
            [$entity, 1, 1, 1],
        ], WithUtilsCache::get('entityUtils')->create_olz_entity_calls);

        $id = FakeEntityManager::AUTO_INCREMENT_ID;

        $this->assertSame([
            [
                ['uploaded_imageA.jpg', 'uploaded_imageB.jpg'],
                realpath(__DIR__.'/../../../Fake/')."/../UnitTests/tmp/img/questions/{$id}/img/",
            ],
            [
                ['uploaded_file1.pdf', 'uploaded_file2.txt'],
                realpath(__DIR__.'/../../../Fake/')."/../UnitTests/tmp/files/questions/{$id}/",
            ],
        ], WithUtilsCache::get('uploadUtils')->move_uploads_calls);
        $this->assertSame([
            [
                ['uploaded_imageA.jpg', 'uploaded_imageB.jpg'],
                realpath(__DIR__.'/../../../')."/Fake/../UnitTests/tmp/img/questions/{$id}/",
            ],
        ], WithUtilsCache::get('imageUtils')->generatedThumbnails);
    }
}
