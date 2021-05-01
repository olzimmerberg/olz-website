<?php

declare(strict_types=1);

require_once __DIR__.'/../../../src/fields/AbstractTemporalField.php';
require_once __DIR__.'/../common/UnitTestCase.php';

class FakeTemporalField extends AbstractTemporalField {
    protected function getRegex() {
        return '/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/';
    }
}

/**
 * @internal
 * @covers \AbstractTemporalField
 */
final class AbstractTemporalFieldTest extends UnitTestCase {
    public function testTypeScriptType(): void {
        $field = new FakeTemporalField('fake', []);
        $this->assertSame('string', $field->getTypeScriptType());
    }

    public function testTypeScriptTypeWithNullAllowed(): void {
        $field = new FakeTemporalField('fake', ['allow_null' => true]);
        $this->assertSame('string|null', $field->getTypeScriptType());
    }

    public function testParse(): void {
        $field = new FakeTemporalField('fake', []);
        $this->assertSame('test', $field->parse('test'));
    }

    public function testMinValueDefault(): void {
        $field = new FakeTemporalField('fake', []);
        $this->assertSame(null, $field->getMinValue());
    }

    public function testMinValueSet(): void {
        $field = new FakeTemporalField('fake', ['min_value' => '2020-03-13']);
        $this->assertSame('2020-03-13', $field->getMinValue());
    }

    public function testMaxValueDefault(): void {
        $field = new FakeTemporalField('fake', []);
        $this->assertSame(null, $field->getMaxValue());
    }

    public function testMaxValueSet(): void {
        $field = new FakeTemporalField('fake', ['max_value' => '2020-03-13']);
        $this->assertSame('2020-03-13', $field->getMaxValue());
    }

    public function testValidatesMinValue(): void {
        $field = new FakeTemporalField('fake', ['min_value' => '2020-03-13']);
        $this->assertSame(['Wert darf nicht kleiner als 2020-03-13 sein.'], $field->getValidationErrors('2020-03-12'));
        $this->assertSame([], $field->getValidationErrors('2020-03-13'));
        $this->assertSame([], $field->getValidationErrors('2020-03-14'));
    }

    public function testValidatesMaxValue(): void {
        $field = new FakeTemporalField('fake', ['max_value' => '2020-03-13']);
        $this->assertSame([], $field->getValidationErrors('2020-03-12'));
        $this->assertSame([], $field->getValidationErrors('2020-03-13'));
        $this->assertSame(['Wert darf nicht grösser als 2020-03-13 sein.'], $field->getValidationErrors('2020-03-14'));
    }

    public function testValidatesWeirdValues(): void {
        $field = new FakeTemporalField('fake', []);
        $this->assertSame(['Wert muss eine Zeichenkette sein.'], $field->getValidationErrors(false));
        $this->assertSame(['Wert muss eine Zeichenkette sein.'], $field->getValidationErrors(true));
        $this->assertSame(['Wert muss eine Zeichenkette sein.'], $field->getValidationErrors(1));
        $this->assertSame(['Wert muss im Format /^[0-9]{4}-[0-9]{2}-[0-9]{2}$/ sein.'], $field->getValidationErrors('test'));
        $this->assertSame(['Wert muss eine Zeichenkette sein.'], $field->getValidationErrors([1]));
        $this->assertSame(['Wert muss eine Zeichenkette sein.'], $field->getValidationErrors([1 => 'one']));
    }
}
