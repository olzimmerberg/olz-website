<?php

declare(strict_types=1);

require_once __DIR__.'/../../../src/utils/GeneralUtils.php';
require_once __DIR__.'/../common/UnitTestCase.php';

/**
 * @internal
 * @covers \GeneralUtils
 */
final class GeneralUtilsTest extends UnitTestCase {
    public function testBase64EncodeUrl(): void {
        $general_utils = new GeneralUtils();
        $binary_string = base64_decode("+/A="); // contains all special chars
        $this->assertSame('-_A', $general_utils->base64EncodeUrl($binary_string));
    }

    public function testBase64DecodeUrl(): void {
        $general_utils = new GeneralUtils();
        $binary_string = base64_decode("+/A="); // contains all special chars
        $this->assertSame($binary_string, $general_utils->base64DecodeUrl('-_A'));
    }

    public function testGetPrettyTrace(): void {
        $trace = debug_backtrace();
        $general_utils = new GeneralUtils();
        $this->assertMatchesRegularExpression('/phpunit/', $general_utils->getPrettyTrace($trace));
    }
}
