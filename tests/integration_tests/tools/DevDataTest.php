<?php

declare(strict_types=1);

require_once __DIR__.'/../common/IntegrationTestCase.php';

/**
 * @internal
 * @coversNothing
 */
final class DevDataTest extends IntegrationTestCase {
    private $dev_data_path = __DIR__.'/../document-root/';
    private $dev_db_structure_path = __DIR__.'/../../../src/tools/dev-data/db_structure.sql';
    private $dev_db_content_path = __DIR__.'/../../../src/tools/dev-data/db_content.sql';

    public function testInitAndDump(): void {
        global $db;
        require_once __DIR__.'/../../../src/config/database.php';
        require_once __DIR__.'/../../../src/tools/dev_data.php';

        $old_dev_db_structure = file_get_contents($this->dev_db_structure_path);
        $old_dev_db_content = file_get_contents($this->dev_db_content_path);

        $init_start_time = time();
        reset_db($db, $this->dev_data_path, true);
        $init_end_time = time();

        $this->assertTrue(is_file("{$this->dev_data_path}olz_mitglieder/max_muster.jpg"));
        $creation_time = filectime("{$this->dev_data_path}olz_mitglieder/max_muster.jpg");
        $this->assertGreaterThanOrEqual($init_start_time, $creation_time);
        $this->assertLessThanOrEqual($init_end_time, $creation_time);

        $new_dev_db_structure = dump_db_structure_sql($db);
        $new_dev_db_content = dump_db_content_sql($db);

        $this->assertGreaterThanOrEqual(100, strlen($new_dev_db_structure));
        $this->assertGreaterThanOrEqual(100, strlen($new_dev_db_content));
    }

    public function testDumpIsFromCurrentMigration(): void {
        global $db;
        require_once __DIR__.'/../../../src/config/database.php';
        require_once __DIR__.'/../../../src/tools/dev_data.php';

        $old_dev_db_structure = file_get_contents($this->dev_db_structure_path);
        $old_dev_db_content = file_get_contents($this->dev_db_content_path);
        $current_migration = get_current_migration($db);

        $structure_has_migration = preg_match(
            '/-- MIGRATION: ([a-zA-Z0-9\\\\]+)\\s+/', $old_dev_db_structure, $structure_matches);
        $this->assertTrue((bool) $structure_has_migration);
        $structure_version = $structure_matches[1];
        $this->assertSame($structure_version, $current_migration);

        $content_has_migration = preg_match(
            '/-- MIGRATION: ([a-zA-Z0-9\\\\]+)\\s+/', $old_dev_db_content, $content_matches);
        $this->assertTrue((bool) $content_has_migration);
        $content_version = $content_matches[1];
        $this->assertSame($content_version, $current_migration);
    }
}
