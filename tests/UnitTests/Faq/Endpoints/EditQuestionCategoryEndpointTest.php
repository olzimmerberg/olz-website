<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Faq\Endpoints;

use Olz\Faq\Endpoints\EditQuestionCategoryEndpoint;
use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;
use Olz\Tests\Fake\Entity\Faq\FakeQuestionCategory;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Faq\Endpoints\EditQuestionCategoryEndpoint
 */
final class EditQuestionCategoryEndpointTest extends UnitTestCase {
    public function testEditQuestionCategoryEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['faq' => false];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = false;
        $endpoint = new EditQuestionCategoryEndpoint();
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

            $this->assertSame(
                [],
                WithUtilsCache::get('entityUtils')->can_update_olz_entity_calls,
            );

            $this->assertSame(403, $err->getCode());
        }
    }

    public function testEditQuestionCategoryEndpointNoSuchEntity(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['faq' => true];
        $endpoint = new EditQuestionCategoryEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                'id' => FakeOlzRepository::NULL_ID,
            ]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                "INFO Valid user request",
                "NOTICE HTTP error 404",
            ], $this->getLogs());
            $this->assertSame(404, $err->getCode());
        }
    }

    public function testEditQuestionCategoryEndpointNoEntityAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['faq' => false];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = false;
        $endpoint = new EditQuestionCategoryEndpoint();
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

            $this->assertSame(
                [],
                WithUtilsCache::get('entityUtils')->can_update_olz_entity_calls,
            );

            $this->assertSame(403, $err->getCode());
        }
    }

    public function testEditQuestionCategoryEndpointMinimal(): void {
        $id = FakeOlzRepository::MINIMAL_ID;
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['faq' => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new EditQuestionCategoryEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'id' => $id,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());

        $this->assertSame([
            [FakeQuestionCategory::minimal(), null, null, null, null, 'faq'],
        ], WithUtilsCache::get('entityUtils')->can_update_olz_entity_calls);

        $this->assertSame([
            'id' => $id,
            'meta' => [
                'ownerUserId' => null,
                'ownerRoleId' => null,
                'onOff' => true,
            ],
            'data' => [
                'position' => 0,
                'name' => '-',
            ],
        ], $result);
    }

    public function testEditQuestionCategoryEndpointEmpty(): void {
        $id = FakeOlzRepository::EMPTY_ID;
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['faq' => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new EditQuestionCategoryEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'id' => $id,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());

        $this->assertSame([
            [FakeQuestionCategory::empty(), null, null, null, null, 'faq'],
        ], WithUtilsCache::get('entityUtils')->can_update_olz_entity_calls);

        $this->assertSame([
            'id' => $id,
            'meta' => [
                'ownerUserId' => null,
                'ownerRoleId' => null,
                'onOff' => false,
            ],
            'data' => [
                'position' => 0,
                'name' => '-',
            ],
        ], $result);
    }

    public function testEditQuestionCategoryEndpointMaximal(): void {
        $id = FakeOlzRepository::MAXIMAL_ID;
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['faq' => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new EditQuestionCategoryEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'id' => $id,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());

        $this->assertSame([
            [FakeQuestionCategory::maximal(), 'default', 'default', 'role', null, 'faq'],
        ], WithUtilsCache::get('entityUtils')->can_update_olz_entity_calls);

        $this->assertSame([
            'id' => $id,
            'meta' => [
                'ownerUserId' => 1,
                'ownerRoleId' => 1,
                'onOff' => true,
            ],
            'data' => [
                'position' => 3,
                'name' => 'Maximal Category',
            ],
        ], $result);
    }
}
