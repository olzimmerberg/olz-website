<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests\Utils;

use Olz\Tests\IntegrationTests\Common\IntegrationTestCase;
use Olz\Utils\DevDataUtils;

/**
 * @internal
 *
 * @covers \Olz\Utils\DevDataUtils
 */
final class DevDataUtilsIntegrationTest extends IntegrationTestCase {
    private string $dev_data_path = __DIR__.'/../document-root/';
    private string $dev_db_structure_path = __DIR__.'/../../../src/Utils/data/db_structure.sql';
    private string $dev_db_content_path = __DIR__.'/../../../src/Utils/data/db_content.sql';

    public function testInitAndDump(): void {
        $this->withLockedDb(function () {
            $utils = $this->getSut();

            $init_start_time = time();
            $utils->fullResetDb();
            $init_end_time = time();

            $this->assertTrue(is_file("{$this->dev_data_path}olz_mitglieder/max_muster.jpg"));
            $creation_time = filectime("{$this->dev_data_path}olz_mitglieder/max_muster.jpg");
            $this->assertGreaterThanOrEqual($init_start_time, $creation_time);
            $this->assertLessThanOrEqual($init_end_time, $creation_time);

            $new_dev_db_structure = $utils->getDbStructureSql();
            $new_dev_db_content = $utils->getDbContentSql();

            $this->assertGreaterThanOrEqual(100, strlen($new_dev_db_structure));
            $this->assertGreaterThanOrEqual(100, strlen($new_dev_db_content));
        });
    }

    public function testDumpIsFromCurrentMigration(): void {
        $this->withLockedDb(function () {
            $utils = $this->getSut();

            $old_dev_db_structure = file_get_contents($this->dev_db_structure_path) ?: '';
            $old_dev_db_content = file_get_contents($this->dev_db_content_path) ?: '';
            $current_migration = $utils->getCurrentMigration();

            $structure_has_migration = preg_match(
                '/-- MIGRATION: ([a-zA-Z0-9\\\]+)\s+/',
                $old_dev_db_structure,
                $structure_matches
            );
            $this->assertTrue((bool) $structure_has_migration);
            $structure_version = $structure_matches[1];
            $this->assertSame($structure_version, $current_migration);

            $content_has_migration = preg_match(
                '/-- MIGRATION: ([a-zA-Z0-9\\\]+)\s+/',
                $old_dev_db_content,
                $content_matches
            );
            $this->assertTrue((bool) $content_has_migration);
            $content_version = $content_matches[1];
            $this->assertSame($content_version, $current_migration);
        });
    }

    protected function getSut(): DevDataUtils {
        self::bootKernel();
        // @phpstan-ignore-next-line
        return self::getContainer()->get(DevDataUtils::class);
    }
}
