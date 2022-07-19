<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Olz\Tests\Fake\FakeEnvUtils;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\IdUtils;

/**
 * @internal
 * @coversNothing
 */
class IdUtilsIdUtilsForTest extends IdUtils {
    public function testOnlySerializeId($internal_id, $type) {
        return $this->serializeId($internal_id, $type);
    }

    public function testOnlyEncryptId($serialized_id) {
        return $this->encryptId($serialized_id);
    }

    public function testOnlyDecryptId($encrypted_id) {
        return $this->decryptId($encrypted_id);
    }

    public function testOnlyDeserializeId($serialized_id, $type) {
        return $this->deserializeId($serialized_id, $type);
    }

    public function testOnlyCrc16($data) {
        return $this->crc16($data);
    }

    public function testOnlyTrimmedBase64Encode($data) {
        return $this->trimmedBase64Encode($data);
    }
}

/**
 * @internal
 * @covers \Olz\Utils\IdUtils
 */
final class IdUtilsTest extends UnitTestCase {
    public function testToExternalId(): void {
        $id_utils = new IdUtilsIdUtilsForTest();
        $id_utils->setEnvUtils(new FakeEnvUtils());
        $max_id = pow(2, 40) - 1;
        $this->assertSame('BZciFnEYIjk', $id_utils->toExternalId(123));
        $this->assertSame('ktdWZ4i54iI', $id_utils->toExternalId(123, 'Test'));
        $this->assertSame('FZ9PI6CQffk', $id_utils->toExternalId($max_id));
        $this->assertSame('hm1XiqEYYmI', $id_utils->toExternalId($max_id, 'Test'));
    }

    public function testToExternalIdNonInteger(): void {
        $id_utils = new IdUtilsIdUtilsForTest();
        $id_utils->setEnvUtils(new FakeEnvUtils());
        try {
            $id_utils->toExternalId(1.25);
            $this->fail('Error expected');
        } catch (\Exception $exc) {
            $this->assertSame('Internal ID must be int', $exc->getMessage());
        }
        try {
            $id_utils->toExternalId('test');
            $this->fail('Error expected');
        } catch (\Exception $exc) {
            $this->assertSame('Internal ID must be int', $exc->getMessage());
        }
        // This works, too, though!
        $this->assertSame('BZciFnEYIjk', $id_utils->toExternalId('123'));
    }

    // public function testGetNullPrefixedCRC(): void {
    //     $id_utils = new IdUtilsIdUtilsForTest();
    //     $id_utils->setEnvUtils(new FakeEnvUtils());
    //     $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    //     $chars_len = strlen($chars);
    //     for ($len=1; $len<5; $len++) {
    //         for ($s=0; $s<pow($chars_len, $len); $s++) {
    //             $str = '';
    //             for ($i=0; $i<$len; $i++) {
    //                 $chr = ($s / pow($chars_len, $i)) % $chars_len;
    //                 $str .= $chars[$chr];
    //             }
    //             $serialization = bin2hex($id_utils->testOnlySerializeId(123, $str));
    //             if (substr($serialization, 0, 4) === '0000') {
    //                 $this->assertSame('', $str);
    //                 return;
    //             }
    //         }
    //     }
    // }

    public function testSerializeId(): void {
        $id_utils = new IdUtilsIdUtilsForTest();
        $id_utils->setEnvUtils(new FakeEnvUtils());
        $max_id = pow(2, 40) - 1;
        $this->assertSame('0c5e000000007b', bin2hex(
            $id_utils->testOnlySerializeId(123, 'h')));
        $this->assertSame('006f000000007b', bin2hex(
            $id_utils->testOnlySerializeId(123, '2p')));
        $this->assertSame('0003000000007b', bin2hex(
            $id_utils->testOnlySerializeId(123, 'HM')));
        $this->assertSame('0000000000007b', bin2hex(
            $id_utils->testOnlySerializeId(123, 'JMfa')));
        $this->assertSame('ffff000000007b', bin2hex(
            $id_utils->testOnlySerializeId(123, '')));
        $this->assertSame('2888000000007b', bin2hex(
            $id_utils->testOnlySerializeId(123, 'Test')));
        $this->assertSame('ffffffffffffff', bin2hex(
            $id_utils->testOnlySerializeId($max_id, '')));
        $this->assertSame('2888ffffffffff', bin2hex(
            $id_utils->testOnlySerializeId($max_id, 'Test')));
    }

