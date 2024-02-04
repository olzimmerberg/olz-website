<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Startseite\Endpoints;

use Olz\Entity\Startseite\WeeklyPicture;
use Olz\Entity\Startseite\WeeklyPictureVote;
use Olz\Startseite\Endpoints\UpdateWeeklyPictureVoteEndpoint;
use Olz\Tests\Fake;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

class FakeUpdateWeeklyPictureVoteEndpointWeeklyPictureRepository {
    public function findOneBy($where) {
        if ($where['id'] === 123 || $where['id'] === 999) {
            $entry = new WeeklyPicture();
            $entry->setId($where['id']);
            return $entry;
        }
        if ($where['id'] === 9999) {
            return null;
        }
        $where_json = json_encode($where);
        throw new \Exception("Query not mocked in findOneBy: {$where_json}", 1);
    }
}

class FakeUpdateWeeklyPictureVoteEndpointWeeklyPictureVoteRepository {
    public function findOneBy($where) {
        if ($where['weekly_picture']->getId() === 123) {
            $entry = new WeeklyPictureVote();
            $entry->setId(Fake\FakeEntityManager::AUTO_INCREMENT_ID);
            $entry->setVote(-1);
            return $entry;
        }
        if ($where['weekly_picture']->getId() === 999) {
            return null;
        }
        $where_json = json_encode($where);
        throw new \Exception("Query not mocked in findOneBy: {$where_json}", 1);
    }
}

/**
 * @internal
 *
 * @covers \Olz\Startseite\Endpoints\UpdateWeeklyPictureVoteEndpoint
 */
final class UpdateWeeklyPictureVoteEndpointTest extends UnitTestCase {
    public function testUpdateWeeklyPictureVoteEndpointIdent(): void {
        $endpoint = new UpdateWeeklyPictureVoteEndpoint();
        $this->assertSame('UpdateWeeklyPictureVoteEndpoint', $endpoint->getIdent());
    }

    public function testUpdateWeeklyPictureVoteEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => false];
        $endpoint = new UpdateWeeklyPictureVoteEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                'vote' => 1,
                'weeklyPictureId' => 123,
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

    public function testUpdateWeeklyPictureVoteEndpointInexistent(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $weekly_picture_repo = new FakeUpdateWeeklyPictureVoteEndpointWeeklyPictureRepository();
        $entity_manager->repositories[WeeklyPicture::class] = $weekly_picture_repo;
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $user = Fake\FakeUsers::adminUser();
        WithUtilsCache::get('authUtils')->current_user = $user;
        $endpoint = new UpdateWeeklyPictureVoteEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                'vote' => 1,
                'weeklyPictureId' => 9999,
            ]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                "INFO Valid user request",
                "WARNING HTTP error 404",
            ], $this->getLogs());
            $this->assertSame(404, $err->getCode());
        }
    }

    public function testUpdateWeeklyPictureVoteEndpointNew(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $weekly_picture_repo = new FakeUpdateWeeklyPictureVoteEndpointWeeklyPictureRepository();
        $entity_manager->repositories[WeeklyPicture::class] = $weekly_picture_repo;
        $weekly_picture_vote_repo = new FakeUpdateWeeklyPictureVoteEndpointWeeklyPictureVoteRepository();
        $entity_manager->repositories[WeeklyPictureVote::class] = $weekly_picture_vote_repo;
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $user = Fake\FakeUsers::adminUser();
        WithUtilsCache::get('authUtils')->current_user = $user;
        $endpoint = new UpdateWeeklyPictureVoteEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'vote' => 1,
            'weeklyPictureId' => 999,
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
        $weekly_picture_vote = $entity_manager->persisted[0];
        $this->assertSame(Fake\FakeEntityManager::AUTO_INCREMENT_ID, $weekly_picture_vote->getId());
        $this->assertSame('2020-03-13', $weekly_picture_vote->getCreatedAt()->format('Y-m-d'));
        $this->assertSame($user, $weekly_picture_vote->getCreatedByUser());
        $this->assertSame(999, $weekly_picture_vote->getWeeklyPicture()->getId());
        $this->assertSame(1, $weekly_picture_vote->getVote());
    }

    public function testUpdateWeeklyPictureVoteEndpointExisting(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $weekly_picture_repo = new FakeUpdateWeeklyPictureVoteEndpointWeeklyPictureRepository();
        $entity_manager->repositories[WeeklyPicture::class] = $weekly_picture_repo;
        $weekly_picture_vote_repo = new FakeUpdateWeeklyPictureVoteEndpointWeeklyPictureVoteRepository();
        $entity_manager->repositories[WeeklyPictureVote::class] = $weekly_picture_vote_repo;
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $user = Fake\FakeUsers::adminUser();
        WithUtilsCache::get('authUtils')->current_user = $user;
        $endpoint = new UpdateWeeklyPictureVoteEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'vote' => 1,
            'weeklyPictureId' => 123,
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
        $weekly_picture_vote = $entity_manager->persisted[0];
        $this->assertSame(Fake\FakeEntityManager::AUTO_INCREMENT_ID, $weekly_picture_vote->getId());
        $this->assertSame('2020-03-13', $weekly_picture_vote->getCreatedAt()->format('Y-m-d'));
        $this->assertSame($user, $weekly_picture_vote->getCreatedByUser());
        $this->assertSame(123, $weekly_picture_vote->getWeeklyPicture()->getId());
        $this->assertSame(1, $weekly_picture_vote->getVote());
    }
}
