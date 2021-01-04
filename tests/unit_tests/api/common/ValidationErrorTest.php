<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../../../../src/api/common/ValidationError.php';

/**
 * @internal
 * @covers \ValidationError
 */
final class ValidationErrorTest extends TestCase {
    public function testValidationError(): void {
        $error = new ValidationError(['test_field' => ['Error 1', 'Error 2']]);
        $this->assertSame(['test_field' => ['Error 1', 'Error 2']], $error->getValidationErrors());
        $this->assertSame(['test_field' => ['Error 1', 'Error 2']], $error->getStructuredAnswer());
    }

    public function testEmptyValidationError(): void {
        $error = new ValidationError([]);
        $this->assertSame([], $error->getValidationErrors());
        $this->assertSame([], $error->getStructuredAnswer());
    }
}
