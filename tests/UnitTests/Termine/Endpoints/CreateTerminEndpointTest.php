<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Termine\Endpoints;

use Olz\Termine\Endpoints\CreateTerminEndpoint;
use Olz\Tests\Fake\FakeEntityManager;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Termine\Endpoints\CreateTerminEndpoint
 */
final class CreateTerminEndpointTest extends UnitTestCase {
    public const VALID_INPUT = [
        'meta' => [
            'ownerUserId' => 1,
            'ownerRoleId' => 1,
            'onOff' => true,
        ],
        'data' => [
            'fromTemplateId' => 123,
            'startDate' => '2020-03-13',
            'startTime' => null,
            'endDate' => null,
            'endTime' => null,
            'title' => 'Test event',
            'text' => "some info\n\n[Home](test-anlass.ch)",
            'deadline' => null,
            'shouldPromote' => false,
            'newsletter' => false,
            'solvId' => null,
            'go2olId' => null,
            'types' => ['training', 'weekend'],
            'locationId' => 1234,
            'coordinateX' => null,
            'coordinateY' => null,
            'imageIds' => ['uploaded_image.jpg', 'inexistent.png'],
            'fileIds' => ['uploaded_file.pdf', 'inexistent.txt'],
        ],
    ];

    public function testCreateTerminEndpointIdent(): void {
        $endpoint = new CreateTerminEndpoint();
        $this->assertSame('CreateTerminEndpoint', $endpoint->getIdent());
    }

    public function testCreateTerminEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['termine' => false];
        $endpoint = new CreateTerminEndpoint();
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

    public function testCreateTerminEndpoint(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['termine' => true];
        $endpoint = new CreateTerminEndpoint();
        $endpoint->runtimeSetup();

        mkdir(__DIR__.'/../../tmp/temp/');
        file_put_contents(__DIR__.'/../../tmp/temp/uploaded_file.pdf', '');
        mkdir(__DIR__.'/../../tmp/files/');
        mkdir(__DIR__.'/../../tmp/files/termine/');
        file_put_contents(__DIR__.'/../../tmp/temp/uploaded_image.jpg', '');
        mkdir(__DIR__.'/../../tmp/img/');
        mkdir(__DIR__.'/../../tmp/img/termine/');

        $result = $endpoint->call(self::VALID_INPUT);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());

        $this->assertSame([
            'status' => 'OK',
            'id' => FakeEntityManager::AUTO_INCREMENT_ID,
        ], $result);
        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertCount(1, $entity_manager->persisted);
        $this->assertCount(1, $entity_manager->flushed_persisted);
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
        $termin = $entity_manager->persisted[0];
        $this->assertSame(FakeEntityManager::AUTO_INCREMENT_ID, $termin->getId());
        $this->assertSame('2020-03-13', $termin->getStartDate()->format('Y-m-d'));
        $this->assertNull($termin->getStartTime());
        $this->assertNull($termin->getEndDate());
        $this->assertNull($termin->getEndTime());
        $this->assertSame('Test event', $termin->getTitle());
        $this->assertSame("some info\n\n[Home](test-anlass.ch)", $termin->getText());
        $this->assertNull($termin->getDeadline());
        $this->assertFalse($termin->getNewsletter());
        $this->assertNull($termin->getSolvId());
        $this->assertNull($termin->getGo2olId());
        $this->assertSame(['training', 'weekend'], array_map(function ($label) {
            return $label->getIdent();
        }, [...$termin->getLabels()]));
        $this->assertSame(1234, $termin->getLocation()->getId());
        $this->assertSame('Fake title', $termin->getLocation()->getName());
        $this->assertNull($termin->getCoordinateX());
        $this->assertNull($termin->getCoordinateY());
        $this->assertSame(
            ['uploaded_image.jpg', 'inexistent.png'],
            $termin->getImageIds(),
        );

        $this->assertSame([
            [$termin, 1, 1, 1],
        ], WithUtilsCache::get('entityUtils')->create_olz_entity_calls);

        $id = FakeEntityManager::AUTO_INCREMENT_ID;

        $this->assertSame([
            [
                ['uploaded_image.jpg', 'inexistent.png'],
                realpath(__DIR__.'/../../../Fake/')."/../UnitTests/tmp/img/termine/{$id}/img/",
            ],
            [
                ['uploaded_file.pdf', 'inexistent.txt'],
                realpath(__DIR__.'/../../../Fake/')."/../UnitTests/tmp/files/termine/{$id}/",
            ],
        ], WithUtilsCache::get('uploadUtils')->move_uploads_calls);
        $this->assertSame([
            [
                ['uploaded_image.jpg', 'inexistent.png'],
                realpath(__DIR__.'/../../../')."/Fake/../UnitTests/tmp/img/termine/{$id}/",
            ],
        ], WithUtilsCache::get('imageUtils')->generatedThumbnails);
    }
}
