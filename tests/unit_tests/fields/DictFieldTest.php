<?php

declare(strict_types=1);

require_once __DIR__.'/../../../src/fields/DictField.php';
require_once __DIR__.'/../common/UnitTestCase.php';
require_once __DIR__.'/FakeItemField.php';

/**
 * @internal
 * @covers \DictField
 */
final class DictFieldTest extends UnitTestCase {
    public function testTypeScriptType(): void {
        $field = new DictField([
            'item_field' => new FakeItemField([]),
        ]);
        $this->assertSame('{[key: string]: ItemType}', $field->getTypeScriptType());
    }

    public function testTypeScriptTypeWithNullAllowed(): void {
        $field = new DictField([
            'item_field' => new FakeItemField([]),
            'allow_null' => true,
        ]);
        $this->assertSame('{[key: string]: ItemType}|null', $field->getTypeScriptType());
    }

    public function testTypeScriptTypeWithNullAllowedInItem(): void {
        $field = new DictField([
            'item_field' => new FakeItemField(['allow_null' => true]),
        ]);
        $this->assertSame('{[key: string]: ItemType|null}', $field->getTypeScriptType());
    }

    public function testParse(): void {
        $field = new DictField([
            'item_field' => new FakeItemField([]),
        ]);
        try {
            $field->parse('test');
            $this->fail('Error expected');
        } catch (\Exception $exc) {
            $this->assertSame("Unlesbares Feld: DictField", $exc->getMessage());
        }
    }

    public function testItemFieldDefault(): void {
        try {
            new DictField([]);
            $this->fail('Error expected');
        } catch (\Exception $exc) {
            $this->assertSame("`item_field` must be an instance of `Field`", $exc->getMessage());
        }
    }

    public function testItemFieldSet(): void {
        $item_field = new FakeItemField([]);
        $field = new DictField([
            'item_field' => $item_field,
        ]);
        $this->assertSame($item_field, $field->getItemField());
    }

    public function testValidatesItemField(): void {
        $field = new DictField([
            'item_field' => new FakeItemField([]),
        ]);
        $this->assertSame(
            [
                "Schlüssel 'key': Wert muss 'foo' oder 'bar' sein.",
                "Schlüssel 'another_key': Wert muss 'foo' oder 'bar' sein.",
            ],
            $field->getValidationErrors([
                'key' => 'value',
                'another_key' => 'another_value',
            ])
        );
        $this->assertSame(
            ["Schlüssel 'offending': Wert muss 'foo' oder 'bar' sein."],
            $field->getValidationErrors([
                'offending' => 'offending',
                'ok' => 'foo',
            ])
        );
        $this->assertSame(
            ["Schlüssel 'key': Feld darf nicht leer sein."],
            $field->getValidationErrors(['key' => null])
        );
        $this->assertSame([], $field->getValidationErrors([
            'foo' => 'foo',
            'bar' => 'bar',
            'other' => 'bar',
        ]));
        $this->assertSame([], $field->getValidationErrors([]));
        $this->assertSame(["Wert muss ein Objekt sein."], $field->getValidationErrors('not_an_object'));
        $this->assertSame(["Feld darf nicht leer sein."], $field->getValidationErrors(null));
    }

    public function testValidatesNullableItemField(): void {
        $field = new DictField([
            'item_field' => new FakeItemField(['allow_null' => true]),
        ]);
        $this->assertSame(
            [
                "Schlüssel 'key': Wert muss 'foo' oder 'bar' sein.",
                "Schlüssel 'another_key': Wert muss 'foo' oder 'bar' sein.",
            ],
            $field->getValidationErrors([
                'key' => 'value',
                'another_key' => 'another_value',
            ])
        );
        $this->assertSame(
            ["Schlüssel 'offending': Wert muss 'foo' oder 'bar' sein."],
            $field->getValidationErrors([
                'offending' => 'offending',
                'ok' => 'foo',
            ])
        );
        $this->assertSame([], $field->getValidationErrors(['key' => null]));
        $this->assertSame([], $field->getValidationErrors([
            'foo' => 'foo',
            'bar' => 'bar',
            'other' => 'bar',
        ]));
        $this->assertSame([], $field->getValidationErrors([]));
        $this->assertSame(["Wert muss ein Objekt sein."], $field->getValidationErrors('not_an_object'));
        $this->assertSame(["Feld darf nicht leer sein."], $field->getValidationErrors(null));
    }

    public function testValidatesNullableDictField(): void {
        $field = new DictField([
            'item_field' => new FakeItemField([]),
            'allow_null' => true,
        ]);
        $this->assertSame(
            [
                "Schlüssel 'key': Wert muss 'foo' oder 'bar' sein.",
                "Schlüssel 'another_key': Wert muss 'foo' oder 'bar' sein.",
            ],
            $field->getValidationErrors([
                'key' => 'value',
                'another_key' => 'another_value',
            ])
        );
        $this->assertSame(
            ["Schlüssel 'offending': Wert muss 'foo' oder 'bar' sein."],
            $field->getValidationErrors([
                'offending' => 'offending',
                'ok' => 'foo',
            ])
        );
        $this->assertSame(
            ["Schlüssel 'key': Feld darf nicht leer sein."],
            $field->getValidationErrors(['key' => null])
        );
        $this->assertSame([], $field->getValidationErrors([
            'foo' => 'foo',
            'bar' => 'bar',
            'other' => 'bar',
        ]));
        $this->assertSame([], $field->getValidationErrors([]));
        $this->assertSame(["Wert muss ein Objekt sein."], $field->getValidationErrors('not_an_object'));
        $this->assertSame([], $field->getValidationErrors(null));
    }
}
