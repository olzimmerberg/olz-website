<?php

declare(strict_types=1);

require_once __DIR__.'/../../common/UnitTestCase.php';

/**
 * @internal
 * @coversNothing
 */
final class GenerateOlzApiTest extends UnitTestCase {
    public function testOlzApiHasBeenGenerated(): void {
        $actual_content = file_get_contents(__DIR__.'/../../../../src/api/client/OlzApi.ts');

        include __DIR__.'/../../../../src/api/client/generate.php';

        $expected_content = file_get_contents(__DIR__.'/../../../../src/api/client/OlzApi.ts');

        $this->assertSame($expected_content, $actual_content);
    }
}
