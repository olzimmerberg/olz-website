<?php

declare(strict_types=1);

require_once __DIR__.'/../../../src/fields/EnumField.php';
require_once __DIR__.'/../common/UnitTestCase.php';

/**
 * @internal
 * @covers \EnumField
 */
final class EnumFieldTest extends UnitTestCase {
    public function testTypeScriptType(): void {
        $field = new EnumField('fake', ['allowed_values' => ['one', 'two', 'three']]);
        $this->assertSame('\'one\'|\'two\'|\'three\'', $field->getTypeScriptType());
    }

    public function testTypeScriptTypeWithNullAllowed(): void {
        $field = new EnumField('fake', ['allowed_values' => ['one', 'two', 'three'], 'allow_null' => true]);
        $this->assertSame('\'one\'|\'two\'|\'three\'|null', $field->getTypeScriptType());
    }

    public function testParse(): void {
        $field = new EnumField('fake', ['allowed_values' => ['one', 'two', 'three']]);
        $this->assertSame('test', $field->parse('test'));
        $this->assertSame(null, $field->parse(''));
        $this->assertSame(null, $field->parse(null));
    }

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
