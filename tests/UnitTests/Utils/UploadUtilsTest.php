<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\GeneralUtils;
use Olz\Utils\UploadUtils;

/**
 * @internal
 *
 * @covers \Olz\Utils\UploadUtils
 */
final class UploadUtilsTest extends UnitTestCase {
    protected function setUp(): void {
        parent::setUp();
        $upload_utils = new UploadUtils();
        $general_utils = new GeneralUtils();
        $upload_utils->setGeneralUtils($general_utils);
        $this->uploadUtils = $upload_utils;
    }

    public function testObfuscateForUpload(): void {
        $obfuscated = $this->uploadUtils->obfuscateForUpload('test');
        $this->assertMatchesRegularExpression('/^[0-9]+;[a-zA-Z0-9\\+\\/]+[=]*$/', $obfuscated);
    }

    public function testDeobfuscateUpload(): void {
        $obfuscated = '39842;vpsPDLtg/ynqf7gfHZ2oSS6akhROeG4rcG0xCoaZMULR';
        $this->assertSame('ĩä😎=/+', $this->uploadUtils->deobfuscateUpload($obfuscated));
    }

    public function testDeobfuscateUploadFromTypeScript(): void {
        $obfuscated_from_ts = '36902;tTcqk0yuflo5H1nIohtler1GN3s5Bnfg2/VIrUmvbNk6';
        $this->assertSame('ĩä😎=/+', $this->uploadUtils->deobfuscateUpload($obfuscated_from_ts));
    }

    public function testObfuscateDeobfuscate(): void {
        $original = 'ĩä😎=/+';
        $obfuscated = $this->uploadUtils->obfuscateForUpload($original);
        $this->assertSame($original, $this->uploadUtils->deobfuscateUpload($obfuscated));
    }

    public function testGetRandomUploadId(): void {
        $this->assertMatchesRegularExpression(
            '/^[a-zA-Z0-9_-]{24}\.jpg$/',
            $this->uploadUtils->getRandomUploadId('.jpg')
        );
    }

    public function testGetRandomUploadIdWithInvalidSuffix(): void {
        try {
            $this->uploadUtils->getRandomUploadId('invalid');
            $this->fail('Error expected');
        } catch (\Exception $exc) {
            $this->assertSame(
                'Invalid upload ID suffix: invalid',
                $exc->getMessage()
            );
        }
    }

    public function testRandomUploadIdIsUploadId(): void {
        $upload_id = $this->uploadUtils->getRandomUploadId('.jpg');
        $this->assertSame(true, $this->uploadUtils->isUploadId($upload_id));
    }

    public function testIsUploadIdEmpty(): void {
        $this->assertSame(false, $this->uploadUtils->isUploadId(''));
    }

    public function testIsUploadIdNoRandom(): void {
        $this->assertSame(false, $this->uploadUtils->isUploadId('.jpg'));
    }

    public function testIsUploadIdTooShort(): void {
        $this->assertSame(false, $this->uploadUtils->isUploadId('12345678901234567890123.jpg'));
    }

    public function testIsUploadIdTooLong(): void {
        $this->assertSame(false, $this->uploadUtils->isUploadId('1234567890123456789012345.jpg'));
    }

    public function testIsUploadIdInvalidChar(): void {
        $this->assertSame(false, $this->uploadUtils->isUploadId('12345678901234567890123!.jpg'));
    }

    public function testIsUploadIdInvalidExtension(): void {
        $this->assertSame(false, $this->uploadUtils->isUploadId('123456789012345678901234.invalid!'));
    }

    public function testIsUploadIdValid(): void {
        $this->assertSame(true, $this->uploadUtils->isUploadId('123456789012345678901234.jpg'));
        $this->assertSame(true, $this->uploadUtils->isUploadId('abcdABCD_-12345678901234.pdf'));
    }
}
