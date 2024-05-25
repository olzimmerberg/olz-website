<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Termine\Endpoints;

use Olz\Termine\Endpoints\CreateTerminTemplateEndpoint;
use Olz\Tests\Fake;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Termine\Endpoints\CreateTerminTemplateEndpoint
 */
final class CreateTerminTemplateEndpointTest extends UnitTestCase {
    public const VALID_INPUT = [
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
            'deadlineEarlierSeconds' => 86400,
            'deadlineTime' => '22:00:00',
            'newsletter' => true,
            'types' => ['ol', 'club'],
            'locationId' => 123,
            'imageIds' => ['uploaded_image.jpg', 'inexistent.png'],
            'fileIds' => ['uploaded_file.pdf', 'inexistent.txt'],
        ],
    ];

    public function testCreateTerminTemplateEndpointIdent(): void {
        $endpoint = new CreateTerminTemplateEndpoint();
        $this->assertSame('CreateTerminTemplateEndpoint', $endpoint->getIdent());
    }

    public function testCreateTerminTemplateEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['termine' => false];
        $endpoint = new CreateTerminTemplateEndpoint();
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

    public function testCreateTerminTemplateEndpoint(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['termine' => true];
        $endpoint = new CreateTerminTemplateEndpoint();
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
            'id' => Fake\FakeEntityManager::AUTO_INCREMENT_ID,
        ], $result);
        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertCount(1, $entity_manager->persisted);
        $this->assertCount(1, $entity_manager->flushed_persisted);
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
        $termin_template = $entity_manager->persisted[0];
        $this->assertSame(Fake\FakeEntityManager::AUTO_INCREMENT_ID, $termin_template->getId());
        $this->assertSame('18:30:00', $termin_template->getStartTime()->format('H:i:s'));
        $this->assertSame(5400, $termin_template->getDurationSeconds());
        $this->assertSame('Fake title', $termin_template->getTitle());
        $this->assertSame('Fake text', $termin_template->getText());
        $this->assertSame(86400, $termin_template->getDeadlineEarlierSeconds());
        $this->assertSame('22:00:00', $termin_template->getDeadlineTime()->format('H:i:s'));
        $this->assertTrue($termin_template->getNewsletter());
        $this->assertSame(' ol club ', $termin_template->getTypes());
        $this->assertSame(123, $termin_template->getLocation()->getId());
        $this->assertSame(
            ['uploaded_image.jpg', 'inexistent.png'],
            $termin_template->getImageIds(),
        );

        $this->assertSame([
            [$termin_template, 1, 1, 1],
        ], WithUtilsCache::get('entityUtils')->create_olz_entity_calls);

        $id = Fake\FakeEntityManager::AUTO_INCREMENT_ID;

        $this->assertSame([
            [
                ['uploaded_image.jpg', 'inexistent.png'],
                realpath(__DIR__.'/../../../Fake/')."/../UnitTests/tmp/img/termin_templates/{$id}/img/",
            ],
            [
                ['uploaded_file.pdf', 'inexistent.txt'],
                realpath(__DIR__.'/../../../Fake/')."/../UnitTests/tmp/files/termin_templates/{$id}/",
            ],
        ], WithUtilsCache::get('uploadUtils')->move_uploads_calls);
    }
}
