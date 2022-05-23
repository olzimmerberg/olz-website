<?php

declare(strict_types=1);

use PhpTypeScriptApi\Fields\FieldTypes;

require_once __DIR__.'/../../../public/_/api/OlzGetEntityEndpoint.php';
require_once __DIR__.'/../../fake/FakeLogger.php';
require_once __DIR__.'/../common/UnitTestCase.php';

class OlzGetEntityConcreteEndpoint extends OlzGetEntityEndpoint {
    public $uses_external_id = false;

    public static function getIdent() {
        return 'ident';
    }

    protected function handle($input) {
        return [
            'id' => $input['id'],
            'meta' => [
                'ownerUserId' => 1,
                'ownerRoleId' => 3,
                'onOff' => true,
            ],
            'data' => 'some-data',
        ];
    }

    public function usesExternalId(): bool {
        return $this->uses_external_id;
    }

    public function getEntityDataField(bool $allow_null): FieldTypes\Field {
        return new FieldTypes\Field(['export_as' => 'TestField']);
    }
}

/**
 * @internal
 * @covers \OlzGetEntityEndpoint
 */
final class OlzGetEntityEndpointTest extends UnitTestCase {
    public function testOlzGetEntityEndpointInternalId(): void {
        $endpoint = new OlzGetEntityConcreteEndpoint();
        $endpoint->uses_external_id = false;
        $endpoint->setLogger(FakeLogger::create());
        $result = $endpoint->call([
            'id' => 5,
        ]);
        $this->assertSame([
            'id' => 5,
            'meta' => [
                'ownerUserId' => 1,
                'ownerRoleId' => 3,
                'onOff' => true,
            ],
            'data' => 'some-data',
        ], $result);
    }

    public function testOlzGetEntityEndpointExternalId(): void {
        $endpoint = new OlzGetEntityConcreteEndpoint();
        $endpoint->uses_external_id = true;
        $endpoint->setLogger(FakeLogger::create());
        $result = $endpoint->call([
            'id' => 'external-id',
        ]);
        $this->assertSame([
            'id' => 'external-id',
            'meta' => [
                'ownerUserId' => 1,
                'ownerRoleId' => 3,
                'onOff' => true,
            ],
            'data' => 'some-data',
        ], $result);
    }
}
