<?php

declare(strict_types=1);

require_once __DIR__.'/../../../src/utils/UploadUtils.php';
require_once __DIR__.'/../common/UnitTestCase.php';

/**
 * @internal
 * @covers \UploadUtils
 */
final class UploadUtilsTest extends UnitTestCase {
    public function testObfuscateForUpload(): void {
        $upload_utils = new UploadUtils();
        $obfuscated = $upload_utils->obfuscateForUpload('test');
        $this->assertMatchesRegularExpression('/^[0-9]+;[a-zA-Z0-9\\+\\/]+[=]*$/', $obfuscated);
    }

    public function testDeobfuscateUpload(): void {
        $upload_utils = new UploadUtils();
        $obfuscated = '39842;vpsPDLtg/ynqf7gfHZ2oSS6akhROeG4rcG0xCoaZMULR';
        $this->assertSame('ĩä😎=/+', $upload_utils->deobfuscateUpload($obfuscated));
    }

    public function testDeobfuscateUploadFromTypeScript(): void {
        $upload_utils = new UploadUtils();
        $obfuscated_from_ts = '36902;tTcqk0yuflo5H1nIohtler1GN3s5Bnfg2/VIrUmvbNk6';
        $this->assertSame('ĩä😎=/+', $upload_utils->deobfuscateUpload($obfuscated_from_ts));
    }

    public function testObfuscateDeobfuscate(): void {
        $upload_utils = new UploadUtils();
        $original = 'ĩä😎=/+';
        $obfuscated = $upload_utils->obfuscateForUpload($original);
        $this->assertSame($original, $upload_utils->deobfuscateUpload($obfuscated));
    }
}
