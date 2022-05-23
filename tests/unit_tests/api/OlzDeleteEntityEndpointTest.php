<?php

declare(strict_types=1);

use PhpTypeScriptApi\Fields\FieldTypes;

require_once __DIR__.'/../../../public/_/api/OlzDeleteEntityEndpoint.php';
require_once __DIR__.'/../../fake/FakeLogger.php';
require_once __DIR__.'/../common/UnitTestCase.php';

class OlzDeleteEntityConcreteEndpoint extends OlzDeleteEntityEndpoint {
    public $uses_external_id = false;

    public static function getIdent() {
        return 'ident';
    }

    protected function handle($input) {
        return [
            'status' => 'OK',
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
 * @covers \OlzDeleteEntityEndpoint
 */
final class OlzDeleteEntityEndpointTest extends UnitTestCase {
    public function testOlzDeleteEntityEndpointInternalId(): void {
        $endpoint = new OlzDeleteEntityConcreteEndpoint();
        $endpoint->uses_external_id = false;
        $endpoint->setLogger(FakeLogger::create());
        $result = $endpoint->call([
            'id' => 5,
        ]);
        $this->assertSame([
            'status' => 'OK',
        ], $result);
    }

    public function testOlzDeleteEntityEndpointExternalId(): void {
        $endpoint = new OlzDeleteEntityConcreteEndpoint();
        $endpoint->uses_external_id = true;
        $endpoint->setLogger(FakeLogger::create());
        $result = $endpoint->call([
            'id' => 'external-id',
        ]);
        $this->assertSame([
            'status' => 'OK',
        ], $result);
    }
}
