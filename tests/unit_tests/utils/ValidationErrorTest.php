<?php

declare(strict_types=1);

require_once __DIR__.'/../../../src/utils/ValidationError.php';
require_once __DIR__.'/../common/UnitTestCase.php';

/**
 * @internal
 * @covers \ValidationError
 */
final class ValidationErrorTest extends UnitTestCase {
    public function testValidationError(): void {
        $error = new ValidationError(['test_field' => ['Error 1', 'Error 2']]);
        $this->assertSame(['test_field' => ['Error 1', 'Error 2']], $error->getValidationErrors());
        $this->assertSame([
            'type' => 'ValidationError',
            'validationErrors' => ['test_field' => ['Error 1', 'Error 2']],
        ], $error->getStructuredAnswer());
    }

    public function testEmptyValidationError(): void {
        $error = new ValidationError([]);
        $this->assertSame([], $error->getValidationErrors());
        $this->assertSame([
            'type' => 'ValidationError',
            'validationErrors' => [],
        ], $error->getStructuredAnswer());
    }
}
