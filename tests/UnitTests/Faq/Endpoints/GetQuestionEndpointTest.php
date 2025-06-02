<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Faq\Endpoints;

use Olz\Faq\Endpoints\GetQuestionEndpoint;
use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;
use Olz\Tests\Fake\Entity\Faq\FakeQuestionCategory;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Faq\Endpoints\GetQuestionEndpoint
 */
final class GetQuestionEndpointTest extends UnitTestCase {
    public function testGetQuestionEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => false];
        $endpoint = new GetQuestionEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                'id' => FakeOlzRepository::MINIMAL_ID,
            ]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                "INFO Valid user request",
                "NOTICE HTTP error 403",
            ], $this->getLogs());
            $this->assertSame(403, $err->getCode());
        }
    }

    public function testGetQuestionEndpointMinimal(): void {
        $id = FakeOlzRepository::MINIMAL_ID;
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new GetQuestionEndpoint();
        $endpoint->runtimeSetup();

        mkdir(__DIR__.'/../../tmp/files/');
        mkdir(__DIR__.'/../../tmp/files/questions/');
        mkdir(__DIR__."/../../tmp/files/questions/{$id}/");
        mkdir(__DIR__.'/../../tmp/img/');
        mkdir(__DIR__.'/../../tmp/img/questions/');
        mkdir(__DIR__."/../../tmp/img/questions/{$id}/");
        mkdir(__DIR__."/../../tmp/img/questions/{$id}/img/");

        $result = $endpoint->call([
            'id' => $id,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame([
            'id' => $id,
            'meta' => [
                'ownerUserId' => null,
                'ownerRoleId' => null,
                'onOff' => true,
            ],
            'data' => [
                'ident' => 'minimal',
                'question' => '-',
                'categoryId' => null,
                'positionWithinCategory' => 0.0,
                'answer' => '-',
                'imageIds' => [],
                'fileIds' => [],
            ],
        ], $result);
    }

    public function testGetQuestionEndpointEmpty(): void {
        $id = FakeOlzRepository::EMPTY_ID;
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new GetQuestionEndpoint();
        $endpoint->runtimeSetup();

        mkdir(__DIR__.'/../../tmp/files/');
        mkdir(__DIR__.'/../../tmp/files/questions/');
        mkdir(__DIR__."/../../tmp/files/questions/{$id}/");
        mkdir(__DIR__.'/../../tmp/img/');
        mkdir(__DIR__.'/../../tmp/img/questions/');
        mkdir(__DIR__."/../../tmp/img/questions/{$id}/");
        mkdir(__DIR__."/../../tmp/img/questions/{$id}/img/");

        $result = $endpoint->call([
            'id' => $id,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame([
            'id' => $id,
            'meta' => [
                'ownerUserId' => null,
                'ownerRoleId' => null,
                'onOff' => false,
            ],
            'data' => [
                'ident' => 'empty',
                'question' => '-',
                'categoryId' => 123,
                'positionWithinCategory' => 0.0,
                'answer' => '-',
                'imageIds' => [],
                'fileIds' => [],
            ],
        ], $result);
    }

    public function testGetQuestionEndpointMaximal(): void {
        $id = FakeOlzRepository::MAXIMAL_ID;
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new GetQuestionEndpoint();
        $endpoint->runtimeSetup();

        mkdir(__DIR__.'/../../tmp/temp/');
        mkdir(__DIR__.'/../../tmp/files/');
        mkdir(__DIR__.'/../../tmp/files/questions/');
        mkdir(__DIR__."/../../tmp/files/questions/{$id}/");
        file_put_contents(__DIR__."/../../tmp/files/questions/{$id}/file___________________1.pdf", '');
        file_put_contents(__DIR__."/../../tmp/files/questions/{$id}/file___________________2.txt", '');
        mkdir(__DIR__.'/../../tmp/img/');
        mkdir(__DIR__.'/../../tmp/img/questions/');
        mkdir(__DIR__."/../../tmp/img/questions/{$id}/");
        mkdir(__DIR__."/../../tmp/img/questions/{$id}/img");
        file_put_contents(__DIR__."/../../tmp/img/questions/{$id}/img/picture________________A.jpg", '');
        file_put_contents(__DIR__."/../../tmp/img/questions/{$id}/img/picture________________B.jpg", '');

        $result = $endpoint->call([
            'id' => $id,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame([
            'id' => $id,
            'meta' => [
                'ownerUserId' => 1,
                'ownerRoleId' => 1,
                'onOff' => true,
            ],
            'data' => [
                'ident' => 'maximal',
                'question' => 'Maximal Question',
                'categoryId' => FakeQuestionCategory::maximal()->getId(),
                'positionWithinCategory' => 3.0,
                'answer' => 'Maximal Answer',
                'imageIds' => ['picture________________A.jpg', 'picture________________B.jpg'],
                'fileIds' => ['file___________________1.pdf', 'file___________________2.txt'],
            ],
        ], $result);
    }
}
