<?php

declare(strict_types=1);

require_once __DIR__.'/../../../src/fields/IntegerField.php';
require_once __DIR__.'/../common/UnitTestCase.php';

/**
 * @internal
 * @covers \IntegerField
 */
final class IntegerFieldTest extends UnitTestCase {
    public function testTypeScriptType(): void {
        $field = new IntegerField('fake', []);
        $this->assertSame('number', $field->getTypeScriptType());
    }

    public function testTypeScriptTypeWithNullAllowed(): void {
        $field = new IntegerField('fake', ['allow_null' => true]);
        $this->assertSame('number|null', $field->getTypeScriptType());
    }

    public function testParse(): void {
        $field = new IntegerField('fake', []);
        $this->assertSame(0, $field->parse('0'));
        $this->assertSame(1234, $field->parse('1234'));
        $this->assertSame(-1234, $field->parse('-1234'));
        $this->assertSame(null, $field->parse(''));
        try {
            $field->parse('test');
            $this->fail('Error expected');
        } catch (\Exception $exc) {
            $this->assertSame("Unlesbare Ganzzahl: 'test'", $exc->getMessage());
        }
    }

    public function testMinValueDefault(): void {
        $field = new IntegerField('fake', []);
        $this->assertSame(null, $field->getMinValue());
    }

    public function testMinValueSet(): void {
        $field = new IntegerField('fake', ['min_value' => 10]);
        $this->assertSame(10, $field->getMinValue());
    }

    public function testMaxValueDefault(): void {
        $field = new IntegerField('fake', []);
        $this->assertSame(null, $field->getMaxValue());
    }

    public function testMaxValueSet(): void {
        $field = new IntegerField('fake', ['max_value' => 10]);
        $this->assertSame(10, $field->getMaxValue());
    }

    public function testValidatesMinValue(): void {
        $field = new IntegerField('fake', ['min_value' => 3]);
        $this->assertSame(['Wert darf nicht kleiner als 3 sein.'], $field->getValidationErrors(2));
        $this->assertSame([], $field->getValidationErrors(3));
        $this->assertSame([], $field->getValidationErrors(4));
    }

    public function testValidatesMaxValue(): void {
        $field = new IntegerField('fake', ['max_value' => 3]);
        $this->assertSame([], $field->getValidationErrors(2));
        $this->assertSame([], $field->getValidationErrors(3));
        $this->assertSame(['Wert darf nicht grösser als 3 sein.'], $field->getValidationErrors(4));
    }

    public function testValidatesWeirdValues(): void {
        $field = new IntegerField('fake', []);
        $this->assertSame(['Wert muss eine Zahl sein.', 'Wert muss eine Ganzzahl sein.'], $field->getValidationErrors(false));
        $this->assertSame(['Wert muss eine Zahl sein.', 'Wert muss eine Ganzzahl sein.'], $field->getValidationErrors(true));
        $this->assertSame(['Wert muss eine Zahl sein.', 'Wert muss eine Ganzzahl sein.'], $field->getValidationErrors('test'));
        $this->assertSame(['Wert muss eine Zahl sein.', 'Wert muss eine Ganzzahl sein.'], $field->getValidationErrors([1]));
        $this->assertSame(['Wert muss eine Zahl sein.', 'Wert muss eine Ganzzahl sein.'], $field->getValidationErrors([1 => 'one']));
    }
}
