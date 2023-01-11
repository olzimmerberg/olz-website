<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Termine\Endpoints;

use Olz\Entity\Termine\Termin;
use Olz\Termine\Endpoints\DeleteTerminEndpoint;
use Olz\Tests\Fake;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use PhpTypeScriptApi\HttpError;

class FakeDeleteTerminEndpointTerminRepository {
    public function findOneBy($where) {
        if ($where === ['id' => 123]) {
            $entry = new Termin();
            $entry->setId(123);
            return $entry;
        }
        if ($where === ['id' => 9999]) {
            return null;
        }
        $where_json = json_encode($where);
        throw new \Exception("Query not mocked in findOneBy: {$where_json}", 1);
    }
}

/**
 * @internal
 *
 * @covers \Olz\Termine\Endpoints\DeleteTerminEndpoint
 */
final class DeleteTerminEndpointTest extends UnitTestCase {
    public function testDeleteTerminEndpointIdent(): void {
        $endpoint = new DeleteTerminEndpoint();
        $this->assertSame('DeleteTerminEndpoint', $endpoint->getIdent());
    }

    public function testDeleteTerminEndpointNoAccess(): void {
        $auth_utils = new Fake\FakeAuthUtils();
        $auth_utils->has_permission_by_query = ['termine' => false];
        $logger = Fake\FakeLogger::create();
        $endpoint = new DeleteTerminEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setLog($logger);

        try {
            $endpoint->call([
                'id' => 123,
            ]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                "INFO Valid user request",
                "WARNING HTTP error 403",
            ], $logger->handler->getPrettyRecords());
            $this->assertSame(403, $err->getCode());
        }
    }

    public function testDeleteTerminEndpoint(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $termin_repo = new FakeDeleteTerminEndpointTerminRepository();
        $entity_manager->repositories[Termin::class] = $termin_repo;
        $auth_utils = new Fake\FakeAuthUtils();
        $auth_utils->has_permission_by_query = ['termine' => true];
        $entity_utils = new Fake\FakeEntityUtils();
        $entity_utils->can_update_olz_entity = true;
        $logger = Fake\FakeLogger::create();
        $endpoint = new DeleteTerminEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setEntityUtils($entity_utils);
        $endpoint->setLog($logger);

        $result = $endpoint->call([
            'id' => 123,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());

        $this->assertSame([
            'status' => 'OK',
        ], $result);
        $this->assertSame(1, count($entity_manager->removed));
        $this->assertSame(1, count($entity_manager->flushed_removed));
        $this->assertSame($entity_manager->removed, $entity_manager->flushed_removed);
        $termin = $entity_manager->removed[0];
        $this->assertSame(123, $termin->getId());
    }

    public function testDeleteTerminEndpointInexistent(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $termin_repo = new FakeDeleteTerminEndpointTerminRepository();
        $entity_manager->repositories[Termin::class] = $termin_repo;
        $auth_utils = new Fake\FakeAuthUtils();
        $auth_utils->has_permission_by_query = ['termine' => true];
        $entity_utils = new Fake\FakeEntityUtils();
        $entity_utils->can_update_olz_entity = true;
        $logger = Fake\FakeLogger::create();
        $endpoint = new DeleteTerminEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setEntityUtils($entity_utils);
        $endpoint->setLog($logger);

        $result = $endpoint->call([
            'id' => 9999,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());

        $this->assertSame([
            'status' => 'ERROR',
        ], $result);
        $this->assertSame(0, count($entity_manager->removed));
        $this->assertSame(0, count($entity_manager->flushed_removed));
    }
}
