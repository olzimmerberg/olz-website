<?php

declare(strict_types=1);

require_once __DIR__.'/../../common/UnitTestCase.php';

/**
 * @internal
 * @coversNothing
 */
final class GenerateOlzApiTest extends UnitTestCase {
    public function testOlzApiHasBeenGenerated(): void {
        $actual_content = file_get_contents(__DIR__.'/../../../../_/api/client/generated_olz_api_types.ts');

        ob_start();
        include __DIR__.'/../../../../_/api/client/generate.php';
        ob_end_clean();

        $expected_content = file_get_contents(__DIR__.'/../../../../_/api/client/generated_olz_api_types.ts');

        $this->assertSame($expected_content, $actual_content);
    }
}
