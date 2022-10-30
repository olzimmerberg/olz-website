<?php

namespace Olz\Utils;

use Ifsnop\Mysqldump\Mysqldump;

class DevDataUtils {
    use WithUtilsTrait;
    public const UTILS = [
        'dbUtils',
        'envUtils',
        'generalUtils',
        'log',
    ];

    private $enqueuedForTouch = [];

    /** DO NOT CALL THIS FUNCTION ON PROD! */
    public function fullResetDb() {
        // Overwrite database with dev content.
        $this->dropDbTables();
        $this->addDbStructure();
        $this->addDbContent();
        $this->migrateTo('latest');

        // Initialize the non-code data file system at $data_path
        $this->clearFiles();
        $this->addFiles();
    }

    /** DO NOT CALL THIS FUNCTION ON PROD! */
    public function resetDbStructure() {
        // Overwrite database with dev content.
        $this->dropDbTables();
        $this->addDbStructure();
        $this->addDbContent();
        $this->migrateTo('latest');

        // Initialize the non-code data file system at $data_path
        $this->addFiles();
    }

    /** DO NOT CALL THIS FUNCTION ON PROD! */
    public function resetDbContent() {
        $this->truncateDbTables();
        $this->addDbContent();
    }

    /** DO NOT CALL THIS FUNCTION ON PROD! */
    public function dropDbTables() {
        $db = $this->dbUtils()->getDb();

        // Remove all database tables.
        $beg = microtime(true);
        $result = $db->query("SHOW TABLES");
        $table_names = [];
        while ($row = $result->fetch_array()) {
            $table_name = $row[0];
            $table_names[] = $table_name;
        }
        $db->query('SET foreign_key_checks = 0');
        foreach ($table_names as $table_name) {
            $sql = "DROP TABLE `{$table_name}`";
            $db->query($sql);
        }
        $db->query('SET foreign_key_checks = 1');
        $duration = round(microtime(true) - $beg, 3);
        $this->log()->debug("Dropping took {$duration}s");
    }

    /** DO NOT CALL THIS FUNCTION ON PROD! */
    public function truncateDbTables() {
        $db = $this->dbUtils()->getDb();

        // Remove all database tables.
        $beg = microtime(true);
        $result = $db->query("SHOW TABLES");
        $table_names = [];
        while ($row = $result->fetch_array()) {
            $table_name = $row[0];
            $table_names[] = $table_name;
        }
        $db->query('SET foreign_key_checks = 0');
        foreach ($table_names as $table_name) {
            $sql = "TRUNCATE TABLE `{$table_name}`";
            $db->query($sql);
        }
        $db->query('SET foreign_key_checks = 1');
        $duration = round(microtime(true) - $beg, 3);
        $this->log()->debug("Truncating took {$duration}s");
    }

    public function addDbStructure() {
        $db = $this->dbUtils()->getDb();
        $dev_data_dir = __DIR__.'/data/';

        // Overwrite database structure with dev content.
        $beg = microtime(true);
        $sql_content = file_get_contents("{$dev_data_dir}db_structure.sql");
        if ($db->multi_query($sql_content)) {
            while ($db->next_result()) {
                $result = $db->store_result();
                if ($result) {
                    $result->free();
                }
            }
        }
        $duration = round(microtime(true) - $beg, 3);
        $this->log()->debug("Adding structure took {$duration}s");
    }

    public function addDbContent() {
        $db = $this->dbUtils()->getDb();
        $dev_data_dir = __DIR__.'/data/';

        // Insert dev content into database.
        $beg = microtime(true);
        $db->query('SET foreign_key_checks = 0');
        $sql_content = file_get_contents("{$dev_data_dir}db_content.sql");
        if ($db->multi_query($sql_content)) {
            while ($db->next_result()) {
                $result = $db->store_result();
                if ($result) {
                    $result->free();
                }
            }
        }
        $db->query('SET foreign_key_checks = 1');
        $duration = round(microtime(true) - $beg, 3);
        $this->log()->debug("Adding content took {$duration}s");
    }

