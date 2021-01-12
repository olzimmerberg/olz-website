<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../../../src/fields/StringField.php';

/**
 * @internal
 * @covers \StringField
 */
final class StringFieldTest extends TestCase {
    public function testTypeScriptType(): void {
        $field = new StringField('fake', []);
        $this->assertSame('string', $field->getTypeScriptType());
    }

    public function testTypeScriptTypeWithNullAllowed(): void {
        $field = new StringField('fake', ['allow_null' => true]);
        $this->assertSame('string|null', $field->getTypeScriptType());
    }

    public function testAllowEmptyDefault(): void {
        $field = new StringField('fake', []);
        $this->assertSame(false, $field->getAllowEmpty());
    }

    public function testAllowEmptyTrue(): void {
        $field = new StringField('fake', ['allow_empty' => true]);
        $this->assertSame(true, $field->getAllowEmpty());
    }

    public function testAllowEmptyFalse(): void {
        $field = new StringField('fake', ['allow_empty' => false]);
        $this->assertSame(false, $field->getAllowEmpty());
    }

    public function testMaxLengthDefault(): void {
        $field = new StringField('fake', []);
        $this->assertSame(null, $field->getMaxLength());
    }

    public function testMaxLengthSet(): void {
        $field = new StringField('fake', ['max_length' => 10]);
        $this->assertSame(10, $field->getMaxLength());
    }

    public function testValidatesAllowEmptyTrue(): void {
        $field = new StringField('fake', ['allow_empty' => true]);
        $this->assertSame([], $field->getValidationErrors(''));
        $this->assertSame([], $field->getValidationErrors('test'));
    }

    public function testValidatesAllowEmptyFalse(): void {
        $field = new StringField('fake', ['allow_empty' => false]);
        $this->assertSame(['Feld darf nicht leer sein.'], $field->getValidationErrors(''));
        $this->assertSame([], $field->getValidationErrors('test'));
    }

    public function testValidatesMaxLength(): void {
        $field = new StringField('fake', ['max_length' => 3]);
        $this->assertSame([], $field->getValidationErrors('12'));
        $this->assertSame([], $field->getValidationErrors('123'));
        $this->assertSame(['Wert darf maximal 3 Zeichen lang sein.'], $field->getValidationErrors('1234'));
    }

    public function testValidatesWeirdValues(): void {
        $field = new StringField('fake', []);
        $this->assertSame(['Wert muss eine Zeichenkette sein.'], $field->getValidationErrors(false));
        $this->assertSame(['Wert muss eine Zeichenkette sein.'], $field->getValidationErrors(true));
        $this->assertSame(['Wert muss eine Zeichenkette sein.'], $field->getValidationErrors(1));
        $this->assertSame(['Wert muss eine Zeichenkette sein.'], $field->getValidationErrors([1]));
        $this->assertSame(['Wert muss eine Zeichenkette sein.'], $field->getValidationErrors([1 => 'one']));
    }
}
