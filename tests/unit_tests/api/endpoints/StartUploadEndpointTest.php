<?php

declare(strict_types=1);

require_once __DIR__.'/../../../../src/api/endpoints/StartUploadEndpoint.php';
require_once __DIR__.'/../../../../src/config/vendor/autoload.php';
require_once __DIR__.'/../../../../src/utils/GeneralUtils.php';
require_once __DIR__.'/../../../fake/FakeAuthUtils.php';
require_once __DIR__.'/../../../fake/FakeLogger.php';
require_once __DIR__.'/../../common/UnitTestCase.php';

class FakeStartUploadEndpointEnvUtils {
    public function getDataPath() {
        return __DIR__.'/../../tmp/';
    }
}

class DeterministicStartUploadEndpoint extends StartUploadEndpoint {
    protected function getRandomUploadId() {
        return 'AAAAAAAAAAAAAAAAAAAAAAAA';
    }
}

/**
 * @internal
 * @covers \StartUploadEndpoint
 */
final class StartUploadEndpointTest extends UnitTestCase {
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

    public function testStartUploadEndpointIdent(): void {
        $endpoint = new StartUploadEndpoint();
        $this->assertSame('StartUploadEndpoint', $endpoint->getIdent());
    }

    public function testStartUploadEndpointUnauthorized(): void {
        $auth_utils = new FakeAuthUtils();
        $auth_utils->has_permission_by_query = ['any' => false];
        $logger = FakeLogger::create();
        $endpoint = new StartUploadEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setLogger($logger);

        $result = $endpoint->call([]);

        $this->assertSame(['status' => 'ERROR', 'id' => null], $result);
        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());
    }

    public function testStartUploadEndpointAbort(): void {
        $auth_utils = new FakeAuthUtils();
        $auth_utils->has_permission_by_query = ['any' => true];
        $env_utils = new FakeStartUploadEndpointEnvUtils();
        $general_utils = GeneralUtils::fromEnv();
        $logger = FakeLogger::create();
        $endpoint = new DeterministicStartUploadEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setEnvUtils($env_utils);
        $endpoint->setGeneralUtils($general_utils);
        $endpoint->setLogger($logger);

        mkdir(__DIR__.'/../../tmp/temp/');
        file_put_contents(__DIR__.'/../../tmp/temp/AAAAAAAAAAAAAAAAAAAAAAAA', '');

        $result = $endpoint->call([]);

        $this->assertSame(['status' => 'ERROR', 'id' => null], $result);
        $this->assertSame([
            "INFO Valid user request",
            "ERROR Could not start upload. Finding unique ID failed. Maximum number of loops exceeded.",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());
    }

    public function testStartUploadEndpoint(): void {
        $auth_utils = new FakeAuthUtils();
        $auth_utils->has_permission_by_query = ['any' => true];
        $env_utils = new FakeStartUploadEndpointEnvUtils();
        $general_utils = GeneralUtils::fromEnv();
        $logger = FakeLogger::create();
        $endpoint = new StartUploadEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setEnvUtils($env_utils);
        $endpoint->setGeneralUtils($general_utils);
        $endpoint->setLogger($logger);

        $result = $endpoint->call([]);

        $this->assertSame('OK', $result['status']);
        $this->assertMatchesRegularExpression('/^[a-zA-Z0-9-_]{24}$/', $result['id']);
        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());
    }
}
