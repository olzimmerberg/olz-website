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
    private $dev_data_path = __DIR__.'/../document-root/';
    private $dev_db_structure_path = __DIR__.'/../../../src/Utils/data/db_structure.sql';
    private $dev_db_content_path = __DIR__.'/../../../src/Utils/data/db_content.sql';

    public function testDevDataUtilsFromEnv(): void {
        $dev_data_utils = DevDataUtils::fromEnv();

        $this->assertFalse(!$dev_data_utils);
    }

    public function testInitAndDump(): void {
        $this->withLockedDb(function () {
            $dev_data_utils = DevDataUtils::fromEnv();

            $old_dev_db_structure = file_get_contents($this->dev_db_structure_path);
            $old_dev_db_content = file_get_contents($this->dev_db_content_path);

            $init_start_time = time();
            $dev_data_utils->fullResetDb();
            $init_end_time = time();

            $this->assertTrue(is_file("{$this->dev_data_path}olz_mitglieder/max_muster.jpg"));
            $creation_time = filectime("{$this->dev_data_path}olz_mitglieder/max_muster.jpg");
            $this->assertGreaterThanOrEqual($init_start_time, $creation_time);
            $this->assertLessThanOrEqual($init_end_time, $creation_time);

            $new_dev_db_structure = $dev_data_utils->getDbStructureSql();
            $new_dev_db_content = $dev_data_utils->getDbContentSql();

            $this->assertGreaterThanOrEqual(100, strlen($new_dev_db_structure));
            $this->assertGreaterThanOrEqual(100, strlen($new_dev_db_content));
        });
    }

    public function testDumpIsFromCurrentMigration(): void {
        $this->withLockedDb(function () {
            $dev_data_utils = DevDataUtils::fromEnv();

            $old_dev_db_structure = file_get_contents($this->dev_db_structure_path);
            $old_dev_db_content = file_get_contents($this->dev_db_content_path);
            $current_migration = $dev_data_utils->getCurrentMigration();

            $structure_has_migration = preg_match(
                '/-- MIGRATION: ([a-zA-Z0-9\\\\]+)\\s+/',
                $old_dev_db_structure,
                $structure_matches
            );
            $this->assertTrue((bool) $structure_has_migration);
            $structure_version = $structure_matches[1];
            $this->assertSame($structure_version, $current_migration);

            $content_has_migration = preg_match(
                '/-- MIGRATION: ([a-zA-Z0-9\\\\]+)\\s+/',
                $old_dev_db_content,
                $content_matches
            );
            $this->assertTrue((bool) $content_has_migration);
            $content_version = $content_matches[1];
            $this->assertSame($content_version, $current_migration);
        });
    }
}
