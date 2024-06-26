<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Api;

use Olz\Api\OlzCreateEntityEndpoint;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use PhpTypeScriptApi\Fields\FieldTypes;

class OlzCreateEntityConcreteEndpoint extends OlzCreateEntityEndpoint {
    public bool $uses_external_id = false;

    public static function getIdent(): string {
        return 'ident';
    }

    protected function handle(mixed $input): mixed {
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
 *
 * @covers \Olz\Api\OlzCreateEntityEndpoint
 */
final class OlzCreateEntityEndpointTest extends UnitTestCase {
    public function testOlzCreateEntityEndpointInternalId(): void {
        $endpoint = new OlzCreateEntityConcreteEndpoint();
        $endpoint->uses_external_id = false;
        $endpoint->runtimeSetup();

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
        $endpoint->runtimeSetup();

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
