<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Api;

use Olz\Api\OlzEditEntityEndpoint;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use PhpTypeScriptApi\Fields\FieldTypes;

class OlzEditEntityConcreteEndpoint extends OlzEditEntityEndpoint {
    public $uses_external_id = false;

    public static function getIdent(): string {
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
 *
 * @covers \Olz\Api\OlzEditEntityEndpoint
 */
final class OlzEditEntityEndpointTest extends UnitTestCase {
    public function testOlzEditEntityEndpointInternalId(): void {
        $endpoint = new OlzEditEntityConcreteEndpoint();
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

    public function testOlzEditEntityEndpointExternalId(): void {
        $endpoint = new OlzEditEntityConcreteEndpoint();
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
