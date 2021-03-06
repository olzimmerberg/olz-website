<?php

declare(strict_types=1);

require_once __DIR__.'/../../../../src/api/common/HttpError.php';
require_once __DIR__.'/../../../../src/utils/ValidationError.php';
require_once __DIR__.'/../../common/UnitTestCase.php';

/**
 * @internal
 * @covers \HttpError
 */
final class HttpErrorTest extends UnitTestCase {
    public function testHttpError(): void {
        $error = new HttpError(404, 'Not Found');
        $this->assertSame(404, $error->getCode());
        $this->assertSame('Not Found', $error->getMessage());
        $this->assertSame([
            'message' => 'Not Found',
            'error' => true,
        ], $error->getStructuredAnswer());
    }

    public function testHttpErrorOfInputValidationError(): void {
        $validation_error = new ValidationError([]);
        $error = new HttpError(400, 'Bad Request', $validation_error);
        $this->assertSame(400, $error->getCode());
        $this->assertSame('Bad Request', $error->getMessage());
        $this->assertSame([
            'message' => 'Bad Request',
            'error' => [
                'type' => 'ValidationError',
                'validationErrors' => [],
            ],
        ], $error->getStructuredAnswer());
    }

    public function testHttpErrorOfOutputValidationError(): void {
        $validation_error = new ValidationError([]);
        $error = new HttpError(500, 'Server Internal Error', $validation_error);
        $this->assertSame(500, $error->getCode());
        $this->assertSame('Server Internal Error', $error->getMessage());
        $this->assertSame([
            'message' => 'Server Internal Error',
        ], $error->getStructuredAnswer());
    }
}
