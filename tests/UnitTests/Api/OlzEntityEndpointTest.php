<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Api;

use Olz\Api\OlzEntityEndpoint;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use PhpTypeScriptApi\Fields\FieldTypes;

class OlzEntityConcreteEndpoint extends OlzEntityEndpoint {
    public $uses_external_id = false;

    public static function getIdent(): string {
        return 'ident';
    }

    public function getResponseField(): FieldTypes\Field {
        return new FieldTypes\ObjectField(['field_structure' => [
            'data' => $this->getEntityDataField(/* allow_null= */ false),
        ]]);
    }

    public function getRequestField(): FieldTypes\Field {
        return new FieldTypes\ObjectField(['field_structure' => [
            'id' => $this->getIdField(/* allow_null= */ true),
        ]]);
    }

    protected function handle(mixed $input): mixed {
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
 *
 * @covers \Olz\Api\OlzEntityEndpoint
 */
final class OlzEntityEndpointTest extends UnitTestCase {
    public function testOlzEntityEndpointInternalId(): void {
        $endpoint = new OlzEntityConcreteEndpoint();
        $endpoint->uses_external_id = false;
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'id' => 5,
        ]);

        $this->assertSame(['data' => 'test'], $result);
    }

    public function testOlzEntityEndpointExternalId(): void {
        $endpoint = new OlzEntityConcreteEndpoint();
        $endpoint->uses_external_id = true;
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'id' => 'external-id',
        ]);

        $this->assertSame(['data' => 'test'], $result);
    }
}
