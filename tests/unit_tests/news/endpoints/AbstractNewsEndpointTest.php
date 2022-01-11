<?php

declare(strict_types=1);

use PhpTypeScriptApi\Fields\FieldTypes;

require_once __DIR__.'/../../../../src/news/endpoints/AbstractNewsEndpoint.php';
require_once __DIR__.'/../../../../src/config/vendor/autoload.php';
require_once __DIR__.'/../../../../src/utils/date/FixedDateUtils.php';
require_once __DIR__.'/../../../fake/fake_role.php';
require_once __DIR__.'/../../../fake/FakeUsers.php';
require_once __DIR__.'/../../../fake/FakeAuthUtils.php';
require_once __DIR__.'/../../../fake/FakeLogger.php';
require_once __DIR__.'/../../../fake/FakeEntityManager.php';
require_once __DIR__.'/../../../fake/FakeEnvUtils.php';
require_once __DIR__.'/../../common/UnitTestCase.php';

class AbstractNewsConcreteEndpoint extends AbstractNewsEndpoint {
    public static function getIdent() {
        return 'ident';
    }

    public function getResponseField() {
        return new FieldTypes\Field();
    }

    public function getRequestField() {
        return new FieldTypes\Field();
    }

    protected function handle($input) {
    }
}

/**
 * @internal
 * @covers \AbstractNewsEndpoint
 */
final class AbstractNewsEndpointTest extends UnitTestCase {
    public function testAbstractNewsEndpoint(): void {
        $endpoint = new AbstractNewsConcreteEndpoint();
        $field = $endpoint->getNewsDataField();
        $this->assertSame(true, $field instanceof FieldTypes\ObjectField);
        $field_structure = $field->getFieldStructure();
        $keys = array_keys($field_structure);
        sort($keys);
        $this->assertSame([
            'author',
            'authorRoleId',
            'authorUserId',
            'content',
            'externalUrl',
            'fileIds',
            'imageIds',
            'onOff',
            'ownerRoleId',
            'ownerUserId',
            'tags',
            'teaser',
            'terminId',
            'title',
        ], $keys);
    }
}
