<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Api;

use Olz\Api\OlzUpdateEntityEndpoint;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use PhpTypeScriptApi\Fields\FieldTypes;

class OlzUpdateEntityConcreteEndpoint extends OlzUpdateEntityEndpoint {
    public bool $uses_external_id = false;

    public static function getIdent(): string {
        return 'ident';
    }

    protected function handle(mixed $input): mixed {
        return [
            'status' => 'OK',
            'id' => $input['id'],
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
 * @covers \Olz\Api\OlzUpdateEntityEndpoint
 */
final class OlzUpdateEntityEndpointTest extends UnitTestCase {
    public function testOlzUpdateEntityEndpointInternalId(): void {
        $endpoint = new OlzUpdateEntityConcreteEndpoint();
        $endpoint->uses_external_id = false;
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'id' => 5,
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

    public function testOlzUpdateEntityEndpointExternalId(): void {
        $endpoint = new OlzUpdateEntityConcreteEndpoint();
        $endpoint->uses_external_id = true;
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'id' => 'external-id',
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