    public function getCurrentMigration() {
        $db = $this->dbUtils()->getDb();

        $migrations_config = require __DIR__.'/../../_/config/migrations.php';
        $migrations_table_name = $migrations_config['table_storage']['table_name'];

        $current_migration_result = $db->query("
            SELECT version
            FROM `{$migrations_table_name}`
            ORDER BY `version` DESC
            LIMIT 1");
        $current_migration = $current_migration_result->fetch_assoc()['version'];
        return $current_migration;
    }

    public function migrateTo($version = 'latest') {
        global $code_href;
        $cwd = getcwd();
        $target_dir = realpath(__DIR__."/../../");
        chdir($target_dir);
        $command = "./bin/console doctrine:migrations:migrate '{$version}' --no-interaction";
        exec($command, $output, $code);
        chdir($cwd);
    }

    public function getDbBackup($key) {
        $db = $this->dbUtils()->getDb();

        if (!$key || strlen($key) < 10) {
            throw new \Exception("No valid key");
        }
        $sql = '';
        $sql .= $this->getDbStructureSql();
        $sql .= "\n\n----------\n\n\n";
        $sql .= $this->getDbContentSql();

        $plaintext = $sql;
        $algo = 'aes-256-gcm';
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($algo));
        $ciphertext = openssl_encrypt($plaintext, $algo, $key, OPENSSL_RAW_DATA, $iv, $tag);
        echo json_encode([
            'algo' => $algo,
            'iv' => base64_encode($iv),
            'tag' => base64_encode($tag),
            'ciphertext' => base64_encode($ciphertext),
        ]);
        echo "\n";
    }

    public function dumpDb() {
        $dev_data_dir = __DIR__.'/data/';

        $sql_structure = $this->getDbStructureSql();
        $sql_content = $this->getDbContentSql();
        $sql_structure_path = "{$dev_data_dir}db_structure.sql";
        $sql_content_path = "{$dev_data_dir}db_content.sql";
        file_put_contents($sql_structure_path, $sql_structure);
        file_put_contents($sql_content_path, $sql_content);
    }

    public function getDbStructureSql() {
        $env_utils = $this->envUtils();

        $current_migration = $this->getCurrentMigration();
        $sql_content = (
            "-- Die Struktur der Datenbank der Webseite der OL Zimmerberg\n"
            ."-- MIGRATION: {$current_migration}\n"
            ."\n"
        );
        $dump_filename = tempnam(__DIR__.'/tmp', 'OLZ');
        $mysql_server = $env_utils->getMysqlServer();
        $mysql_schema = $env_utils->getMysqlSchema();
        $dump = new Mysqldump(
            "mysql:host={$mysql_server};dbname={$mysql_schema}",
            $env_utils->getMysqlUsername(),
            $env_utils->getMysqlPassword(),
            [
                'skip-comments' => true,
                'no-data' => true,
                // This is the only way to exclude all views:
                'include-views' => [''], // include only a view which does not exist.
            ],
        );
        $dump->start($dump_filename);
        $sql_content .= file_get_contents($dump_filename);
        unlink($dump_filename);

        return $sql_content;
    }

    public function getDbContentSql() {
        $db = $this->dbUtils()->getDb();
        $current_migration = $this->getCurrentMigration();
        $sql_content = (
            "-- Der Test-Inhalt der Datenbank der Webseite der OL Zimmerberg\n"
            ."-- MIGRATION: {$current_migration}\n"
            ."\n"
            ."SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n"
            ."SET AUTOCOMMIT = 0;\n"
            ."START TRANSACTION;\n"
            ."SET time_zone = \"+00:00\";\n"
        );

        $res_tables = $db->query('SHOW TABLES');
        while ($row_tables = $res_tables->fetch_row()) {
            $table_name = $row_tables[0];
            $sql_content .= "\n";
            $sql_content .= "-- Table {$table_name}\n";
            $res_contents = $db->query("SELECT * FROM `{$table_name}`");
            if ($res_contents->num_rows > 0) {
                $sql_content .= "INSERT INTO {$table_name}\n";
                $content_fields = $res_contents->fetch_fields();
                $field_names = [];
                foreach ($content_fields as $field) {
                    $field_names[] = $field->name;
                }
                $field_names_sql = implode('`, `', $field_names);
                $sql_content .= "    (`{$field_names_sql}`)\n";
                $sql_content .= "VALUES\n";
                $first = true;
                while ($row_contents = $res_contents->fetch_assoc()) {
                    if ($first) {
                        $first = false;
                    } else {
                        $sql_content .= ",\n";
                    }
                    $field_values = [];
                    foreach ($field_names as $name) {
                        $content = $row_contents[$name];
                        if ($content === null) {
                            $field_values[] = 'NULL';
                        } else {
                            $sane_content = $db->escape_string("{$content}");
                            $field_values[] = "'{$sane_content}'";
                        }
                    }
                    $field_values_sql = implode(', ', $field_values);
                    $sql_content .= "    ({$field_values_sql})";
                }
                $sql_content .= ";\n";
            }
        }
        $sql_content .= "\n";
        $sql_content .= "COMMIT;\n";
        return $sql_content;
    }

