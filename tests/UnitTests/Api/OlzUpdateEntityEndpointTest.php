<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Api;

use Olz\Api\OlzUpdateEntityEndpoint;
use Olz\Tests\Fake;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use PhpTypeScriptApi\Fields\FieldTypes;

class OlzUpdateEntityConcreteEndpoint extends OlzUpdateEntityEndpoint {
    public $uses_external_id = false;

    public static function getIdent() {
        return 'ident';
    }

    protected function handle($input) {
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
        $endpoint->setLog(Fake\FakeLogger::create());
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
        $endpoint->setLog(Fake\FakeLogger::create());
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
