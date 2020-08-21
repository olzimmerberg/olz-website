<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../../../src/fields/EnumField.php';

/**
 * @internal
 * @coversNothing
 */
final class EnumFieldTest extends TestCase {
    public function testAllowedValuesDefault(): void {
        $field = new EnumField('fake', []);
        $this->assertSame([], $field->getAllowedValues());
    }

    public function testAllowedValuesSet(): void {
        $field = new EnumField('fake', ['allowed_values' => ['one', 'two', 'three']]);
        $this->assertSame(['one', 'two', 'three'], $field->getAllowedValues());
    }

    public function testValidatesAllowedValue(): void {
        $field = new EnumField('fake', ['allowed_values' => ['one', 'two', 'three']]);
        $this->assertSame([], $field->getValidationErrors('one'));
        $this->assertSame([], $field->getValidationErrors('two'));
        $this->assertSame([], $field->getValidationErrors('three'));
    }

    public function testValidatesDisallowedValue(): void {
        $field = new EnumField('fake', ['allowed_values' => ['one', 'two', 'three']]);
        $this->assertSame(['Feld hat ungültigen Wert.'], $field->getValidationErrors('zero'));
        $this->assertSame(['Feld hat ungültigen Wert.'], $field->getValidationErrors('four'));
        $this->assertSame(['Feld hat ungültigen Wert.'], $field->getValidationErrors(''));
    }

    public function testValidatesWeirdValues(): void {
        $field = new EnumField('fake', []);
        $this->assertSame(['Feld hat ungültigen Wert.'], $field->getValidationErrors(false));
        $this->assertSame(['Feld hat ungültigen Wert.'], $field->getValidationErrors(true));
        $this->assertSame(['Feld hat ungültigen Wert.'], $field->getValidationErrors(1));
        $this->assertSame(['Feld hat ungültigen Wert.'], $field->getValidationErrors('test'));
        $this->assertSame(['Feld hat ungültigen Wert.'], $field->getValidationErrors([1]));
        $this->assertSame(['Feld hat ungültigen Wert.'], $field->getValidationErrors([1 => 'one']));
    }
}
