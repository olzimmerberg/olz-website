<?php

declare(strict_types=1);

use PhpTypeScriptApi\Fields\FieldTypes;

require_once __DIR__.'/../../../../public/_/news/endpoints/NewsEndpointTrait.php';
require_once __DIR__.'/../../../../public/_/config/vendor/autoload.php';
require_once __DIR__.'/../../../../public/_/utils/date/FixedDateUtils.php';
require_once __DIR__.'/../../../fake/fake_role.php';
require_once __DIR__.'/../../../fake/FakeUsers.php';
require_once __DIR__.'/../../../fake/FakeAuthUtils.php';
require_once __DIR__.'/../../../fake/FakeLogger.php';
require_once __DIR__.'/../../../fake/FakeEntityManager.php';
require_once __DIR__.'/../../../fake/FakeEnvUtils.php';
require_once __DIR__.'/../../common/UnitTestCase.php';

class NewsEndpointTraitConcreteEndpoint {
    use NewsEndpointTrait;

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
 * @covers \NewsEndpointTrait
 */
final class NewsEndpointTraitTest extends UnitTestCase {
    public function testNewsEndpointTrait(): void {
        $endpoint = new NewsEndpointTraitConcreteEndpoint();
        $this->assertSame(false, $endpoint->usesExternalId());

        $field = $endpoint->getEntityDataField(/* allow_null= */ false);
        $this->assertSame(true, $field instanceof FieldTypes\ObjectField);
        $this->assertSame(false, $field->getAllowNull());
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
            'tags',
            'teaser',
            'terminId',
            'title',
        ], $keys);
    }
}
