<?php

declare(strict_types=1);

require_once __DIR__.'/../../../../_/news/endpoints/EditNewsEndpoint.php';
require_once __DIR__.'/../../../../_/config/vendor/autoload.php';
require_once __DIR__.'/../../../../_/utils/date/FixedDateUtils.php';
require_once __DIR__.'/../../../fake/fake_role.php';
require_once __DIR__.'/../../../fake/FakeUsers.php';
require_once __DIR__.'/../../../fake/FakeAuthUtils.php';
require_once __DIR__.'/../../../fake/FakeLogger.php';
require_once __DIR__.'/../../../fake/FakeEntityManager.php';
require_once __DIR__.'/../../../fake/FakeEnvUtils.php';
require_once __DIR__.'/../../common/UnitTestCase.php';

/**
 * @internal
 * @covers \EditNewsEndpoint
 */
final class EditNewsEndpointTest extends UnitTestCase {
    public function testEditNewsEndpointIdent(): void {
        $endpoint = new EditNewsEndpoint();
        $this->assertSame('EditNewsEndpoint', $endpoint->getIdent());
    }
}
