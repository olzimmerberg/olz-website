<?php

declare(strict_types=1);

require_once __DIR__.'/../../../src/fields/NumberField.php';
require_once __DIR__.'/../common/UnitTestCase.php';

/**
 * @internal
 * @covers \NumberField
 */
final class NumberFieldTest extends UnitTestCase {
    public function testTypeScriptType(): void {
        $field = new NumberField('fake', []);
        $this->assertSame('number', $field->getTypeScriptType());
    }

    public function testTypeScriptTypeWithNullAllowed(): void {
        $field = new NumberField('fake', ['allow_null' => true]);
        $this->assertSame('number|null', $field->getTypeScriptType());
    }

    public function testParse(): void {
        $field = new NumberField('fake', []);
        $this->assertSame(0.0, $field->parse('0'));
        $this->assertSame(0.0, $field->parse('0.0'));
        $this->assertSame(12.34, $field->parse('12.34'));
        $this->assertSame(-12.34, $field->parse('-12.34'));
        $this->assertSame(1234.0, $field->parse('1234'));
        $this->assertSame(-1234.0, $field->parse('-1234'));
        $this->assertSame(null, $field->parse('test'));
        $this->assertSame(null, $field->parse(''));
    }

    public function testMinValueDefault(): void {
        $field = new NumberField('fake', []);
        $this->assertSame(null, $field->getMinValue());
    }

    public function testMinValueSet(): void {
        $field = new NumberField('fake', ['min_value' => 10.3]);
        $this->assertSame(10.3, $field->getMinValue());
    }

    public function testMaxValueDefault(): void {
        $field = new NumberField('fake', []);
        $this->assertSame(null, $field->getMaxValue());
    }

    public function testMaxValueSet(): void {
        $field = new NumberField('fake', ['max_value' => 1.5]);
        $this->assertSame(1.5, $field->getMaxValue());
    }

    public function testValidatesMinValue(): void {
        $field = new NumberField('fake', ['min_value' => 2.5]);
        $this->assertSame(['Wert darf nicht kleiner als 2.5 sein.'], $field->getValidationErrors(2));
        $this->assertSame(['Wert darf nicht kleiner als 2.5 sein.'], $field->getValidationErrors(2.4999));
        $this->assertSame([], $field->getValidationErrors(2.5));
        $this->assertSame([], $field->getValidationErrors(3));
    }

    public function testValidatesMaxValue(): void {
        $field = new NumberField('fake', ['max_value' => 2.5]);
        $this->assertSame([], $field->getValidationErrors(2));
        $this->assertSame([], $field->getValidationErrors(2.5));
        $this->assertSame(['Wert darf nicht grösser als 2.5 sein.'], $field->getValidationErrors(2.5001));
        $this->assertSame(['Wert darf nicht grösser als 2.5 sein.'], $field->getValidationErrors(3));
    }

    public function testValidatesWeirdValues(): void {
        $field = new NumberField('fake', []);
        $this->assertSame(['Wert muss eine Zahl sein.'], $field->getValidationErrors(false));
        $this->assertSame(['Wert muss eine Zahl sein.'], $field->getValidationErrors(true));
        $this->assertSame(['Wert muss eine Zahl sein.'], $field->getValidationErrors('test'));
        $this->assertSame(['Wert muss eine Zahl sein.'], $field->getValidationErrors([1]));
        $this->assertSame(['Wert muss eine Zahl sein.'], $field->getValidationErrors([1 => 'one']));
    }
}
