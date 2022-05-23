<?php

declare(strict_types=1);

use PhpTypeScriptApi\Fields\FieldTypes;

require_once __DIR__.'/../../../public/_/api/OlzEntityEndpoint.php';
require_once __DIR__.'/../../fake/FakeLogger.php';
require_once __DIR__.'/../common/UnitTestCase.php';

class OlzEntityConcreteEndpoint extends OlzEntityEndpoint {
    public $uses_external_id = false;

    public static function getIdent() {
        return 'ident';
    }

    public function getResponseField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'data' => $this->getEntityDataField(/* allow_null= */ false),
        ]]);
    }

    public function getRequestField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'id' => $this->getIdField(/* allow_null= */ true),
        ]]);
    }

    protected function handle($input) {
        return ['data' => 'test'];
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
 * @covers \OlzEntityEndpoint
 */
final class OlzEntityEndpointTest extends UnitTestCase {
    public function testOlzEntityEndpointInternalId(): void {
        $endpoint = new OlzEntityConcreteEndpoint();
        $endpoint->uses_external_id = false;
        $endpoint->setLogger(FakeLogger::create());
        $result = $endpoint->call([
            'id' => 5,
        ]);
        $this->assertSame(['data' => 'test'], $result);
    }

    public function testOlzEntityEndpointExternalId(): void {
        $endpoint = new OlzEntityConcreteEndpoint();
        $endpoint->uses_external_id = true;
        $endpoint->setLogger(FakeLogger::create());
        $result = $endpoint->call([
            'id' => 'external-id',
        ]);
        $this->assertSame(['data' => 'test'], $result);
    }
}
