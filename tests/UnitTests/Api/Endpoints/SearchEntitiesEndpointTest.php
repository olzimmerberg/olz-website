<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Api\Endpoints;

use Doctrine\Common\Collections\AbstractLazyCollection;
use Doctrine\Common\Collections\Criteria;
use Olz\Api\Endpoints\SearchEntitiesEndpoint;
use Olz\Entity\Termine\TerminLocation;
use Olz\Tests\Fake\Entity\Common\FakeLazyCollection;
use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @extends FakeOlzRepository<TerminLocation>
 */
class FakeSearchEntitiesEndpointTerminLocationRepository extends FakeOlzRepository {
    /** @return AbstractLazyCollection<int, TerminLocation> */
    public function matching(Criteria $criteria): AbstractLazyCollection {
        $termin_location_1 = new TerminLocation();
        $termin_location_1->setId(1);
        $termin_location_1->setName('Query-Hütte');
        $termin_location_1->setLatitude(47.2);
        $termin_location_1->setLongitude(8.3);
        return new FakeLazyCollection([$termin_location_1]);
    }
}

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
                "WARNING Bad user request",
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
                    ['.' => ["Wert muss vom Typ 'QuestionCategory' sein."]],
                    ['.' => ["Wert muss vom Typ 'SolvEvent' sein."]],
                    ['.' => ["Wert muss vom Typ 'TerminLocation' sein."]],
                    ['.' => ["Wert muss vom Typ 'TerminTemplate' sein."]],
                    ['.' => ["Wert muss vom Typ 'Role' sein."]],
                    ['.' => ["Wert muss vom Typ 'User' sein."]],
                ]]],
                // @phpstan-ignore-next-line
            ], $httperr->getPrevious()->getValidationErrors());
            $this->assertSame([
                "WARNING Bad user request",
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
                    ['.' => ["Wert muss vom Typ 'QuestionCategory' sein."]],
                    ['.' => ["Wert muss vom Typ 'SolvEvent' sein."]],
                    ['.' => ["Wert muss vom Typ 'TerminLocation' sein."]],
                    ['.' => ["Wert muss vom Typ 'TerminTemplate' sein."]],
                    ['.' => ["Wert muss vom Typ 'Role' sein."]],
                    ['.' => ["Wert muss vom Typ 'User' sein."]],
                ]]],
                // @phpstan-ignore-next-line
            ], $httperr->getPrevious()->getValidationErrors());
            $this->assertSame([
                "WARNING Bad user request",
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
                "WARNING HTTP error 403",
            ], $this->getLogs());
        }
    }

    public function testSearchEntitiesEndpointWithValidId(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $entity_manager = WithUtilsCache::get('entityManager');
        $termin_location_repo = new FakeSearchEntitiesEndpointTerminLocationRepository($entity_manager);
        $entity_manager->repositories[TerminLocation::class] = $termin_location_repo;
        $endpoint = new SearchEntitiesEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'entityType' => 'TerminLocation',
            'query' => null,
            'id' => 1,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame(['result' => [
            ['id' => 1, 'title' => 'Query-Hütte'],
        ]], $result);
    }

    public function testSearchEntitiesEndpointWithValidQuery(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $entity_manager = WithUtilsCache::get('entityManager');
        $termin_location_repo = new FakeSearchEntitiesEndpointTerminLocationRepository($entity_manager);
        $entity_manager->repositories[TerminLocation::class] = $termin_location_repo;
        $endpoint = new SearchEntitiesEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'entityType' => 'TerminLocation',
            'query' => 'Query',
            'id' => null,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame(['result' => [
            ['id' => 1, 'title' => 'Query-Hütte'],
        ]], $result);
    }
}
