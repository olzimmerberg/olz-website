<?php

declare(strict_types=1);

use PhpTypeScriptApi\Fields\FieldTypes;

require_once __DIR__.'/../../../_/api/OlzCreateEntityEndpoint.php';
require_once __DIR__.'/../../fake/FakeLogger.php';
require_once __DIR__.'/../common/UnitTestCase.php';

class OlzCreateEntityConcreteEndpoint extends OlzCreateEntityEndpoint {
    public $uses_external_id = false;

    public static function getIdent() {
        return 'ident';
    }

    protected function handle($input) {
        return [
            'status' => 'OK',
            'id' => $this->uses_external_id ? 'external-id' : 5,
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
 * @covers \OlzCreateEntityEndpoint
 */
final class OlzCreateEntityEndpointTest extends UnitTestCase {
    public function testOlzCreateEntityEndpointInternalId(): void {
        $endpoint = new OlzCreateEntityConcreteEndpoint();
        $endpoint->uses_external_id = false;
        $endpoint->setLogger(FakeLogger::create());
        $result = $endpoint->call([
            'meta' => [
                'ownerUserId' => 1,
                'ownerRoleId' => 3,
                'onOff' => true,
            ],
            'data' => 'some-data',
        ]);
        $this->assertSame([
            'status' => 'OK',
            'id' => 5,
        ], $result);
    }

    public function testOlzCreateEntityEndpointExternalId(): void {
        $endpoint = new OlzCreateEntityConcreteEndpoint();
        $endpoint->uses_external_id = true;
        $endpoint->setLogger(FakeLogger::create());
        $result = $endpoint->call([
            'meta' => [
                'ownerUserId' => 1,
                'ownerRoleId' => 3,
                'onOff' => true,
            ],
            'data' => 'some-data',
        ]);
        $this->assertSame([
            'status' => 'OK',
            'id' => 'external-id',
        ], $result);
    }
}
