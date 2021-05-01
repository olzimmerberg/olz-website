<?php

declare(strict_types=1);

require_once __DIR__.'/../../../src/fields/BooleanField.php';
require_once __DIR__.'/../common/UnitTestCase.php';

/**
 * @internal
 * @covers \BooleanField
 */
final class BooleanFieldTest extends UnitTestCase {
    public function testTypeScriptType(): void {
        $field = new BooleanField('fake', []);
        $this->assertSame('boolean', $field->getTypeScriptType());
    }

    public function testTypeScriptTypeWithNullAllowed(): void {
        $field = new BooleanField('fake', ['allow_null' => true]);
        $this->assertSame('boolean|null', $field->getTypeScriptType());
    }

    public function testParse(): void {
        $field = new BooleanField('fake', []);
        $this->assertSame(true, $field->parse('true'));
        $this->assertSame(true, $field->parse('1'));
        $this->assertSame(false, $field->parse('false'));
        $this->assertSame(false, $field->parse('0'));
        $this->assertSame(null, $field->parse(''));
        $this->assertSame(null, $field->parse(null));
        try {
            $field->parse('test');
            $this->fail('Error expected');
        } catch (\Exception $exc) {
            $this->assertSame("Unlesbarer Binärwert: 'test'", $exc->getMessage());
        }
    }

    public function testValidatesNullAllowed(): void {
        $field = new BooleanField('fake', ['allow_null' => true]);
        $this->assertSame([], $field->getValidationErrors(true));
        $this->assertSame([], $field->getValidationErrors(false));
        $this->assertSame([], $field->getValidationErrors(null));
    }

    public function testValidatesNullDisallowed(): void {
        $field = new BooleanField('fake', ['allow_null' => false]);
        $this->assertSame([], $field->getValidationErrors(true));
        $this->assertSame([], $field->getValidationErrors(false));
        $this->assertSame(['Feld darf nicht leer sein.'], $field->getValidationErrors(null));
    }

    public function testValidatesWeirdValues(): void {
        $field = new BooleanField('fake', []);
        $this->assertSame(['Wert muss Ja oder Nein sein.'], $field->getValidationErrors(1));
        $this->assertSame(['Wert muss Ja oder Nein sein.'], $field->getValidationErrors('test'));
        $this->assertSame(['Wert muss Ja oder Nein sein.'], $field->getValidationErrors([1]));
        $this->assertSame(['Wert muss Ja oder Nein sein.'], $field->getValidationErrors([1 => 'one']));
    }
}
