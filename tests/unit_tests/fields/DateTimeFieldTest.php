<?php

declare(strict_types=1);

require_once __DIR__.'/../../../src/fields/DateTimeField.php';
require_once __DIR__.'/../common/UnitTestCase.php';

/**
 * @internal
 * @covers \DateTimeField
 */
final class DateTimeFieldTest extends UnitTestCase {
    public function testTypeScriptType(): void {
        $field = new DateTimeField('fake', []);
        $this->assertSame('string', $field->getTypeScriptType());
    }

    public function testTypeScriptTypeWithNullAllowed(): void {
        $field = new DateTimeField('fake', ['allow_null' => true]);
        $this->assertSame('string|null', $field->getTypeScriptType());
    }

    public function testParse(): void {
        $field = new DateTimeField('fake', []);
        $this->assertSame('test', $field->parse('test'));
        $this->assertSame(null, $field->parse(''));
        $this->assertSame(null, $field->parse(null));
    }

    public function testMinValueDefault(): void {
        $field = new DateTimeField('fake', []);
        $this->assertSame(null, $field->getMinValue());
    }

    public function testMinValueSet(): void {
        $field = new DateTimeField('fake', ['min_value' => '2020-03-13']);
        $this->assertSame('2020-03-13', $field->getMinValue());
    }

    public function testMaxValueDefault(): void {
        $field = new DateTimeField('fake', []);
        $this->assertSame(null, $field->getMaxValue());
    }

    public function testMaxValueSet(): void {
        $field = new DateTimeField('fake', ['max_value' => '2020-03-13']);
        $this->assertSame('2020-03-13', $field->getMaxValue());
    }

    public function testValidatesMinValue(): void {
        $field = new DateTimeField('fake', ['min_value' => '2020-03-13 19:30:00']);
        $this->assertSame(['Wert darf nicht kleiner als 2020-03-13 19:30:00 sein.'], $field->getValidationErrors('2020-03-12 19:29:59'));
        $this->assertSame([], $field->getValidationErrors('2020-03-13 19:30:00'));
        $this->assertSame([], $field->getValidationErrors('2020-03-13 19:30:01'));
    }

    public function testValidatesMaxValue(): void {
        $field = new DateTimeField('fake', ['max_value' => '2020-03-13 19:30:00']);
        $this->assertSame([], $field->getValidationErrors('2020-03-13 19:29:59'));
        $this->assertSame([], $field->getValidationErrors('2020-03-13 19:30:00'));
        $this->assertSame(['Wert darf nicht grösser als 2020-03-13 19:30:00 sein.'], $field->getValidationErrors('2020-03-13 19:30:01'));
    }

    public function testValidatesWeirdValues(): void {
        $field = new DateTimeField('fake', []);
        $this->assertSame(['Wert muss eine Zeichenkette sein.'], $field->getValidationErrors(false));
        $this->assertSame(['Wert muss eine Zeichenkette sein.'], $field->getValidationErrors(true));
        $this->assertSame(['Wert muss eine Zeichenkette sein.'], $field->getValidationErrors(1));
        $this->assertSame(['Wert muss im Format /^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/ sein.'], $field->getValidationErrors('test'));
        $this->assertSame(['Wert muss eine Zeichenkette sein.'], $field->getValidationErrors([1]));
        $this->assertSame(['Wert muss eine Zeichenkette sein.'], $field->getValidationErrors([1 => 'one']));
    }
}
