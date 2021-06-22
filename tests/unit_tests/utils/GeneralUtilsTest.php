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

    public function testObfuscateForUpload(): void {
        $general_utils = new GeneralUtils();
        $obfuscated = $general_utils->obfuscateForUpload('test');
        $this->assertMatchesRegularExpression('/^[0-9]+;[a-zA-Z0-9\\+\\/]+[=]*$/', $obfuscated);
    }

    public function testDeobfuscateUpload(): void {
        $general_utils = new GeneralUtils();
        $obfuscated = '39842;vpsPDLtg/ynqf7gfHZ2oSS6akhROeG4rcG0xCoaZMULR';
        $this->assertSame('ĩä😎=/+', $general_utils->deobfuscateUpload($obfuscated));
    }

    public function testDeobfuscateUploadFromTypeScript(): void {
        $general_utils = new GeneralUtils();
        $obfuscated_from_ts = '36902;tTcqk0yuflo5H1nIohtler1GN3s5Bnfg2/VIrUmvbNk6';
        $this->assertSame('ĩä😎=/+', $general_utils->deobfuscateUpload($obfuscated_from_ts));
    }

    public function testObfuscateDeobfuscate(): void {
        $general_utils = new GeneralUtils();
        $original = 'ĩä😎=/+';
        $obfuscated = $general_utils->obfuscateForUpload($original);
        $this->assertSame($original, $general_utils->deobfuscateUpload($obfuscated));
    }

    public function testGetPrettyTrace(): void {
        $trace = debug_backtrace();
        $general_utils = new GeneralUtils();
        $this->assertMatchesRegularExpression('/phpunit/', $general_utils->getPrettyTrace($trace));
    }
}
