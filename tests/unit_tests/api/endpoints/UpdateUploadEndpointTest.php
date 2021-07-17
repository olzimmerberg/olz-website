<?php

declare(strict_types=1);

require_once __DIR__.'/../../../../src/api/endpoints/UpdateUploadEndpoint.php';
require_once __DIR__.'/../../../../src/config/vendor/autoload.php';
require_once __DIR__.'/../../../../src/utils/GeneralUtils.php';
require_once __DIR__.'/../../../fake/FakeAuthUtils.php';
require_once __DIR__.'/../../../fake/FakeEnvUtils.php';
require_once __DIR__.'/../../../fake/FakeLogger.php';
require_once __DIR__.'/../../common/UnitTestCase.php';

/**
 * @internal
 * @covers \UpdateUploadEndpoint
 */
final class UpdateUploadEndpointTest extends UnitTestCase {
    public function setUp(): void {
        parent::setUp();
        $temp_path = __DIR__.'/../../tmp/temp/';
        if (is_dir($temp_path)) {
            foreach (scandir($temp_path) as $entry) {
                if ($entry != '.' && $entry != '..') {
                    unlink("{$temp_path}{$entry}");
                }
            }
            rmdir($temp_path);
        }
    }

    public function testUpdateUploadEndpointIdent(): void {
        $endpoint = new UpdateUploadEndpoint();
        $this->assertSame('UpdateUploadEndpoint', $endpoint->getIdent());
    }

    public function testUpdateUploadEndpointUnauthorized(): void {
        $auth_utils = new FakeAuthUtils();
        $auth_utils->has_permission_by_query = ['any' => false];
        $logger = FakeLogger::create();
        $endpoint = new UpdateUploadEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setLogger($logger);

        $result = $endpoint->call([
            'id' => 'AAAAAAAAAAAAAAAAAAAAAAAA',
            'part' => 0,
            'content' => 'ASDF',
        ]);

        $this->assertSame(['status' => 'ERROR'], $result);
        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());
    }

    public function testUpdateUploadEndpointInvalidId(): void {
        $auth_utils = new FakeAuthUtils();
        $auth_utils->has_permission_by_query = ['any' => true];
        $env_utils = new FakeEnvUtils();
        $logger = FakeLogger::create();
        $endpoint = new UpdateUploadEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setEnvUtils($env_utils);
        $endpoint->setLogger($logger);

        mkdir(__DIR__.'/../../tmp/temp/', 0777, true);

        $result = $endpoint->call([
            'id' => 'invalid',
            'part' => 0,
            'content' => 'ASDF',
        ]);

        $this->assertSame(['status' => 'ERROR'], $result);
        $this->assertSame([
            "INFO Valid user request",
            "ERROR Could not update upload. Invalid ID: 'invalid'.",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());
        $this->assertSame(false, is_file(__DIR__.'/../../tmp/temp/invalid_0'));
    }

    public function testUpdateUploadEndpoint(): void {
        $auth_utils = new FakeAuthUtils();
        $auth_utils->has_permission_by_query = ['any' => true];
        $env_utils = new FakeEnvUtils();
        $general_utils = GeneralUtils::fromEnv();
        $logger = FakeLogger::create();
        $endpoint = new UpdateUploadEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setEnvUtils($env_utils);
        $endpoint->setGeneralUtils($general_utils);
        $endpoint->setLogger($logger);

        mkdir(__DIR__.'/../../tmp/temp/', 0777, true);
        file_put_contents(__DIR__.'/../../tmp/temp/AAAAAAAAAAAAAAAAAAAAAAAA', '');

        $result = $endpoint->call([
            'id' => 'AAAAAAAAAAAAAAAAAAAAAAAA',
            'part' => 0,
            'content' => 'ASDF',
        ]);

        $this->assertSame(['status' => 'OK'], $result);
        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());
        $this->assertSame(true, is_file(__DIR__.'/../../tmp/temp/AAAAAAAAAAAAAAAAAAAAAAAA_0'));
        $this->assertSame('H1', file_get_contents(__DIR__.'/../../tmp/temp/AAAAAAAAAAAAAAAAAAAAAAAA_0'));
    }
}
