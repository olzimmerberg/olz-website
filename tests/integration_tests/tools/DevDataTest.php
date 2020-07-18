<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

$_SERVER['DOCUMENT_ROOT'] = realpath(__DIR__.'/../document-root/');

require_once __DIR__.'/../../../src/config/database.php';
require_once __DIR__.'/../../../src/tools/dev_data.php';

/**
 * @internal
 * @coversNothing
 */
final class DevDataTest extends TestCase {
    private $dev_data_path = __DIR__.'/../document-root/';
    private $dev_db_structure_path = __DIR__.'/../../../src/tools/dev-data/db_structure.sql';
    private $dev_db_content_path = __DIR__.'/../../../src/tools/dev-data/db_content.sql';

    public function testInitAndDump(): void {
        global $db;
        $old_dev_db_structure = file_get_contents($this->dev_db_structure_path);
        $old_dev_db_content = file_get_contents($this->dev_db_content_path);

        $init_start_time = time();
        init_dev_data($db, $this->dev_data_path);
        $init_end_time = time();

        $this->assertTrue(is_file("{$this->dev_data_path}olz_mitglieder/max_muster.jpg"));
        $creation_time = filectime("{$this->dev_data_path}olz_mitglieder/max_muster.jpg");
        $this->assertGreaterThanOrEqual($init_start_time, $creation_time);
        $this->assertLessThanOrEqual($init_end_time, $creation_time);

        $new_dev_db_structure = dump_db_structure_sql($db);
        $new_dev_db_content = dump_db_content_sql($db);

        $this->assertSame($old_dev_db_structure, $new_dev_db_structure);
        $this->assertSame($old_dev_db_content, $new_dev_db_content);
    }
}
