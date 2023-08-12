<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Termine\Endpoints;

use Olz\Entity\Termine\Termin;
use Olz\Termine\Endpoints\EditTerminEndpoint;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

class FakeEditTerminEndpointTerminRepository {
    public function findOneBy($where) {
        // Minimal
        if ($where === ['id' => 12]) {
            $termin = new Termin();
            $termin->setId(12);
            $termin->setStartsOn(new \DateTime('2020-03-13'));
            $termin->setTitle("Fake title");
            $termin->setText("");
            $termin->setNewsletter(false);
            $termin->setOnOff(true);
            return $termin;
        }
        // Empty
        if ($where === ['id' => 123]) {
            $termin = new Termin();
            $termin->setId(123);
            $termin->setStartsOn(new \DateTime('0000-01-01'));
            $termin->setStartTime(new \DateTime('00:00:00'));
            $termin->setEndsOn(new \DateTime('0000-01-01'));
            $termin->setEndTime(new \DateTime('00:00:00'));
            $termin->setTitle("Cannot be empty");
            $termin->setText("");
            $termin->setLink('');
            $termin->setTypes('');
            $termin->setCoordinateX(0);
            $termin->setCoordinateY(0);
            $termin->setDeadline(new \DateTime('0000-01-01 00:00:00'));
            $termin->setSolvId('');
            $termin->setGo2olId('');
            $termin->setNewsletter(false);
            $termin->setImageIds([]);
            $termin->setOnOff(false);
            return $termin;
        }
        // Maximal
        if ($where === ['id' => 1234]) {
            $termin = new Termin();
            $termin->setId(1234);
            $termin->setStartsOn(new \DateTime('2020-03-13'));
            $termin->setStartTime(new \DateTime('19:30:00'));
            $termin->setEndsOn(new \DateTime('2020-03-16'));
            $termin->setEndTime(new \DateTime('12:00:00'));
            $termin->setTitle("Fake title");
            $termin->setText("Fake content");
            $termin->setLink('<a href="test-anlass.ch">Home</a>');
            $termin->setTypes(' training weekends ');
            $termin->setCoordinateX(684835);
            $termin->setCoordinateY(237021);
            $termin->setDeadline(new \DateTime('2020-03-13 18:00:00'));
            $termin->setSolvId(11012);
            $termin->setGo2olId('deprecated');
            $termin->setNewsletter(true);
            $termin->setImageIds(['img1.jpg', 'img2.png']);
            $termin->setOnOff(true);
            return $termin;
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
 * @covers \Olz\Termine\Endpoints\EditTerminEndpoint
 */
final class EditTerminEndpointTest extends UnitTestCase {
    public function testEditTerminEndpointIdent(): void {
        $endpoint = new EditTerminEndpoint();
        $this->assertSame('EditTerminEndpoint', $endpoint->getIdent());
    }

    public function testEditTerminEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['termine' => false];
        $endpoint = new EditTerminEndpoint();
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

    public function testEditTerminEndpointNoSuchEntity(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['termine' => true];
        $entity_manager = WithUtilsCache::get('entityManager');
        $termin_repo = new FakeEditTerminEndpointTerminRepository();
        $entity_manager->repositories[Termin::class] = $termin_repo;
        $endpoint = new EditTerminEndpoint();
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

    public function testEditTerminEndpointMinimal(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['termine' => true];
        $entity_manager = WithUtilsCache::get('entityManager');
        $termin_repo = new FakeEditTerminEndpointTerminRepository();
        $entity_manager->repositories[Termin::class] = $termin_repo;
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new EditTerminEndpoint();
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
                'startDate' => '2020-03-13',
                'startTime' => null,
                'endDate' => null,
                'endTime' => null,
                'title' => 'Fake title',
                'text' => '',
                'link' => '',
                'deadline' => null,
                'newsletter' => false,
                'solvId' => null,
                'go2olId' => null,
                'types' => [],
                'coordinateX' => null,
                'coordinateY' => null,
                'imageIds' => [],
                'fileIds' => [],
            ],
        ], $result);
    }

    public function testEditTerminEndpointEmpty(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['termine' => true];
        $entity_manager = WithUtilsCache::get('entityManager');
        $termin_repo = new FakeEditTerminEndpointTerminRepository();
        $entity_manager->repositories[Termin::class] = $termin_repo;
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new EditTerminEndpoint();
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
                'startDate' => '0000-01-01',
                'startTime' => '00:00:00',
                'endDate' => '0000-01-01',
                'endTime' => '00:00:00',
                'title' => 'Cannot be empty',
                'text' => '',
                'link' => '',
                'deadline' => '0000-01-01 00:00:00',
                'newsletter' => false,
                'solvId' => null,
                'go2olId' => null,
                'types' => [],
                'coordinateX' => 0,
                'coordinateY' => 0,
                'imageIds' => [],
                'fileIds' => [],
            ],
        ], $result);
    }

    public function testEditTerminEndpointMaximal(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['termine' => true];
        $entity_manager = WithUtilsCache::get('entityManager');
        $termin_repo = new FakeEditTerminEndpointTerminRepository();
        $entity_manager->repositories[Termin::class] = $termin_repo;
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new EditTerminEndpoint();
        $endpoint->runtimeSetup();

        mkdir(__DIR__.'/../../tmp/temp/');
        mkdir(__DIR__.'/../../tmp/img/');
        mkdir(__DIR__.'/../../tmp/img/termine/');
        mkdir(__DIR__.'/../../tmp/img/termine/1234/');
        mkdir(__DIR__.'/../../tmp/img/termine/1234/img/');
        file_put_contents(__DIR__.'/../../tmp/img/termine/1234/img/img1.jpg', '');
        file_put_contents(__DIR__.'/../../tmp/img/termine/1234/img/img2.png', '');
        mkdir(__DIR__.'/../../tmp/files/');
        mkdir(__DIR__.'/../../tmp/files/termine/');
        mkdir(__DIR__.'/../../tmp/files/termine/1234/');
        file_put_contents(__DIR__.'/../../tmp/files/termine/1234/file1.pdf', '');
        file_put_contents(__DIR__.'/../../tmp/files/termine/1234/file2.pdf', '');

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
                'startDate' => '2020-03-13',
                'startTime' => '19:30:00',
                'endDate' => '2020-03-16',
                'endTime' => '12:00:00',
                'title' => 'Fake title',
                'text' => 'Fake content',
                'link' => '<a href="test-anlass.ch">Home</a>',
                'deadline' => '2020-03-13 18:00:00',
                'newsletter' => true,
                'solvId' => 11012,
                'go2olId' => 'deprecated',
                'types' => ['training', 'weekends'],
                'coordinateX' => 684835,
                'coordinateY' => 237021,
                'imageIds' => ['img1.jpg', 'img2.png'],
                'fileIds' => ['file1.pdf', 'file2.pdf'],
            ],
        ], $result);
    }
}
