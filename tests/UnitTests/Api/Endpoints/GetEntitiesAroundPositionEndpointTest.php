<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Api\Endpoints;

use Olz\Api\Endpoints\GetEntitiesAroundPositionEndpoint;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Api\Endpoints\GetEntitiesAroundPositionEndpoint
 */
final class GetEntitiesAroundPositionEndpointTest extends UnitTestCase {
    public function testGetEntitiesAroundPositionEndpointWithoutInput(): void {
        $endpoint = new GetEntitiesAroundPositionEndpoint();
        $endpoint->runtimeSetup();
        try {
            $endpoint->call([]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame([
                'entityType' => ["Fehlender Schlüssel: entityType."],
                'entityField' => ["Fehlender Schlüssel: entityField."],
                // @phpstan-ignore-next-line
            ], $httperr->getPrevious()->getValidationErrors());
            $this->assertSame([
                "NOTICE Bad user request",
            ], $this->getLogs());
        }
    }

    public function testGetEntitiesAroundPositionEndpointWithNullInput(): void {
        $endpoint = new GetEntitiesAroundPositionEndpoint();
        $endpoint->runtimeSetup();
        try {
            $endpoint->call([
                'entityType' => null,
                'entityField' => null,
                'id' => null,
            ]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame([
                'entityType' => [['.' => [
                    ['.' => ["Wert muss vom Typ 'Download' sein."]],
                    ['.' => ["Wert muss vom Typ 'Link' sein."]],
                    ['.' => ["Wert muss vom Typ 'Question' sein."]],
                    ['.' => ["Wert muss vom Typ 'QuestionCategory' sein."]],
                    ['.' => ["Wert muss vom Typ 'SolvEvent' sein."]],
                    ['.' => ["Wert muss vom Typ 'TerminLabel' sein."]],
                    ['.' => ["Wert muss vom Typ 'TerminLocation' sein."]],
                    ['.' => ["Wert muss vom Typ 'TerminTemplate' sein."]],
                    ['.' => ["Wert muss vom Typ 'Role' sein."]],
                    ['.' => ["Wert muss vom Typ 'User' sein."]],
                ]]],
                'entityField' => [['.' => ['Wert muss vom Typ non-empty-string sein.']]],
                // @phpstan-ignore-next-line
            ], $httperr->getPrevious()->getValidationErrors());
            $this->assertSame([
                "NOTICE Bad user request",
            ], $this->getLogs());
        }
    }

    public function testGetEntitiesAroundPositionEndpointWithInvalidEntityType(): void {
        $endpoint = new GetEntitiesAroundPositionEndpoint();
        $endpoint->runtimeSetup();
        try {
            $endpoint->call([
                'entityType' => 'invalid',
                'entityField' => null,
                'id' => null,
            ]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame([
                'entityType' => [['.' => [
                    ['.' => ["Wert muss vom Typ 'Download' sein."]],
                    ['.' => ["Wert muss vom Typ 'Link' sein."]],
                    ['.' => ["Wert muss vom Typ 'Question' sein."]],
                    ['.' => ["Wert muss vom Typ 'QuestionCategory' sein."]],
                    ['.' => ["Wert muss vom Typ 'SolvEvent' sein."]],
                    ['.' => ["Wert muss vom Typ 'TerminLabel' sein."]],
                    ['.' => ["Wert muss vom Typ 'TerminLocation' sein."]],
                    ['.' => ["Wert muss vom Typ 'TerminTemplate' sein."]],
                    ['.' => ["Wert muss vom Typ 'Role' sein."]],
                    ['.' => ["Wert muss vom Typ 'User' sein."]],
                ]]],
                'entityField' => [['.' => ['Wert muss vom Typ non-empty-string sein.']]],
                // @phpstan-ignore-next-line
            ], $httperr->getPrevious()->getValidationErrors());
            $this->assertSame([
                "NOTICE Bad user request",
            ], $this->getLogs());
        }
    }

    public function testGetEntitiesAroundPositionEndpointWithInvalidEntityField(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new GetEntitiesAroundPositionEndpoint();
        $endpoint->runtimeSetup();
        try {
            $endpoint->call([
                'entityType' => 'Role',
                'entityField' => 'position',
                'id' => 1234,
            ]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame(400, $httperr->getCode());
            $this->assertSame([
                "INFO Valid user request",
                "NOTICE HTTP error 400",
            ], $this->getLogs());
        }
    }

    public function testGetEntitiesAroundPositionEndpointWithNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => false];
        $endpoint = new GetEntitiesAroundPositionEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                'entityType' => 'QuestionCategory',
                'entityField' => 'position',
                'id' => 1,
            ]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame(403, $httperr->getCode());
            $this->assertSame([
                "INFO Valid user request",
                "NOTICE HTTP error 403",
            ], $this->getLogs());
        }
    }

    public function testGetEntitiesAroundPositionEndpointWithoutIdAndPosition(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new GetEntitiesAroundPositionEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'entityType' => 'QuestionCategory',
            'entityField' => 'position',
            'id' => null,
            'position' => null,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame([
            'before' => null,
            'this' => null,
            'after' => null,
        ], $result);
    }

    public function testGetEntitiesAroundPositionEndpointWithValidId(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new GetEntitiesAroundPositionEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'entityType' => 'QuestionCategory',
            'entityField' => 'position',
            'id' => 12,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame([
            'before' => null,
            'this' => [
                'id' => 12,
                'position' => 0.0,
                'title' => '-',
            ],
            'after' => [
                'id' => 1234,
                'position' => 3.0,
                'title' => 'Maximal Category',
            ],
        ], $result);
    }

    public function testGetEntitiesAroundPositionEndpointWithSoftDeletedId(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new GetEntitiesAroundPositionEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'entityType' => 'QuestionCategory',
            'entityField' => 'position',
            'id' => 123,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame([
            'before' => null,
            'this' => null,
            'after' => null,
        ], $result);
    }

    public function testGetEntitiesAroundPositionEndpointWithValidPosition(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new GetEntitiesAroundPositionEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'entityType' => 'QuestionCategory',
            'entityField' => 'position',
            'position' => 3.0,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame([
            'before' => [
                'id' => 12,
                'position' => 0.0,
                'title' => '-',
            ],
            'this' => [
                'id' => 1234,
                'position' => 3.0,
                'title' => 'Maximal Category',
            ],
            'after' => null,
        ], $result);
    }

    public function testGetEntitiesAroundPositionEndpointWithInvalidFilter(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new GetEntitiesAroundPositionEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                'entityType' => 'Role',
                'entityField' => 'featuredPosition',
                'id' => null,
                'filter' => ['invalidFilter' => 'invalid'],
            ]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame(400, $httperr->getCode());
            $this->assertSame([
                "INFO Valid user request",
                "NOTICE HTTP error 400",
            ], $this->getLogs());
        }
    }

    public function testGetEntitiesAroundPositionEndpointWithValidFilter(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new GetEntitiesAroundPositionEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'entityType' => 'Role',
            'entityField' => 'featuredPosition',
            'id' => 1234,
            'filter' => ['featuredPositionNotNull' => 'true'],
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame([
            'before' => null,
            'this' => [
                'id' => 1234,
                'position' => 6.0,
                'title' => 'Test Role',
            ],
            'after' => null,
        ], $result);
    }
}
