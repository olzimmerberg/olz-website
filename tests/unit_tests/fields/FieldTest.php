<?php

declare(strict_types=1);

require_once __DIR__.'/../../../src/fields/Field.php';
require_once __DIR__.'/../common/UnitTestCase.php';

/**
 * @internal
 * @covers \Field
 */
final class FieldTest extends UnitTestCase {
    public function testTypeScriptType(): void {
        $field = new Field('fake', []);
        $this->assertSame('any', $field->getTypeScriptType());
    }

    public function testTypeScriptTypeWithNullAllowed(): void {
        $field = new Field('fake', ['allow_null' => true]);
        $this->assertSame('any', $field->getTypeScriptType());
    }

    public function testParse(): void {
        $field = new Field('fake', []);
        $this->assertSame('test', $field->parse('test'));
        $this->assertSame(null, $field->parse(''));
        $this->assertSame(null, $field->parse(null));
    }

    public function testGetId(): void {
        $field = new Field('fake', []);
        $this->assertSame('fake', $field->getId());
    }

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
