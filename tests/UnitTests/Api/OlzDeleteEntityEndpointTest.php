<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Api;

use Olz\Api\OlzDeleteEntityEndpoint;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use PhpTypeScriptApi\Fields\FieldTypes;

class OlzDeleteEntityConcreteEndpoint extends OlzDeleteEntityEndpoint {
    public bool $uses_external_id = false;

    public static function getIdent(): string {
        return 'ident';
    }

    protected function handle(mixed $input): mixed {
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
 *
 * @covers \Olz\Api\OlzDeleteEntityEndpoint
 */
final class OlzDeleteEntityEndpointTest extends UnitTestCase {
    public function testOlzDeleteEntityEndpointInternalId(): void {
        $endpoint = new OlzDeleteEntityConcreteEndpoint();
        $endpoint->uses_external_id = false;
        $endpoint->runtimeSetup();

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
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'id' => 'external-id',
        ]);

        $this->assertSame([
            'status' => 'OK',
        ], $result);
    }
}
