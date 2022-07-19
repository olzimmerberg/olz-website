<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\GeneralUtils;

/**
 * @internal
 * @covers \Olz\Utils\GeneralUtils
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
