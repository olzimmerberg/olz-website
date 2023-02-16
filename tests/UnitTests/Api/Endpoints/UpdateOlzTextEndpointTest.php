<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Api\Endpoints;

use Olz\Api\Endpoints\UpdateOlzTextEndpoint;
use Olz\Entity\OlzText;
use Olz\Tests\Fake;
use Olz\Tests\UnitTests\Common\UnitTestCase;

class FakeUpdateOlzTextEndpointOlzTextRepository {
    public $olz_text;

    public function __construct() {
        $olz_text = new OlzText();
        $olz_text->setId(1);
        $this->olz_text = $olz_text;
    }

    public function findOneBy($where) {
        if ($where === ['id' => 1]) {
            return $this->olz_text;
        }
        return null;
    }
}

/**
 * @internal
 *
 * @covers \Olz\Api\Endpoints\UpdateOlzTextEndpoint
 */
final class UpdateOlzTextEndpointTest extends UnitTestCase {
    public function testUpdateOlzTextEndpointIdent(): void {
        $endpoint = new UpdateOlzTextEndpoint();
        $this->assertSame('UpdateOlzTextEndpoint', $endpoint->getIdent());
    }

    public function testUpdateOlzTextEndpointNoAccess(): void {
        $auth_utils = new Fake\FakeAuthUtils();
        $auth_utils->has_permission_by_query = ['olz_text_1' => false];
        $entity_manager = new Fake\FakeEntityManager();
        $logger = Fake\FakeLogger::create();
        $endpoint = new UpdateOlzTextEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setLog($logger);

        $result = $endpoint->call([
            'id' => 1,
            'text' => 'New **content**!',
        ]);

        $this->assertSame([
            'INFO Valid user request',
            'INFO Valid user response',
        ], $logger->handler->getPrettyRecords());
        $this->assertSame(['status' => 'ERROR'], $result);
    }

    public function testUpdateOlzTextEndpointNoEntry(): void {
        $auth_utils = new Fake\FakeAuthUtils();
        $auth_utils->has_permission_by_query = ['olz_text_3' => true];
        $entity_manager = new Fake\FakeEntityManager();
        $olz_text_repo = new FakeUpdateOlzTextEndpointOlzTextRepository();
        $entity_manager->repositories[OlzText::class] = $olz_text_repo;
        $logger = Fake\FakeLogger::create();
        $endpoint = new UpdateOlzTextEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setLog($logger);

        $result = $endpoint->call([
            'id' => 3,
            'text' => 'New **content**!',
        ]);

        $this->assertSame([
            'INFO Valid user request',
            'INFO Valid user response',
        ], $logger->handler->getPrettyRecords());
        $this->assertSame(['status' => 'OK'], $result);
        $this->assertSame(1, count($entity_manager->persisted));
        $this->assertSame('New **content**!', $entity_manager->persisted[0]->getText());
        $this->assertSame(1, count($entity_manager->flushed_persisted));
        $this->assertSame('New **content**!', $entity_manager->flushed_persisted[0]->getText());
    }

    public function testUpdateOlzTextEndpoint(): void {
        $auth_utils = new Fake\FakeAuthUtils();
        $auth_utils->has_permission_by_query = ['olz_text_1' => true];
        $entity_manager = new Fake\FakeEntityManager();
        $olz_text_repo = new FakeUpdateOlzTextEndpointOlzTextRepository();
        $entity_manager->repositories[OlzText::class] = $olz_text_repo;
        $logger = Fake\FakeLogger::create();
        $endpoint = new UpdateOlzTextEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setLog($logger);

        $result = $endpoint->call([
            'id' => 1,
            'text' => 'New **content**!',
        ]);

        $this->assertSame([
            'INFO Valid user request',
            'INFO Valid user response',
        ], $logger->handler->getPrettyRecords());
        $this->assertSame(['status' => 'OK'], $result);
        $olz_text = $entity_manager->getRepository(OlzText::class)->olz_text;
        $this->assertSame(1, $olz_text->getId());
        $this->assertSame('New **content**!', $olz_text->getText());
    }
}
