<?php

declare(strict_types=1);

require_once __DIR__.'/../../../src/fields/Field.php';
require_once __DIR__.'/../../../src/fields/IntegerField.php';
require_once __DIR__.'/../../../src/utils/FieldUtils.php';
require_once __DIR__.'/../../../src/utils/ValidationError.php';
require_once __DIR__.'/../common/UnitTestCase.php';

/**
 * @internal
 * @covers \FieldUtils
 */
final class FieldUtilsTest extends UnitTestCase {
    public function testValidateEmptyFieldsEmptyInput(): void {
        $field_utils = new FieldUtils();
        $this->assertSame([], $field_utils->validate([], []));
    }

    public function testValidateEmptyFieldsTooMuchInput(): void {
        $field_utils = new FieldUtils();
        try {
            $field_utils->validate([], ['tooMuch' => true]);
            $this->fail('Error expected');
        } catch (ValidationError $err) {
            $this->assertMatchesRegularExpression('/^Validation Error: /', $err->getMessage());
            $this->assertSame([
                'tooMuch' => ["Feld existiert nicht: tooMuch"],
            ], $err->getValidationErrors());
        }
    }

    public function testValidateMissingInput(): void {
        $field_utils = new FieldUtils();
        try {
            $field_utils->validate([
                'input' => new Field(['allow_null' => false]),
            ], []);
            $this->fail('Error expected');
        } catch (ValidationError $err) {
            $this->assertMatchesRegularExpression('/^Validation Error: /', $err->getMessage());
            $this->assertSame([
                'input' => ["Feld darf nicht leer sein."],
            ], $err->getValidationErrors());
        }
    }

    public function testValidateTooMuchInput(): void {
        $field_utils = new FieldUtils();
        try {
            $field_utils->validate([
                'input' => new Field(['allow_null' => false]),
            ], ['input' => 'test', 'tooMuch' => true]);
            $this->fail('Error expected');
        } catch (ValidationError $err) {
            $this->assertMatchesRegularExpression('/^Validation Error: /', $err->getMessage());
            $this->assertSame([
                'tooMuch' => ["Feld existiert nicht: tooMuch"],
            ], $err->getValidationErrors());
        }
    }

    public function testValidateInvalidInput(): void {
        $field_utils = new FieldUtils();
        try {
            $field_utils->validate([
                'input' => new Field(['allow_null' => false]),
            ], ['input' => null]);
            $this->fail('Error expected');
        } catch (ValidationError $err) {
            $this->assertMatchesRegularExpression('/^Validation Error: /', $err->getMessage());
            $this->assertSame([
                'input' => ["Feld darf nicht leer sein."],
            ], $err->getValidationErrors());
        }
    }

    public function testValidateMultipleErrors(): void {
        $field_utils = new FieldUtils();
        try {
            $field_utils->validate([
                'input' => new Field(['allow_null' => false]),
            ], ['input' => null, 'tooMuch' => true]);
            $this->fail('Error expected');
        } catch (ValidationError $err) {
            $this->assertMatchesRegularExpression('/^Validation Error: /', $err->getMessage());
            $this->assertSame([
                'input' => ["Feld darf nicht leer sein."],
                'tooMuch' => ["Feld existiert nicht: tooMuch"],
            ], $err->getValidationErrors());
        }
    }

    public function testValidateParsed(): void {
        $field_utils = new FieldUtils();
        try {
            $field_utils->validate([
                'input' => new Field(['allow_null' => false]),
            ], ['input' => ''], ['parse' => true]);
            $this->fail('Error expected');
        } catch (ValidationError $err) {
            $this->assertMatchesRegularExpression('/^Validation Error: /', $err->getMessage());
            $this->assertSame([
                'input' => ["Feld darf nicht leer sein."],
            ], $err->getValidationErrors());
        }
    }

    public function testValidateUnparseable(): void {
        $field_utils = new FieldUtils();
        try {
            $field_utils->validate([
                'input' => new IntegerField([]),
            ], ['input' => 'not_an_integer'], ['parse' => true]);
            $this->fail('Error expected');
        } catch (ValidationError $err) {
            $this->assertMatchesRegularExpression('/^Validation Error: /', $err->getMessage());
            $this->assertSame([
                'input' => ["Unlesbare Ganzzahl: 'not_an_integer'"],
            ], $err->getValidationErrors());
        }
    }
}