    public function testSerializeIdNonInteger(): void {
        $id_utils = new IdUtilsIdUtilsForTest();
        $id_utils->setEnvUtils(new FakeEnvUtils());
        try {
            $id_utils->testOnlySerializeId(1.25, '');
            $this->fail('Error expected');
        } catch (\Exception $exc) {
            $this->assertSame('Internal ID must be int', $exc->getMessage());
        }
        try {
            $id_utils->testOnlySerializeId('test', '');
            $this->fail('Error expected');
        } catch (\Exception $exc) {
            $this->assertSame('Internal ID must be int', $exc->getMessage());
        }
        // This works, too, though!
        $this->assertSame('ffff000000007b', bin2hex($id_utils->testOnlySerializeId('123', '')));
    }

    public function testSerializeIdNegative(): void {
        $id_utils = new IdUtilsIdUtilsForTest();
        $id_utils->setEnvUtils(new FakeEnvUtils());
        try {
            $id_utils->testOnlySerializeId(-1, '');
            $this->fail('Error expected');
        } catch (\Exception $exc) {
            $this->assertSame('Internal ID must be positive', $exc->getMessage());
        }
    }

    public function testSerializeIdTooLarge(): void {
        $id_utils = new IdUtilsIdUtilsForTest();
        $id_utils->setEnvUtils(new FakeEnvUtils());
        $max_id = pow(2, 40) - 1;
        try {
            $id_utils->testOnlySerializeId($max_id + 1, '');
            $this->fail('Error expected');
        } catch (\Exception $exc) {
            $this->assertSame('Internal ID must be at most 40 bits', $exc->getMessage());
        }
    }

    public function testEncryptId(): void {
        $id_utils = new IdUtilsIdUtilsForTest();
        $id_utils->setEnvUtils(new FakeEnvUtils());
        $this->assertSame('g8US8+kiV4w', $id_utils->testOnlyEncryptId(''));
        $this->assertSame('aEDMWECsRcA', $id_utils->testOnlyEncryptId('test'));
    }

    public function testToInternalId(): void {
        $id_utils = new IdUtilsIdUtilsForTest();
        $id_utils->setEnvUtils(new FakeEnvUtils());
        $max_id = pow(2, 40) - 1;
        $this->assertSame(123, $id_utils->toInternalId('BZciFnEYIjk'));
        $this->assertSame(123, $id_utils->toInternalId('ktdWZ4i54iI', 'Test'));
        $this->assertSame($max_id, $id_utils->toInternalId('FZ9PI6CQffk'));
        $this->assertSame($max_id, $id_utils->toInternalId('hm1XiqEYYmI', 'Test'));
    }

    public function testToInternalIdTypeMismatch(): void {
        $id_utils = new IdUtilsIdUtilsForTest();
        $id_utils->setEnvUtils(new FakeEnvUtils());
        try {
            $id_utils->toInternalId($id_utils->toExternalId(123, 'Test'));
            $this->fail('Error expected');
        } catch (\Exception $exc) {
            $this->assertSame('Invalid serialized ID: Type mismatch', $exc->getMessage());
        }
        try {
            $id_utils->toInternalId($id_utils->toExternalId(123), 'Test');
            $this->fail('Error expected');
        } catch (\Exception $exc) {
            $this->assertSame('Invalid serialized ID: Type mismatch', $exc->getMessage());
        }
        try {
            $id_utils->toInternalId($id_utils->toExternalId(123, 'One'), 'Other');
            $this->fail('Error expected');
        } catch (\Exception $exc) {
            $this->assertSame('Invalid serialized ID: Type mismatch', $exc->getMessage());
        }
    }

