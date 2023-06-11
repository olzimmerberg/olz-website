<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Termine\Endpoints;

use Olz\Entity\Termine\Termin;
use Olz\Termine\Endpoints\GetTerminEndpoint;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

class FakeGetTerminEndpointTerminRepository {
    public function findOneBy($where) {
        if ($where === ['id' => 12]) {
            $termin = new Termin();
            $termin->setId(12);
            $termin->setStartsOn(new \DateTime('2020-03-13'));
            $termin->setTitle("Fake title");
            $termin->setNewsletter(false);
            $termin->setOnOff(true);
            return $termin;
        }
        if ($where === ['id' => 123]) {
            $termin = new Termin();
            $termin->setId(123);
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
        $where_json = json_encode($where);
        throw new \Exception("Query not mocked in findOneBy: {$where_json}", 1);
    }
}

/**
 * @internal
 *
 * @covers \Olz\Termine\Endpoints\GetTerminEndpoint
 */
final class GetTerminEndpointTest extends UnitTestCase {
    public function testGetTerminEndpointIdent(): void {
        $endpoint = new GetTerminEndpoint();
        $this->assertSame('GetTerminEndpoint', $endpoint->getIdent());
    }

    public function testGetTerminEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => false];
        $endpoint = new GetTerminEndpoint();
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

    public function testGetTerminEndpointMinimal(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $entity_manager = WithUtilsCache::get('entityManager');
        $termin_repo = new FakeGetTerminEndpointTerminRepository();
        $entity_manager->repositories[Termin::class] = $termin_repo;
        $endpoint = new GetTerminEndpoint();
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

    public function testGetTerminEndpointMaximal(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $entity_manager = WithUtilsCache::get('entityManager');
        $termin_repo = new FakeGetTerminEndpointTerminRepository();
        $entity_manager->repositories[Termin::class] = $termin_repo;
        $endpoint = new GetTerminEndpoint();
        $endpoint->runtimeSetup();

        mkdir(__DIR__.'/../../tmp/files/');
        mkdir(__DIR__.'/../../tmp/files/termine/');
        mkdir(__DIR__.'/../../tmp/files/termine/123/');
        file_put_contents(__DIR__.'/../../tmp/files/termine/123/file1.pdf', '');
        file_put_contents(__DIR__.'/../../tmp/files/termine/123/file2.pdf', '');

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
