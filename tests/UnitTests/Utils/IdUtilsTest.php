<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\IdUtils;

class TestOnlyIdUtils extends IdUtils {
    public function testOnlySerializeId(int $internal_id, string $type): string {
        return $this->serializeId($internal_id, $type);
    }

    public function testOnlyEncryptId(string $serialized_id): string {
        return $this->encryptId($serialized_id);
    }

    public function testOnlyDecryptId(string $encrypted_id): ?string {
        return $this->decryptId($encrypted_id);
    }

    public function testOnlyDeserializeId(string $serialized_id, string $type): int {
        return $this->deserializeId($serialized_id, $type);
    }

    public function testOnlyCrc16(string $data): int {
        return $this->crc16($data);
    }
}

/**
 * @internal
 *
 * @covers \Olz\Utils\IdUtils
 */
final class IdUtilsTest extends UnitTestCase {
    public function testToExternalId(): void {
        $id_utils = new TestOnlyIdUtils();
        $max_id = intval(pow(2, 40) - 1);
        $this->assertSame('9AUh0IsXMgc', $id_utils->toExternalId(123));
        $this->assertSame('KvwHIC1COIo', $id_utils->toExternalId(123, 'Test'));
        // Contains an underscore
        $this->assertSame('YI_noV3FCIs', $id_utils->toExternalId($max_id));
        $this->assertSame('9eNNm3rHqQQ', $id_utils->toExternalId($max_id, 'Test'));
        // Contains a dash
        $this->assertSame('QOcb-mBqO90', $id_utils->toExternalId(1, 'Test'));
    }

    public function testSerializeId(): void {
        $id_utils = new TestOnlyIdUtils();
        $max_id = intval(pow(2, 40) - 1);
        $this->assertSame('0c5e000000007b', bin2hex(
            $id_utils->testOnlySerializeId(123, 'h')
        ));
        $this->assertSame('006f000000007b', bin2hex(
            $id_utils->testOnlySerializeId(123, '2p')
        ));
        $this->assertSame('0003000000007b', bin2hex(
            $id_utils->testOnlySerializeId(123, 'HM')
        ));
        $this->assertSame('0000000000007b', bin2hex(
            $id_utils->testOnlySerializeId(123, 'JMfa')
        ));
        $this->assertSame('ffff000000007b', bin2hex(
            $id_utils->testOnlySerializeId(123, '')
        ));
        $this->assertSame('2888000000007b', bin2hex(
            $id_utils->testOnlySerializeId(123, 'Test')
        ));
        $this->assertSame('ffffffffffffff', bin2hex(
            $id_utils->testOnlySerializeId($max_id, '')
        ));
        $this->assertSame('2888ffffffffff', bin2hex(
            $id_utils->testOnlySerializeId($max_id, 'Test')
        ));
    }

    public function testSerializeIdNegative(): void {
        $id_utils = new TestOnlyIdUtils();
        try {
            $id_utils->testOnlySerializeId(-1, '');
            $this->fail('Error expected');
        } catch (\Exception $exc) {
            $this->assertSame('Internal ID must be positive', $exc->getMessage());
        }
    }

    public function testSerializeIdTooLarge(): void {
        $id_utils = new TestOnlyIdUtils();
        $max_id = intval(pow(2, 40) - 1);
        try {
            $id_utils->testOnlySerializeId($max_id + 1, '');
            $this->fail('Error expected');
        } catch (\Exception $exc) {
            $this->assertSame('Internal ID must be at most 40 bits', $exc->getMessage());
        }
    }

    public function testEncryptId(): void {
        $id_utils = new TestOnlyIdUtils();
        $this->assertSame('w0_JgCePlCI', $id_utils->testOnlyEncryptId(''));
        $this->assertSame('KNYgqjTkR5o', $id_utils->testOnlyEncryptId('test'));

        // Those should not show any similarity!
        $this->assertSame(
            'q8JuUMqRC60',
            $id_utils->testOnlyEncryptId(hex2bin('0123456789abcd') ?: ''),
        );
        $this->assertSame(
            '0oqhJ3PIdS8',
            $id_utils->testOnlyEncryptId(hex2bin('0123456789abce') ?: ''),
        );
        $this->assertSame(
            'L6sX3DLEtcY',
            $id_utils->testOnlyEncryptId(hex2bin('8123456789abcd') ?: ''),
        );
    }

    public function testToInternalId(): void {
        $id_utils = new TestOnlyIdUtils();
        $max_id = intval(pow(2, 40) - 1);
        $this->assertSame(123, $id_utils->toInternalId('9AUh0IsXMgc'));
        $this->assertSame(123, $id_utils->toInternalId('KvwHIC1COIo', 'Test'));
        // Contains an underscore
        $this->assertSame($max_id, $id_utils->toInternalId('YI_noV3FCIs'));
        $this->assertSame($max_id, $id_utils->toInternalId('9eNNm3rHqQQ', 'Test'));
        // Contains a dash
        $this->assertSame(1, $id_utils->toInternalId('QOcb-mBqO90', 'Test'));

        // Legacy; pure base64
        $this->assertSame($max_id, $id_utils->toInternalId('YI/noV3FCIs'));
    }

