<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../../../src/fields/IntegerField.php';

/**
 * @internal
 * @covers \IntegerField
 */
final class IntegerFieldTest extends TestCase {
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
