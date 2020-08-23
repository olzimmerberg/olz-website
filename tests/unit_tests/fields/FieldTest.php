<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../../../src/fields/Field.php';

/**
 * @internal
 * @coversNothing
 */
final class FieldTest extends TestCase {
    public function testAllowNullDefault(): void {
        $field = new Field('fake', []);
        $this->assertSame(false, $field->getAllowNull());
    }

    public function testAllowNullTrue(): void {
        $field = new Field('fake', ['allow_null' => true]);
        $this->assertSame(true, $field->getAllowNull());
    }

    public function testAllowNullFalse(): void {
        $field = new Field('fake', ['allow_null' => false]);
        $this->assertSame(false, $field->getAllowNull());
    }

    public function testDefaultValueDefault(): void {
        $field = new Field('fake', []);
        $this->assertSame(null, $field->getDefaultValue());
    }

    public function testDefaultValueSet(): void {
        $field = new Field('fake', ['default_value' => 'test']);
        $this->assertSame('test', $field->getDefaultValue());
    }

    public function testValidatesAllowNullTrue(): void {
        $field = new Field('fake', ['allow_null' => true]);
        $this->assertSame([], $field->getValidationErrors(null));
        $this->assertSame([], $field->getValidationErrors('test'));
    }

    public function testValidatesAllowNullFalse(): void {
        $field = new Field('fake', ['allow_null' => false]);
        $this->assertSame(['Feld darf nicht leer sein.'], $field->getValidationErrors(null));
        $this->assertSame([], $field->getValidationErrors('test'));
    }

    public function testValidatesDefaultValueDefault(): void {
        $field = new Field('fake', ['allow_null' => false]);
        $this->assertSame(['Feld darf nicht leer sein.'], $field->getValidationErrors(null));
        $this->assertSame([], $field->getValidationErrors('test'));
    }

    public function testValidatesDefaultValueSet(): void {
        $field = new Field('fake', ['allow_null' => false, 'default_value' => 'test']);
        $this->assertSame([], $field->getValidationErrors(null));
        $this->assertSame([], $field->getValidationErrors('test'));
    }
}