    public function testToInternalIdTypeMismatch(): void {
        $id_utils = new TestOnlyIdUtils();
        try {
            $id_utils->toInternalId($id_utils->toExternalId(123, 'Test'));
            $this->fail('Error expected');
        } catch (\Exception $exc) {
            $this->assertSame(
                'Invalid serialized ID: Type mismatch 2888 vs. ffff',
                $exc->getMessage(),
            );
        }
        try {
            $id_utils->toInternalId($id_utils->toExternalId(123), 'Test');
            $this->fail('Error expected');
        } catch (\Exception $exc) {
            $this->assertSame(
                'Invalid serialized ID: Type mismatch ffff vs. 2888',
                $exc->getMessage(),
            );
        }
        try {
            $id_utils->toInternalId($id_utils->toExternalId(123, 'One'), 'Other');
            $this->fail('Error expected');
        } catch (\Exception $exc) {
            $this->assertSame(
                'Invalid serialized ID: Type mismatch e926 vs. 75d9',
                $exc->getMessage(),
            );
        }
    }

    public function testDecryptId(): void {
        $id_utils = new TestOnlyIdUtils();
        $this->assertSame('', $id_utils->testOnlyDecryptId('w0/JgCePlCI'));
        try {
            $id_utils->testOnlyDecryptId('invalid');
            $this->fail('Error expected');
        } catch (\Exception $exc) {
            $this->assertSame('IdUtils.php:*** Could not decrypt ID: invalid', $exc->getMessage());
        }
        $this->assertSame('test', $id_utils->testOnlyDecryptId('KNYgqjTkR5o'));
    }

    public function testDeserializeId(): void {
        $id_utils = new TestOnlyIdUtils();
        $max_id = intval(pow(2, 40) - 1);
        $this->assertSame(123, $id_utils->testOnlyDeserializeId(
            hex2bin('0c5e000000007b') ?: '',
            'h'
        ));
        $this->assertSame(123, $id_utils->testOnlyDeserializeId(
            hex2bin('006f000000007b') ?: '',
            '2p'
        ));
        $this->assertSame(123, $id_utils->testOnlyDeserializeId(
            hex2bin('0003000000007b') ?: '',
            'HM'
        ));
        $this->assertSame(123, $id_utils->testOnlyDeserializeId(
            hex2bin('0000000000007b') ?: '',
            'JMfa'
        ));
        $this->assertSame(123, $id_utils->testOnlyDeserializeId(
            hex2bin('ffff000000007b') ?: '',
            ''
        ));
        $this->assertSame(123, $id_utils->testOnlyDeserializeId(
            hex2bin('2888000000007b') ?: '',
            'Test'
        ));
        $this->assertSame($max_id, $id_utils->testOnlyDeserializeId(
            hex2bin('ffffffffffffff') ?: '',
            ''
        ));
        $this->assertSame($max_id, $id_utils->testOnlyDeserializeId(
            hex2bin('2888ffffffffff') ?: '',
            'Test'
        ));
    }

    public function testToExternalIdToInternalId(): void {
        $id_utils = new TestOnlyIdUtils();
        $this->assertSame(1, $id_utils->toInternalId($id_utils->toExternalId(1)));
        $this->assertSame(1, $id_utils->toInternalId($id_utils->toExternalId(1, 'a'), 'a'));
    }

    public function testSerializeIdDeserializeId(): void {
        $id_utils = new TestOnlyIdUtils();
        $max_id = intval(pow(2, 40) - 1);
        $this->assertSame(123, $id_utils->testOnlyDeserializeId(
            $id_utils->testOnlySerializeId(123, 'h'),
            'h'
        ));
        $this->assertSame(123, $id_utils->testOnlyDeserializeId(
            $id_utils->testOnlySerializeId(123, '2p'),
            '2p'
        ));
        $this->assertSame(123, $id_utils->testOnlyDeserializeId(
            $id_utils->testOnlySerializeId(123, 'HM'),
            'HM'
        ));
        $this->assertSame(123, $id_utils->testOnlyDeserializeId(
            $id_utils->testOnlySerializeId(123, 'JMfa'),
            'JMfa'
        ));
        $this->assertSame(123, $id_utils->testOnlyDeserializeId(
            $id_utils->testOnlySerializeId(123, ''),
            ''
        ));
        $this->assertSame(123, $id_utils->testOnlyDeserializeId(
            $id_utils->testOnlySerializeId(123, 'Test'),
            'Test'
        ));
        $this->assertSame($max_id, $id_utils->testOnlyDeserializeId(
            $id_utils->testOnlySerializeId($max_id, ''),
            ''
        ));
        $this->assertSame($max_id, $id_utils->testOnlyDeserializeId(
            $id_utils->testOnlySerializeId($max_id, 'Test'),
            'Test'
        ));
    }

    public function testCrc16(): void {
        $id_utils = new TestOnlyIdUtils();
        $this->assertSame('1fc6', dechex($id_utils->testOnlyCrc16('test')));
        $this->assertSame('ffff', dechex($id_utils->testOnlyCrc16('')));
    }
}
