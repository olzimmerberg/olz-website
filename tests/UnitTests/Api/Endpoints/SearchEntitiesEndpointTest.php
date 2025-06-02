<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Api\Endpoints;

use Olz\Api\Endpoints\SearchEntitiesEndpoint;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Api\Endpoints\SearchEntitiesEndpoint
 */
final class SearchEntitiesEndpointTest extends UnitTestCase {
    public function testSearchEntitiesEndpointWithoutInput(): void {
        $endpoint = new SearchEntitiesEndpoint();
        $endpoint->runtimeSetup();
        try {
            $endpoint->call([]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame([
                'entityType' => ["Fehlender Schlüssel: entityType."],
                // @phpstan-ignore-next-line
            ], $httperr->getPrevious()->getValidationErrors());
            $this->assertSame([
                "NOTICE Bad user request",
            ], $this->getLogs());
        }
    }

    public function testSearchEntitiesEndpointWithNullInput(): void {
        $endpoint = new SearchEntitiesEndpoint();
        $endpoint->runtimeSetup();
        try {
            $endpoint->call([
                'entityType' => null,
                'query' => null,
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
                // @phpstan-ignore-next-line
            ], $httperr->getPrevious()->getValidationErrors());
            $this->assertSame([
                "NOTICE Bad user request",
            ], $this->getLogs());
        }
    }

    public function testSearchEntitiesEndpointWithInvalidEntityType(): void {
        $endpoint = new SearchEntitiesEndpoint();
        $endpoint->runtimeSetup();
        try {
            $endpoint->call([
                'entityType' => 'invalid',
                'query' => null,
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
                // @phpstan-ignore-next-line
            ], $httperr->getPrevious()->getValidationErrors());
            $this->assertSame([
                "NOTICE Bad user request",
            ], $this->getLogs());
        }
    }

    public function testSearchEntitiesEndpointWithNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => false];
        $endpoint = new SearchEntitiesEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                'entityType' => 'TerminLocation',
                'query' => null,
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

    public function testSearchEntitiesEndpointWithValidId(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new SearchEntitiesEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'entityType' => 'TerminLocation',
            'query' => null,
            'id' => 1234,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame(['result' => [
            ['id' => 1234, 'title' => 'Fake title'],
        ]], $result);
    }

    public function testSearchEntitiesEndpointWithValidQuery(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new SearchEntitiesEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'entityType' => 'TerminLocation',
            'query' => 'Fake',
            'id' => null,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame(['result' => [
            ['id' => 12, 'title' => 'Fake title'],
            ['id' => 1234, 'title' => 'Fake title'],
        ]], $result);
    }

    public function testSearchEntitiesEndpointWithInvalidFilter(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new SearchEntitiesEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                'entityType' => 'TerminLocation',
                'query' => 'Fake',
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

    public function testSearchEntitiesEndpointWithValidFilter(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new SearchEntitiesEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'entityType' => 'Role',
            'query' => 'Test',
            'id' => null,
            'filter' => ['featuredPositionNotNull' => 'true'],
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame(['result' => [
            ['id' => 1234, 'title' => 'Test Role'],
        ]], $result);
    }

    public function testSearchEntitiesEndpointForNonOlzEntity(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new SearchEntitiesEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'entityType' => 'SolvEvent',
            'query' => 'Fake',
            'id' => null,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame(['result' => [
            ['id' => 1234, 'title' => '2020-03-13: Fake Event'],
        ]], $result);
    }
}
