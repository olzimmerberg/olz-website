<?php

declare(strict_types=1);

require_once __DIR__.'/../../../../src/api/endpoints/UpdateUploadEndpoint.php';
require_once __DIR__.'/../../../../src/config/vendor/autoload.php';
require_once __DIR__.'/../../../../src/utils/GeneralUtils.php';
require_once __DIR__.'/../../../fake/FakeLogger.php';
require_once __DIR__.'/../../common/UnitTestCase.php';

class FakeUpdateUploadEndpointAuthUtils {
    public $has_permission_by_query = [];

    public function hasPermission($query) {
        $has_permission = $this->has_permission_by_query[$query] ?? null;
        if ($has_permission === null) {
            throw new Exception("hasPermission has not been mocked for {$query}");
        }
        return $has_permission;
    }
}

class FakeUpdateUploadEndpointEnvUtils {
    public function getDataPath() {
        return __DIR__.'/../../tmp/';
    }
}

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
        $auth_utils = new FakeUpdateUploadEndpointAuthUtils();
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
        $auth_utils = new FakeUpdateUploadEndpointAuthUtils();
        $auth_utils->has_permission_by_query = ['any' => true];
        $env_utils = new FakeUpdateUploadEndpointEnvUtils();
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
        $auth_utils = new FakeUpdateUploadEndpointAuthUtils();
        $auth_utils->has_permission_by_query = ['any' => true];
        $env_utils = new FakeUpdateUploadEndpointEnvUtils();
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
