<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\UploadUtils;

/**
 * @internal
 *
 * @covers \Olz\Utils\UploadUtils
 */
final class UploadUtilsTest extends UnitTestCase {
    protected $uploadUtils;

    protected function setUp(): void {
        parent::setUp();
        $upload_utils = new UploadUtils();
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

    public function testIsUploadIdNull(): void {
        $this->assertSame(false, $this->uploadUtils->isUploadId(null));
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

    public function testGetUploadIdRegex(): void {
        $this->assertSame('[a-zA-Z0-9_-]{24}\\.[a-zA-Z0-9]+', $this->uploadUtils->getUploadIdRegex());
    }

    public function testGetValidUploadId(): void {
        $valid = '123456789012345678901234.jpg';
        $inexistent = 'inexistent12345678901234.jpg';
        $invalid = 'invalid';
        $this->simulateUpload($valid);

        $this->assertSame($valid, $this->uploadUtils->getValidUploadId($valid));
        $this->assertSame(null, $this->uploadUtils->getValidUploadId($inexistent));
        $this->assertSame(null, $this->uploadUtils->getValidUploadId($invalid));
        $this->assertSame(null, $this->uploadUtils->getValidUploadId(''));
    }

    public function testGetValidUploadIds(): void {
        $valid = '123456789012345678901234.jpg';
        $invalid = 'invalid';
        $this->simulateUpload($valid);

        $this->assertSame([$valid, $valid], $this->uploadUtils->getValidUploadIds([$valid, $valid]));
        $this->assertSame([$valid], $this->uploadUtils->getValidUploadIds([$invalid, $valid]));
        $this->assertSame([], $this->uploadUtils->getValidUploadIds([$invalid, $invalid]));
        $this->assertSame([], $this->uploadUtils->getValidUploadIds([]));
        $this->assertSame([], $this->uploadUtils->getValidUploadIds(null));
    }

    public function testGetStoredUploadIds(): void {
        $valid1 = '111111111111111111111111.jpg';
        $valid2 = '222222222222222222222222.jpg';
        $this->simulateUpload($valid1, 'storage');
        $this->simulateUpload($valid2, 'storage');
        $data_path = $this->envUtils()->getDataPath();

        $this->assertSame([], $this->getLogs());
        $this->assertSame([$valid1, $valid2], $this->uploadUtils->getStoredUploadIds("{$data_path}storage"));
    }

    public function testOverwriteUploads(): void {
        $valid1 = '111111111111111111111111.jpg';
        $valid2 = '222222222222222222222222.jpg';
        $valid3 = '333333333333333333333333.jpg';
        $inexistent = 'inexistent12345678901234.jpg';
        $invalid = 'invalid';
        $this->simulateUpload($valid1, 'storage');
        $this->simulateUpload($valid2, 'storage');
        $this->simulateUpload($valid2, 'temp');
        $this->simulateUpload($valid3, 'temp');
        $data_path = $this->envUtils()->getDataPath();
        $this->assertSame([$valid1, $valid2], $this->uploadUtils->getStoredUploadIds("{$data_path}storage"));

        $this->uploadUtils->overwriteUploads([$valid2, $valid3, $inexistent, $invalid], "{$data_path}storage/");

        $this->assertSame([
            "INFO Deleting existing upload: data-path/storage/111111111111111111111111.jpg.",
            "INFO Deleting existing upload: data-path/storage/222222222222222222222222.jpg.",
            "WARNING Upload file \"data-path/temp/inexistent12345678901234.jpg\" does not exist.",
            "WARNING Upload ID \"invalid\" is invalid.",
        ], $this->getLogs());
        $this->assertSame([$valid2, $valid3], $this->uploadUtils->getStoredUploadIds("{$data_path}storage"));
        $this->assertSame([], $this->uploadUtils->getStoredUploadIds("{$data_path}temp"));
    }

    public function testOverwriteUploadsCreatesNewBasePath(): void {
        $data_path = $this->envUtils()->getDataPath();

        $this->uploadUtils->overwriteUploads([], "{$data_path}storage/");

        $this->assertSame([], $this->getLogs());
        $this->assertSame(true, is_dir("{$data_path}storage"));
    }

    public function testEditUploads(): void {
        $valid1 = '111111111111111111111111.jpg';
        $valid2 = '222222222222222222222222.jpg';
        $inexistent = 'inexistent12345678901234.jpg';
        $invalid = 'invalid';
        $this->simulateUpload($valid1, 'storage');
        $this->simulateUpload($valid2, 'storage');
        $this->simulateUpload($valid1, 'temp');
        $data_path = $this->envUtils()->getDataPath();
        $this->assertSame([$valid1, $valid2], $this->uploadUtils->getStoredUploadIds("{$data_path}storage"));

        $this->uploadUtils->editUploads([$valid1, $valid2, $inexistent, $invalid], "{$data_path}storage/");

        $this->assertSame([
            "WARNING Storage file \"data-path/storage/inexistent12345678901234.jpg\" does not exist.",
            "WARNING Upload ID \"invalid\" is invalid.",
        ], $this->getLogs());
        $this->assertSame([$valid1, $valid2], $this->uploadUtils->getStoredUploadIds("{$data_path}storage"));
        $this->assertSame([$valid1, $valid2], $this->uploadUtils->getStoredUploadIds("{$data_path}temp"));
    }

    private function simulateUpload(string $upload_id, $path = 'temp'): void {
        $data_path = $this->envUtils()->getDataPath();
        if (!is_dir("{$data_path}{$path}/")) {
            mkdir("{$data_path}{$path}/", 0777, true);
        }
        file_put_contents("{$data_path}{$path}/{$upload_id}", '');
    }
}
