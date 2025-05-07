<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Faq\Endpoints;

use Olz\Faq\Endpoints\UpdateQuestionEndpoint;
use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;
use Olz\Tests\Fake\Entity\Faq\FakeQuestion;
use Olz\Tests\Fake\Entity\Faq\FakeQuestionCategory;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Faq\Endpoints\UpdateQuestionEndpoint
 */
final class UpdateQuestionEndpointTest extends UnitTestCase {
    /** @return array<string, mixed> */
    protected function getValidInput(): array {
        return [
            'id' => FakeOlzRepository::MAXIMAL_ID,
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

    public function testUpdateQuestionEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['faq' => false];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = false;
        $endpoint = new UpdateQuestionEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call($this->getValidInput());
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                "INFO Valid user request",
                "NOTICE HTTP error 403",
            ], $this->getLogs());

            $this->assertSame(
                [],
                WithUtilsCache::get('entityUtils')->can_update_olz_entity_calls,
            );

            $this->assertSame(403, $err->getCode());
        }
    }

    public function testUpdateQuestionEndpointNoSuchEntity(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['faq' => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new UpdateQuestionEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                ...$this->getValidInput(),
                'id' => FakeOlzRepository::NULL_ID,
            ]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                "INFO Valid user request",
                "NOTICE HTTP error 404",
            ], $this->getLogs());

            $this->assertSame(
                [],
                WithUtilsCache::get('entityUtils')->can_update_olz_entity_calls,
            );

            $this->assertSame(404, $err->getCode());
        }
    }

    public function testUpdateQuestionEndpointNoEntityAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['faq' => false];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = false;
        $endpoint = new UpdateQuestionEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call($this->getValidInput());
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                "INFO Valid user request",
                "NOTICE HTTP error 403",
            ], $this->getLogs());

            $this->assertSame(
                [],
                WithUtilsCache::get('entityUtils')->can_update_olz_entity_calls,
            );

            $this->assertSame(403, $err->getCode());
        }
    }

    public function testUpdateQuestionEndpoint(): void {
        $id = FakeOlzRepository::MAXIMAL_ID;
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['faq' => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new UpdateQuestionEndpoint();
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
            'id' => $id,
        ], $result);

        $this->assertSame([
            [FakeQuestion::maximal(), 'default', 'default', 'role', ['ownerUserId' => 1, 'ownerRoleId' => 1, 'onOff' => true], 'faq'],
        ], WithUtilsCache::get('entityUtils')->can_update_olz_entity_calls);

        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertCount(1, $entity_manager->persisted);
        $this->assertCount(1, $entity_manager->flushed_persisted);
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
        $entity = $entity_manager->persisted[0];
        $this->assertSame($id, $entity->getId());
        $this->assertSame('test', $entity->getIdent());
        $this->assertSame('Test Question', $entity->getQuestion());
        $this->assertSame(
            FakeQuestionCategory::maximal()->getId(),
            $entity->getCategory()->getId(),
        );
        $this->assertSame(2, $entity->getPositionWithinCategory());
        $this->assertSame('Test Answer', $entity->getAnswer());
        $this->assertSame(1, $entity->getOnOff());

        $this->assertSame([
            [$entity, 1, 1, 1],
        ], WithUtilsCache::get('entityUtils')->update_olz_entity_calls);

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
