<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Termine\Endpoints;

use Olz\Entity\Termine\TerminLocation;
use Olz\Entity\Termine\TerminTemplate;
use Olz\Termine\Endpoints\EditTerminTemplateEndpoint;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

class FakeEditTerminTemplateEndpointTerminTemplateRepository {
    public function findOneBy($where) {
        // Minimal
        if ($where === ['id' => 12]) {
            $termin_template = new TerminTemplate();
            $termin_template->setId(12);
            $termin_template->setNewsletter(true);
            $termin_template->setOnOff(true);
            return $termin_template;
        }
        // Empty
        if ($where === ['id' => 123]) {
            $termin_template = new TerminTemplate();
            $termin_template->setId(123);
            $termin_template->setStartTime(null);
            $termin_template->setDurationSeconds(null);
            $termin_template->setTitle(null);
            $termin_template->setText(null);
            $termin_template->setLink(null);
            $termin_template->setDeadlineEarlierSeconds(null);
            $termin_template->setDeadlineTime(null);
            $termin_template->setNewsletter(false);
            $termin_template->setTypes(null);
            $termin_template->setLocation(null);
            $termin_template->setImageIds(null);
            $termin_template->setOnOff(false);
            return $termin_template;
        }
        // Maximal
        if ($where === ['id' => 1234]) {
            $termin_location = new TerminLocation();
            $termin_location->setId(12);
            $termin_location->setName('Fake location');
            $termin_location->setDetails('Fake location details');
            $termin_location->setLatitude(47.2790953);
            $termin_location->setLongitude(8.5591936);
            $termin_template = new TerminTemplate();
            $termin_template->setId(1234);
            $termin_template->setStartTime(new \DateTime('09:00:00'));
            $termin_template->setDurationSeconds(7200);
            $termin_template->setTitle("Fake title");
            $termin_template->setText("Fake text");
            $termin_template->setLink("Fake link");
            $termin_template->setDeadlineEarlierSeconds(86400 * 2);
            $termin_template->setDeadlineTime(new \DateTime('18:00:00'));
            $termin_template->setNewsletter(true);
            $termin_template->setTypes(' ol club ');
            $termin_template->setLocation($termin_location);
            $termin_template->setImageIds([
                'image__________________1.jpg', 'image__________________2.png']);
            $termin_template->setOnOff(true);
            return $termin_template;
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
 * @covers \Olz\Termine\Endpoints\EditTerminTemplateEndpoint
 */
final class EditTerminTemplateEndpointTest extends UnitTestCase {
    public function testEditTerminTemplateEndpointIdent(): void {
        $endpoint = new EditTerminTemplateEndpoint();
        $this->assertSame('EditTerminTemplateEndpoint', $endpoint->getIdent());
    }

    public function testEditTerminTemplateEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['termine' => false];
        $endpoint = new EditTerminTemplateEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                'id' => 123,
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

    public function testEditTerminTemplateEndpointNoSuchEntity(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['termine' => true];
        $entity_manager = WithUtilsCache::get('entityManager');
        $termin_template_repo = new FakeEditTerminTemplateEndpointTerminTemplateRepository();
        $entity_manager->repositories[TerminTemplate::class] = $termin_template_repo;
        $endpoint = new EditTerminTemplateEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
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

    public function testEditTerminTemplateEndpointMinimal(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['termine' => true];
        $entity_manager = WithUtilsCache::get('entityManager');
        $termin_template_repo = new FakeEditTerminTemplateEndpointTerminTemplateRepository();
        $entity_manager->repositories[TerminTemplate::class] = $termin_template_repo;
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new EditTerminTemplateEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'id' => 12,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame([
            'id' => 12,
            'meta' => [
                'ownerUserId' => null,
                'ownerRoleId' => null,
                'onOff' => true,
            ],
            'data' => [
                'startTime' => null,
                'durationSeconds' => null,
                'title' => '',
                'text' => '',
                'link' => '',
                'deadlineEarlierSeconds' => null,
                'deadlineTime' => null,
                'newsletter' => true,
                'types' => [],
                'locationId' => null,
                'imageIds' => [],
                'fileIds' => [],
            ],
        ], $result);
    }

    public function testEditTerminTemplateEndpointEmpty(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['termine' => true];
        $entity_manager = WithUtilsCache::get('entityManager');
        $termin_template_repo = new FakeEditTerminTemplateEndpointTerminTemplateRepository();
        $entity_manager->repositories[TerminTemplate::class] = $termin_template_repo;
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new EditTerminTemplateEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'id' => 123,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame([
            'id' => 123,
            'meta' => [
                'ownerUserId' => null,
                'ownerRoleId' => null,
                'onOff' => false,
            ],
            'data' => [
                'startTime' => null,
                'durationSeconds' => null,
                'title' => '',
                'text' => '',
                'link' => '',
                'deadlineEarlierSeconds' => null,
                'deadlineTime' => null,
                'newsletter' => false,
                'types' => [],
                'locationId' => null,
                'imageIds' => [],
                'fileIds' => [],
            ],
        ], $result);
    }

    public function testEditTerminTemplateEndpointMaximal(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['termine' => true];
        $entity_manager = WithUtilsCache::get('entityManager');
        $termin_template_repo = new FakeEditTerminTemplateEndpointTerminTemplateRepository();
        $entity_manager->repositories[TerminTemplate::class] = $termin_template_repo;
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new EditTerminTemplateEndpoint();
        $endpoint->runtimeSetup();

        mkdir(__DIR__.'/../../tmp/temp/');
        mkdir(__DIR__.'/../../tmp/img/');
        mkdir(__DIR__.'/../../tmp/img/termin_templates/');
        mkdir(__DIR__.'/../../tmp/img/termin_templates/1234/');
        mkdir(__DIR__.'/../../tmp/img/termin_templates/1234/img/');
        file_put_contents(__DIR__.'/../../tmp/img/termin_templates/1234/img/image__________________1.jpg', '');
        file_put_contents(__DIR__.'/../../tmp/img/termin_templates/1234/img/image__________________2.png', '');
        mkdir(__DIR__.'/../../tmp/files/');
        mkdir(__DIR__.'/../../tmp/files/termin_templates/');
        mkdir(__DIR__.'/../../tmp/files/termin_templates/1234/');
        file_put_contents(__DIR__.'/../../tmp/files/termin_templates/1234/file___________________1.pdf', '');
        file_put_contents(__DIR__.'/../../tmp/files/termin_templates/1234/file___________________2.pdf', '');

        $result = $endpoint->call([
            'id' => 1234,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame([
            'id' => 1234,
            'meta' => [
                'ownerUserId' => null,
                'ownerRoleId' => null,
                'onOff' => true,
            ],
            'data' => [
                'startTime' => '09:00:00',
                'durationSeconds' => 7200,
                'title' => 'Fake title',
                'text' => 'Fake text',
                'link' => 'Fake link',
                'deadlineEarlierSeconds' => 172800,
                'deadlineTime' => '18:00:00',
                'newsletter' => true,
                'types' => ['ol', 'club'],
                'locationId' => 12,
                'imageIds' => ['image__________________1.jpg', 'image__________________2.png'],
                'fileIds' => ['file___________________1.pdf', 'file___________________2.pdf'],
            ],
        ], $result);
    }
}
