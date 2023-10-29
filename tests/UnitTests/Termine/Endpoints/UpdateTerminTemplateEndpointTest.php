<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Termine\Endpoints;

use Olz\Entity\Termine\TerminLocation;
use Olz\Entity\Termine\TerminTemplate;
use Olz\Termine\Endpoints\UpdateTerminTemplateEndpoint;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

class FakeUpdateTerminTemplateEndpointTerminTemplateRepository {
    public function findOneBy($where) {
        if ($where === ['id' => 123]) {
            $entry = new TerminTemplate();
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

class FakeUpdateTerminTemplateEndpointTerminLocationRepository {
    public function findOneBy($where) {
        if ($where === ['id' => 123]) {
            $entry = new TerminLocation();
            $entry->setId(123);
            $entry->setName("Fake location");
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
 * @covers \Olz\Termine\Endpoints\UpdateTerminTemplateEndpoint
 */
final class UpdateTerminTemplateEndpointTest extends UnitTestCase {
    public const VALID_INPUT = [
        'id' => 123,
        'meta' => [
            'ownerUserId' => 1,
            'ownerRoleId' => 1,
            'onOff' => true,
        ],
        'data' => [
            'startTime' => '18:30:00',
            'durationSeconds' => 5400,
            'title' => 'Fake title',
            'text' => 'Fake text',
            'link' => 'Fake link',
            'deadlineEarlierSeconds' => 86400,
            'deadlineTime' => '22:00:00',
            'newsletter' => true,
            'types' => ['ol', 'club'],
            'locationId' => 123,
            'imageIds' => ['uploaded_image.jpg', 'inexistent.png'],
            'fileIds' => ['uploaded_file.pdf', 'inexistent.txt'],
        ],
    ];

    public function testUpdateTerminTemplateEndpointIdent(): void {
        $endpoint = new UpdateTerminTemplateEndpoint();
        $this->assertSame('UpdateTerminTemplateEndpoint', $endpoint->getIdent());
    }

    public function testUpdateTerminTemplateEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['termine' => false];
        $endpoint = new UpdateTerminTemplateEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call(self::VALID_INPUT);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                "INFO Valid user request",
                "WARNING HTTP error 403",
            ], $this->getLogs());
            $this->assertSame(403, $err->getCode());
        }
    }

    public function testUpdateTerminTemplateEndpointNoSuchEntity(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $termin_template_repo = new FakeUpdateTerminTemplateEndpointTerminTemplateRepository();
        $entity_manager->repositories[TerminTemplate::class] = $termin_template_repo;
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['termine' => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new UpdateTerminTemplateEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                ...self::VALID_INPUT,
                'id' => 9999,
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

    public function testUpdateTerminTemplateEndpoint(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $termin_template_repo = new FakeUpdateTerminTemplateEndpointTerminTemplateRepository();
        $entity_manager->repositories[TerminTemplate::class] = $termin_template_repo;
        $termin_location_repo = new FakeUpdateTerminTemplateEndpointTerminLocationRepository();
        $entity_manager->repositories[TerminLocation::class] = $termin_location_repo;
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['termine' => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new UpdateTerminTemplateEndpoint();
        $endpoint->runtimeSetup();

        mkdir(__DIR__.'/../../tmp/temp/');
        file_put_contents(__DIR__.'/../../tmp/temp/uploaded_image.jpg', '');
        mkdir(__DIR__.'/../../tmp/img/');
        mkdir(__DIR__.'/../../tmp/img/termin_templates/');

        $result = $endpoint->call(self::VALID_INPUT);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());

        $this->assertSame([
            'status' => 'OK',
            'id' => 123,
        ], $result);
        $this->assertSame(1, count($entity_manager->persisted));
        $this->assertSame(1, count($entity_manager->flushed_persisted));
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
        $termin_template = $entity_manager->persisted[0];
        $this->assertSame(123, $termin_template->getId());
        $this->assertSame('18:30:00', $termin_template->getStartTime()->format('H:i:s'));
        $this->assertSame(5400, $termin_template->getDurationSeconds());
        $this->assertSame('Fake title', $termin_template->getTitle());
        $this->assertSame('Fake text', $termin_template->getText());
        $this->assertSame('Fake link', $termin_template->getLink());
        $this->assertSame(86400, $termin_template->getDeadlineEarlierSeconds());
        $this->assertSame('22:00:00', $termin_template->getDeadlineTime()->format('H:i:s'));
        $this->assertSame(true, $termin_template->getNewsletter());
        $this->assertSame(' ol club ', $termin_template->getTypes());
        $this->assertSame(123, $termin_template->getLocation()->getId());
        $this->assertSame(
            ['uploaded_image.jpg', 'inexistent.png'],
            $termin_template->getImageIds(),
        );

        $this->assertSame([
            [$termin_template, 1, 1, 1],
        ], WithUtilsCache::get('entityUtils')->update_olz_entity_calls);

        $id = 123;

        $this->assertSame([
            [
                ['uploaded_image.jpg', 'inexistent.png'],
                realpath(__DIR__.'/../../../')."/Fake/../UnitTests/tmp/img/termin_templates/{$id}/img/",
            ],
            [
                ['uploaded_file.pdf', 'inexistent.txt'],
                realpath(__DIR__.'/../../../Fake/')."/../UnitTests/tmp/files/termin_templates/{$id}/",
            ],
        ], WithUtilsCache::get('uploadUtils')->move_uploads_calls);
    }
}