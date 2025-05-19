<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Apps\Results\Endpoints;

use Olz\Apps\Results\Endpoints\UpdateResultsEndpoint;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Apps\Results\Endpoints\UpdateResultsEndpoint
 */
final class UpdateResultsEndpointTest extends UnitTestCase {
    public function testUpdateResultsEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => false];
        $endpoint = new UpdateResultsEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                'file' => '2020-milchsuppe.xml',
                'content' => null,
                'iofXmlFileId' => 'uploaded_file.pdf',
            ]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                "INFO Valid user request",
                "NOTICE HTTP error 403",
            ], $this->getLogs());
            $this->assertSame(403, $err->getCode());
        }
    }

    public function testUpdateResultsEndpointInvalidFilename(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new UpdateResultsEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'file' => 'äöü.xml',
            'content' => null,
            'iofXmlFileId' => 'uploaded_file.pdf',
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "WARNING Filename must match ^[a-z0-9\\-\\.]+$: äöü.xml",
            "INFO Valid user response",
        ], $this->getLogs());

        $this->assertSame([
            'status' => 'INVALID_FILENAME',
        ], $result);
    }

    public function testUpdateResultsEndpointInvalidBase64(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new UpdateResultsEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'file' => '2020-milchsuppe.xml',
            'content' => 'ä',
            'iofXmlFileId' => null,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "WARNING Invalid base64 data",
            "INFO Valid user response",
        ], $this->getLogs());

        $this->assertSame([
            'status' => 'INVALID_BASE64_DATA',
        ], $result);
    }

    public function testUpdateResultsEndpointInvalidNoData(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new UpdateResultsEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                'file' => '2020-milchsuppe.xml',
                'content' => null,
                'iofXmlFileId' => null,
            ]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                "INFO Valid user request",
                "NOTICE HTTP error 400",
            ], $this->getLogs());
            $this->assertSame(400, $err->getCode());
        }
    }

    public function testUpdateResultsEndpointWithFileNotFound(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new UpdateResultsEndpoint();
        $endpoint->runtimeSetup();

        mkdir(__DIR__.'/../../../tmp/temp/');
        // File is missing

        try {
            $endpoint->call([
                'file' => '2020-milchsuppe.xml',
                'content' => null,
                'iofXmlFileId' => 'uploaded_file.pdf',
            ]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                "INFO Valid user request",
                "NOTICE HTTP error 400",
            ], $this->getLogs());
            $this->assertSame(400, $err->getCode());
        }
    }

    public function testUpdateResultsEndpointWithFileId(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new UpdateResultsEndpoint();
        $endpoint->runtimeSetup();

        mkdir(__DIR__.'/../../../tmp/temp/');
        file_put_contents(__DIR__.'/../../../tmp/temp/uploaded_file.pdf', 'fake-xml');
        mkdir(__DIR__.'/../../../tmp/results/');

        $result = $endpoint->call([
            'file' => '2020-milchsuppe.xml',
            'content' => null,
            'iofXmlFileId' => 'uploaded_file.pdf',
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());

        $this->assertSame(['status' => 'OK'], $result);

        $this->assertSame(
            'fake-xml',
            file_get_contents(__DIR__.'/../../../tmp/results/2020-milchsuppe.xml')
        );
    }

    public function testUpdateResultsEndpointWithContent(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new UpdateResultsEndpoint();
        $endpoint->runtimeSetup();

        mkdir(__DIR__.'/../../../tmp/results/');

        $result = $endpoint->call([
            'file' => '2020-milchsuppe.xml',
            'content' => base64_encode('fake-xml'),
            'iofXmlFileId' => null,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());

        $this->assertSame(['status' => 'OK'], $result);

        $this->assertSame(
            'fake-xml',
            file_get_contents(__DIR__.'/../../../tmp/results/2020-milchsuppe.xml')
        );
    }

    public function testUpdateResultsEndpointWithDataUrl(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new UpdateResultsEndpoint();
        $endpoint->runtimeSetup();

        mkdir(__DIR__.'/../../../tmp/results/');

        $result = $endpoint->call([
            'file' => '2020-milchsuppe.xml',
            'content' => 'data:text/plain;base64,'.base64_encode('fake-xml'),
            'iofXmlFileId' => null,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());

        $this->assertSame(['status' => 'OK'], $result);

        $this->assertSame(
            'fake-xml',
            file_get_contents(__DIR__.'/../../../tmp/results/2020-milchsuppe.xml')
        );
    }

    public function testUpdateResultsEndpointWithExistingFile(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new UpdateResultsEndpoint();
        $endpoint->runtimeSetup();

        mkdir(__DIR__.'/../../../tmp/results/');
        file_put_contents(__DIR__.'/../../../tmp/results/2020-termine-4.xml', 'existing-xml');

        $result = $endpoint->call([
            'file' => '2020-termine-4.xml',
            'content' => base64_encode('fake-xml'),
            'iofXmlFileId' => null,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());

        $this->assertSame(['status' => 'OK'], $result);

        $this->assertSame(
            'fake-xml',
            file_get_contents(__DIR__.'/../../../tmp/results/2020-termine-4.xml')
        );
        $this->assertSame(
            'existing-xml',
            file_get_contents(__DIR__.'/../../../tmp/results/2020-termine-4.xml.bak_2020-03-13_19:30:00')
        );
    }
}