    public function testDecryptId(): void {
        $id_utils = new IdUtilsIdUtilsForTest();
        $id_utils->setEnvUtils(new FakeEnvUtils());
        $this->assertSame('', $id_utils->testOnlyDecryptId('g8US8+kiV4w'));
        $this->assertSame('test', $id_utils->testOnlyDecryptId('aEDMWECsRcA'));
    }

    public function testDeserializeId(): void {
        $id_utils = new IdUtilsIdUtilsForTest();
        $id_utils->setEnvUtils(new FakeEnvUtils());
        $max_id = pow(2, 40) - 1;
        $this->assertSame(123, $id_utils->testOnlyDeserializeId(
            hex2bin('0c5e000000007b'), 'h'));
        $this->assertSame(123, $id_utils->testOnlyDeserializeId(
            hex2bin('006f000000007b'), '2p'));
        $this->assertSame(123, $id_utils->testOnlyDeserializeId(
            hex2bin('0003000000007b'), 'HM'));
        $this->assertSame(123, $id_utils->testOnlyDeserializeId(
            hex2bin('0000000000007b'), 'JMfa'));
        $this->assertSame(123, $id_utils->testOnlyDeserializeId(
            hex2bin('ffff000000007b'), ''));
        $this->assertSame(123, $id_utils->testOnlyDeserializeId(
            hex2bin('2888000000007b'), 'Test'));
        $this->assertSame($max_id, $id_utils->testOnlyDeserializeId(
            hex2bin('ffffffffffffff'), ''));
        $this->assertSame($max_id, $id_utils->testOnlyDeserializeId(
            hex2bin('2888ffffffffff'), 'Test'));
    }

    public function testToExternalIdToInternalId(): void {
        $id_utils = new IdUtilsIdUtilsForTest();
        $id_utils->setEnvUtils(new FakeEnvUtils());
        $this->assertSame(1, $id_utils->toInternalId($id_utils->toExternalId(1)));
        $this->assertSame(1, $id_utils->toInternalId($id_utils->toExternalId(1, 'a'), 'a'));
    }

    public function testSerializeIdDeserializeId(): void {
        $id_utils = new IdUtilsIdUtilsForTest();
        $id_utils->setEnvUtils(new FakeEnvUtils());
        $max_id = pow(2, 40) - 1;
        $this->assertSame(123, $id_utils->testOnlyDeserializeId(
            $id_utils->testOnlySerializeId(123, 'h'), 'h'));
        $this->assertSame(123, $id_utils->testOnlyDeserializeId(
            $id_utils->testOnlySerializeId(123, '2p'), '2p'));
        $this->assertSame(123, $id_utils->testOnlyDeserializeId(
            $id_utils->testOnlySerializeId(123, 'HM'), 'HM'));
        $this->assertSame(123, $id_utils->testOnlyDeserializeId(
            $id_utils->testOnlySerializeId(123, 'JMfa'), 'JMfa'));
        $this->assertSame(123, $id_utils->testOnlyDeserializeId(
            $id_utils->testOnlySerializeId(123, ''), ''));
        $this->assertSame(123, $id_utils->testOnlyDeserializeId(
            $id_utils->testOnlySerializeId(123, 'Test'), 'Test'));
        $this->assertSame($max_id, $id_utils->testOnlyDeserializeId(
            $id_utils->testOnlySerializeId($max_id, ''), ''));
        $this->assertSame($max_id, $id_utils->testOnlyDeserializeId(
            $id_utils->testOnlySerializeId($max_id, 'Test'), 'Test'));
    }

    public function testCrc16(): void {
        $id_utils = new IdUtilsIdUtilsForTest();
        $id_utils->setEnvUtils(new FakeEnvUtils());
        $this->assertSame('1fc6', dechex($id_utils->testOnlyCrc16('test')));
        $this->assertSame('ffff', dechex($id_utils->testOnlyCrc16('')));
    }

    public function testTrimmedBase64Encode(): void {
        $id_utils = new IdUtilsIdUtilsForTest();
        $id_utils->setEnvUtils(new FakeEnvUtils());
        $this->assertSame('dGVzdA', $id_utils->testOnlyTrimmedBase64Encode('test'));
        $this->assertSame('', $id_utils->testOnlyTrimmedBase64Encode(''));
    }
}
