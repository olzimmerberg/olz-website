<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Api;

use Olz\Api\OlzGetEntityEndpoint;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use PhpTypeScriptApi\Fields\FieldTypes;

class OlzGetEntityConcreteEndpoint extends OlzGetEntityEndpoint {
    public $uses_external_id = false;

    public static function getIdent(): string {
        return 'ident';
    }

    protected function handle(mixed $input): mixed {
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
 *
 * @covers \Olz\Api\OlzGetEntityEndpoint
 */
final class OlzGetEntityEndpointTest extends UnitTestCase {
    public function testOlzGetEntityEndpointInternalId(): void {
        $endpoint = new OlzGetEntityConcreteEndpoint();
        $endpoint->uses_external_id = false;
        $endpoint->runtimeSetup();

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
        $endpoint->runtimeSetup();

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