    public function clearFiles() {
        $env_utils = $this->envUtils();
        $data_path = $env_utils->getDataPath();
        $general_utils = $this->generalUtils();

        // Remove existing data.
        $general_utils->removeRecursive("{$data_path}downloads");
        $general_utils->removeRecursive("{$data_path}files");
        $general_utils->removeRecursive("{$data_path}img");
        $general_utils->removeRecursive("{$data_path}movies");
        $general_utils->removeRecursive("{$data_path}olz_mitglieder");
        $general_utils->removeRecursive("{$data_path}OLZimmerbergAblage");
        $general_utils->removeRecursive("{$data_path}pdf");
        $general_utils->removeRecursive("{$data_path}results");
        $general_utils->removeRecursive("{$data_path}temp");
    }

    public function addFiles() {
        $env_utils = $this->envUtils();
        $data_path = $env_utils->getDataPath();

        $sample_path = __DIR__.'/data/sample-data/';

        $this->enqueuedForTouch = [];

        // Build downloads/
        $this->mkdir("{$data_path}downloads");

        // Build files/
        $this->mkdir("{$data_path}files");
        $this->mkdir("{$data_path}files/aktuell");
        $this->mkdir("{$data_path}files/aktuell/3");
        $this->copy("{$sample_path}sample-document.pdf", "{$data_path}files/aktuell/3/001.pdf");
        $this->mkdir("{$data_path}files/blog");
        $this->mkdir("{$data_path}files/blog/1");
        $this->copy("{$sample_path}sample-document.pdf", "{$data_path}files/blog/1/001.pdf");
        $this->mkdir("{$data_path}files/downloads");
        $this->mkdir("{$data_path}files/news");
        $this->mkdir("{$data_path}files/news/4");
        $this->copy("{$sample_path}sample-document.pdf", "{$data_path}files/news/4/xMpu3ExjfBKa8Cp35bcmsDgq.pdf");
        $this->mkdir("{$data_path}files/termine");
        $this->mkdir("{$data_path}files/termine/2");
        $this->copy("{$sample_path}sample-document.pdf", "{$data_path}files/termine/2/001.pdf");

        // Build img/
        $this->mkdir("{$data_path}img");
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/trophy.png", 140, 140);
        $this->mkdir("{$data_path}img/aktuell");
        $this->mkdir("{$data_path}img/aktuell/3");
        $this->mkdir("{$data_path}img/aktuell/3/img");
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/aktuell/3/img/001.jpg", 800, 600);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/aktuell/3/img/002.jpg", 800, 600);
        $this->mkdir("{$data_path}img/blog");
        $this->mkdir("{$data_path}img/blog/1");
        $this->mkdir("{$data_path}img/blog/1/img");
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/blog/1/img/001.jpg", 800, 600);
        $this->mkdir("{$data_path}img/weekly_picture");
        $this->mkdir("{$data_path}img/weekly_picture/2");
        $this->mkdir("{$data_path}img/weekly_picture/2/img");
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/weekly_picture/2/img/001.jpg", 800, 600);
        $this->mkdir("{$data_path}img/fuer_einsteiger");
        $this->mkdir("{$data_path}img/fuer_einsteiger/img");
        $this->mkdir("{$data_path}img/fuer_einsteiger/thumb");
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/orientierungslauf_001.jpg", 800, 600);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/orientierungslauf_002.jpg", 800, 600);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/orientierungslauf_003.jpg", 800, 600);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/orientierungslauf_004.jpg", 800, 600);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/was_ist_ol_001.jpg", 800, 600);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/ol_zimmerberg_001.jpg", 800, 600);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/ol_zimmerberg_002.jpg", 800, 600);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/ol_zimmerberg_003.jpg", 800, 600);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/ol_zimmerberg_004.jpg", 800, 600);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/ol_zimmerberg_005.jpg", 800, 600);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/ol_zimmerberg_006.jpg", 800, 600);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/ol_zimmerberg_007.jpg", 800, 600);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/ol_zimmerberg_008.jpg", 800, 600);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/ol_zimmerberg_009.jpg", 800, 600);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/ol_zimmerberg_010.jpg", 800, 600);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/ol_zimmerberg_011.jpg", 800, 600);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/ol_zimmerberg_012.jpg", 800, 600);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/ol_zimmerberg_013.jpg", 800, 600);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/ol_zimmerberg_014.jpg", 800, 600);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/ol_zimmerberg_015.jpg", 800, 600);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/ol_zimmerberg_016.jpg", 800, 600);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/wie_anfangen_001.jpg", 800, 600);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/wie_anfangen_002.jpg", 800, 600);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/wie_anfangen_003.jpg", 800, 600);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/wie_anfangen_004.jpg", 800, 600);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/trainings_001.jpg", 800, 600);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/trainings_002.jpg", 800, 600);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/trainings_003.jpg", 800, 600);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/trainings_004.jpg", 800, 600);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/trainings_005.jpg", 800, 600);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/trainings_006.jpg", 800, 600);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/trainings_007.jpg", 800, 600);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/trainings_008.jpg", 800, 600);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/trainings_009.jpg", 800, 600);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/trainings_010.jpg", 800, 600);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/trainings_011.jpg", 800, 600);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/trainings_012.jpg", 800, 600);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/trainings_013.jpg", 800, 600);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/trainings_014.jpg", 800, 600);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/trainings_015.jpg", 800, 600);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/trainings_016.jpg", 800, 600);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/pack_die_chance_001.jpg", 800, 600);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/ansprechperson_001.jpg", 800, 600);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/ansprechperson_002.jpg", 800, 600);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/ansprechperson_003.jpg", 800, 600);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/ansprechperson_004.jpg", 800, 600);

        // Generate thumbs
        if (function_exists('shell_exec')) {
            $this->copy(__DIR__."/../../tools/fuer_einsteiger/thumbize.sh", "{$data_path}img/fuer_einsteiger/thumbize.sh");
            $pwd = getcwd();
            chdir("{$data_path}img/fuer_einsteiger");
            shell_exec("sh ./thumbize.sh");
            chdir($pwd);
        }

        $this->mkdir("{$data_path}img/galerie");
        $this->mkdir("{$data_path}img/galerie/1");
        $this->mkdir("{$data_path}img/galerie/1/img");
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/galerie/1/img/001.jpg", 800, 600);
        $this->mkdir("{$data_path}img/galerie/2");
        $this->mkdir("{$data_path}img/galerie/2/img");
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/galerie/2/img/001.jpg", 800, 600);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/galerie/2/img/002.jpg", 800, 600);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/galerie/2/img/003.jpg", 800, 600);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/galerie/2/img/004.jpg", 800, 600);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/galerie/2/img/005.jpg", 800, 600);
        $this->mkdir("{$data_path}img/galerie/3");
        $this->mkdir("{$data_path}img/galerie/3/img");
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/galerie/3/img/001.jpg", 800, 600);
        $this->mkdir("{$data_path}img/karten");
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/karten/landforst_2017_10000.jpg", 800, 600);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/karten/horgen_dorfkern_2011_2000.jpg", 800, 600);
        $this->mkdir("{$data_path}img/news");
        $this->mkdir("{$data_path}img/news/4");
        $this->mkdir("{$data_path}img/news/4/img");
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/news/4/img/xkbGJQgO5LFXpTSz2dCnvJzu.jpg", 800, 600);

        $this->mkdir("{$data_path}img/users");
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/users/1.jpg", 84, 120);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/users/1@2x.jpg", 168, 240);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/users/3.jpg", 84, 120);

        // Build movies/
        $this->mkdir("{$data_path}movies");

        // Build olz_mitglieder/
        $this->mkdir("{$data_path}olz_mitglieder");
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "olz_mitglieder/max_muster.jpg", 84, 120);

        // Build OLZimmerbergAblage/
        $this->mkdir("{$data_path}OLZimmerbergAblage");
        $dokumente_path = "{$data_path}OLZimmerbergAblage/OLZ Dokumente";
        $this->mkdir("{$dokumente_path}");
        $this->mkdir("{$dokumente_path}/vorstand");
        $this->copy("{$sample_path}sample-document.pdf", "{$dokumente_path}/vorstand/mitgliederliste.pdf");
        $this->mkdir("{$dokumente_path}/vorstand/protokolle");
        $this->copy("{$sample_path}sample-document.pdf", "{$dokumente_path}/vorstand/protokolle/protokoll.pdf");
        $this->mkdir("{$dokumente_path}/karten");
        $this->copy("{$sample_path}sample-document.pdf", "{$dokumente_path}/karten/uebersicht.pdf");
        $this->mkdir("{$dokumente_path}/karten/wald");
        $this->copy("{$sample_path}sample-document.pdf", "{$dokumente_path}/karten/wald/buchstabenwald.pdf");

        // Build pdf/
        $this->mkdir("{$data_path}pdf");
        $this->copy("{$sample_path}sample-document.pdf", "{$data_path}pdf/trainingsprogramm.pdf");

        // Build results/
        $this->mkdir("{$data_path}results");
        $this->copy("{$sample_path}sample-results.xml", "{$data_path}results/results.xml");

        // Build temp/
        $this->mkdir("{$data_path}temp");

        $this->touchEnqueued(1584118800);
    }

    protected function mkdir($path, $mode = 0777, $recursive = false) {
        if (!is_dir($path)) {
            mkdir($path, $mode, $recursive);
        }
        $this->enqueueForTouch($path);
    }

    protected function copy($source, $dest) {
        if (!is_file($dest)) {
            copy($source, $dest);
        }
        $this->enqueueForTouch($dest);
    }

    protected function mkimg($source_path, $data_path, $destination_relative_path, $width, $height) {
        $destination_path = "{$data_path}{$destination_relative_path}";
        if (is_file($destination_path)) {
            return;
        }
        $tmp_dir = __DIR__.'/data/tmp/';
        if (!is_dir($tmp_dir)) {
            mkdir($tmp_dir);
        }
        $flat_destination_relative_path = str_replace('/', '___', $destination_relative_path);
        $tmp_path = "{$tmp_dir}{$flat_destination_relative_path}";
        if (!is_file($tmp_path)) {
            $info = getimagesize($source_path);
            $source_width = $info[0];
            $source_height = $info[1];
            $source = imagecreatefromjpeg($source_path);
            $destination = imagecreatetruecolor($width, $height);
            imagesavealpha($destination, true);
            imagecopyresampled($destination, $source, 0, 0, 0, 0, $width, $height, $source_width, $source_height);
            $black = imagecolorallocate($destination, 255, 0, 0);
            $hash = intval(substr(md5($destination_relative_path), 0, 1), 16);
            $x = floor($hash / 4) * $width / 4;
            $y = floor($hash % 4) * $height / 4;
            imagefilledrectangle($destination, $x, $y, $x + $width / 4, $y + $height / 4, $black);
            if (preg_match('/\.jpg$/', $destination_relative_path)) {
                imagejpeg($destination, $tmp_path, 90);
            } else {
                imagepng($destination, $tmp_path);
            }
            imagedestroy($destination);
        }
        $this->copy($tmp_path, $destination_path);
    }

    protected function enqueueForTouch($path) {
        $this->enqueuedForTouch[] = $path;
    }

    protected function touchEnqueued($timestamp) {
        foreach ($this->enqueuedForTouch as $path) {
            touch($path, $timestamp, $timestamp);
        }
    }
}