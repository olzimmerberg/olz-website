<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Api\ApiObjects;

use Olz\Api\ApiObjects\IsoCountry;
use Olz\Tests\UnitTests\Common\UnitTestCase;

/**
 * @internal
 *
 * @covers \Olz\Api\ApiObjects\IsoCountry
 */
final class IsoCountryTest extends UnitTestCase {
    public function testSerializeIsoCountry(): void {
        $iso_date = new IsoCountry('CH');

        $this->assertSame('CH', $iso_date->data());
    }

    public function testDeserializeIsoCountry(): void {
        $iso_date = IsoCountry::fromData('FR');

        $this->assertSame('FR', $iso_date->data());
    }

    public function testDeserializeIllTypedIsoCountry(): void {
        try {
            IsoCountry::fromData(['ill-typed']);
            $this->fail('Error expected');
        } catch (\Throwable $th) {
            $this->assertSame('IsoCountry must be string', $th->getMessage());
        }
    }

    public function testDeserializeMalformedIsoCountry(): void {
        try {
            IsoCountry::fromData('malformed');
            $this->fail('Error expected');
        } catch (\Throwable $th) {
            $this->assertSame('IsoCountry must be a 2-letter code', $th->getMessage());
        }
    }
}
