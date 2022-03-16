<?php

declare(strict_types=1);

require_once __DIR__.'/../../../src/utils/IdUtils.php';
require_once __DIR__.'/../common/UnitTestCase.php';

/**
 * @internal
 * @covers \IdUtils
 */
final class IdUtilsTest extends UnitTestCase {
    public function testToExternalId(): void {
        $id_utils = new IdUtils();
        $this->assertSame('MTIzLUFBQUFBQQ', $id_utils->toExternalId(123));
        $this->assertSame('MTIzLVhZbGkyUQ', $id_utils->toExternalId(123, 'Test'));
        $this->assertSame('NDU2Nzg5MC1BQUFBQUE', $id_utils->toExternalId(4567890));
        $this->assertSame('NDU2Nzg5MC1YWWxpMlE', $id_utils->toExternalId(4567890, 'Test'));
    }

    public function testToExternalIdNonInteger(): void {
        $id_utils = new IdUtils();
        try {
            $id_utils->toExternalId(1.25);
            $this->fail('Error expected');
        } catch (Exception $exc) {
            $this->assertSame('Internal ID must be int', $exc->getMessage());
        }
        try {
            $id_utils->toExternalId('test');
            $this->fail('Error expected');
        } catch (Exception $exc) {
            $this->assertSame('Internal ID must be int', $exc->getMessage());
        }
        // This works, too, though!
        $this->assertSame('MTIzLUFBQUFBQQ', $id_utils->toExternalId('123'));
    }

    public function testToInternalId(): void {
        $id_utils = new IdUtils();
        $this->assertSame(123, $id_utils->toInternalId('MTIzLUFBQUFBQQ'));
        $this->assertSame(123, $id_utils->toInternalId('MTIzLVhZbGkyUQ', 'Test'));
        $this->assertSame(4567890, $id_utils->toInternalId('NDU2Nzg5MC1BQUFBQUE'));
        $this->assertSame(4567890, $id_utils->toInternalId('NDU2Nzg5MC1YWWxpMlE', 'Test'));
    }

    public function testToInternalIdNoMatch(): void {
        $id_utils = new IdUtils();
        try {
            $id_utils->toInternalId(base64_encode('invalid'));
            $this->fail('Error expected');
        } catch (Exception $exc) {
            $this->assertSame('Invalid external ID: No match', $exc->getMessage());
        }
    }

    public function testToInternalIdFalsyId(): void {
        $id_utils = new IdUtils();
        try {
            $id_utils->toInternalId($id_utils->toExternalId(0));
            $this->fail('Error expected');
        } catch (Exception $exc) {
            $this->assertSame('Invalid external ID: Falsy ID', $exc->getMessage());
        }
    }

    public function testToInternalIdTypeMismatch(): void {
        $id_utils = new IdUtils();
        try {
            $id_utils->toInternalId($id_utils->toExternalId(123, 'Test'));
            $this->fail('Error expected');
        } catch (Exception $exc) {
            $this->assertSame('Invalid external ID: Type mismatch', $exc->getMessage());
        }
        try {
            $id_utils->toInternalId($id_utils->toExternalId(123), 'Test');
            $this->fail('Error expected');
        } catch (Exception $exc) {
            $this->assertSame('Invalid external ID: Type mismatch', $exc->getMessage());
        }
        try {
            $id_utils->toInternalId($id_utils->toExternalId(123, 'One'), 'Other');
            $this->fail('Error expected');
        } catch (Exception $exc) {
            $this->assertSame('Invalid external ID: Type mismatch', $exc->getMessage());
        }
    }

    public function testToExternalIdToInternalId(): void {
        $id_utils = new IdUtils();
        $this->assertSame(1, $id_utils->toInternalId($id_utils->toExternalId(1)));
        $this->assertSame(1, $id_utils->toInternalId($id_utils->toExternalId(1, 'a'), 'a'));
    }
}
