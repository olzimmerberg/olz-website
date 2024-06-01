<?php

namespace Olz\Utils;

use Ifsnop\Mysqldump\Mysqldump;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class DevDataUtils {
    use WithUtilsTrait;

    /** @var array<string> */
    private array $enqueuedForTouch = [];

    /** DO NOT CALL THIS FUNCTION ON PROD! */
    public function fullResetDb(): void {
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
    public function resetDbStructure(): void {
        // Overwrite database with dev content.
        $this->dropDbTables();
        $this->addDbStructure();
        $this->addDbContent();
        $this->migrateTo('latest');

        // Initialize the non-code data file system at $data_path
        $this->addFiles();
    }

    /** DO NOT CALL THIS FUNCTION ON PROD! */
    public function resetDbContent(): void {
        $this->truncateDbTables();
        $this->addDbContent();
    }

    /** DO NOT CALL THIS FUNCTION ON PROD! */
    public function dropDbTables(): void {
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
    public function truncateDbTables(): void {
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

    public function addDbStructure(): void {
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

    public function addDbContent(): void {
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

    public function getCurrentMigration(): ?string {
        $input = new ArrayInput(['--no-interaction' => true]);
        $input->setInteractive(false);
        $output = new BufferedOutput();
        $this->symfonyUtils()->callCommand(
            'doctrine:migrations:current',
            $input,
            $output
        );
        $is_match = preg_match('/^\s*([a-zA-Z0-9\\\\]+)(\s|$)/', $output->fetch(), $matches);
        return $is_match ? $matches[1] ?? null : null;
    }

    public function generateMigration(): string {
        $input = new ArrayInput([
            '--no-interaction' => true,
        ]);
        $input->setInteractive(false);
        $output = new BufferedOutput();
        $this->symfonyUtils()->callCommand(
            'doctrine:migrations:diff',
            $input,
            $output
        );
        return $output->fetch();
    }

    public function migrateTo(string $version = 'latest'): string {
        $input = new ArrayInput([
            'version' => $version,
            '--no-interaction' => true,
        ]);
        $input->setInteractive(false);
        $output = new BufferedOutput();
        $this->symfonyUtils()->callCommand(
            'doctrine:migrations:migrate',
            $input,
            $output
        );
        return $output->fetch();
    }

    public function getDbBackup(string $key): void {
        $db = $this->dbUtils()->getDb();

        if (!$key || strlen($key) < 10) {
            throw new \Exception("No valid key");
        }

        $tmp_dir = __DIR__.'/data/tmp/';
        if (!is_dir($tmp_dir)) {
            mkdir($tmp_dir);
        }

        $plain_path = "{$tmp_dir}backup.plain.sql";
        $plain_fp = fopen($plain_path, 'w+');
        $sql = '';
        fwrite($plain_fp, $this->getDbStructureSql());
        fwrite($plain_fp, "\n\n----------\n\n\n");
        fwrite($plain_fp, $this->getDbContentSql());
        fclose($plain_fp);

        $cipher_path = "{$tmp_dir}backup.cipher.sql";
        $cipher_fp = fopen($cipher_path, 'w+');
        $algo = 'aes-256-gcm';
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($algo));
        fwrite($cipher_fp, openssl_encrypt(file_get_contents($plain_path), $algo, $key, OPENSSL_RAW_DATA, $iv, $tag));
        fclose($cipher_fp);

        unlink($plain_path);

        echo json_encode([
            'algo' => $algo,
            'iv' => base64_encode($iv),
            'tag' => base64_encode($tag),
            'ciphertext' => base64_encode(file_get_contents($cipher_path)),
        ]);
        echo "\n";

        unlink($cipher_path);
    }

    public function dumpDb(): void {
        $dev_data_dir = __DIR__.'/data/';

        $sql_structure = $this->getDbStructureSql();
        $sql_content = $this->getDbContentSql();
        $sql_structure_path = "{$dev_data_dir}db_structure.sql";
        $sql_content_path = "{$dev_data_dir}db_content.sql";
        file_put_contents($sql_structure_path, $sql_structure);
        file_put_contents($sql_content_path, $sql_content);
    }

    public function getDbStructureSql(): string {
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

    public function getDbContentSql(): string {
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
            if ($table_name === 'counter') {
                $sql_content .= "-- (counter omitted)\n";
            } elseif ($table_name === 'auth_requests') {
                $sql_content .= "-- (auth_requests omitted)\n";
            } elseif ($res_contents->num_rows > 0) {
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
                        $content = $row_contents[$name] ?? null;
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

    public function clearFiles(): void {
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
        $general_utils->removeRecursive("{$data_path}panini_data");
        $general_utils->removeRecursive("{$data_path}pdf");
        $general_utils->removeRecursive("{$data_path}results");
        $general_utils->removeRecursive("{$data_path}temp");
    }

    public function addFiles(): void {
        $env_utils = $this->envUtils();
        $data_path = $env_utils->getDataPath();

        $sample_path = __DIR__.'/data/sample-data/';

        $this->enqueuedForTouch = [];

        // Build downloads/
        $this->mkdir("{$data_path}downloads");

        // Build files/
        $this->mkdir("{$data_path}files");
        $this->mkdir("{$data_path}files/downloads");
        $this->mkdir("{$data_path}files/downloads/1");
        $this->copy("{$sample_path}sample-document.pdf", "{$data_path}files/downloads/1/MIGRATED0000000000010001.pdf");
        $this->mkdir("{$data_path}files/downloads/3");
        $this->copy("{$sample_path}sample-document.pdf", "{$data_path}files/downloads/3/XV4x94BJaf2JCPWvB8DDqTyt.pdf");

        $this->mkdir("{$data_path}files/news");
        $this->mkdir("{$data_path}files/news/3");
        $this->copy("{$sample_path}sample-document.pdf", "{$data_path}files/news/3/MIGRATED0000000000030001.pdf");
        $this->mkdir("{$data_path}files/news/4");
        $this->copy("{$sample_path}sample-document.pdf", "{$data_path}files/news/4/xMpu3ExjfBKa8Cp35bcmsDgq.pdf");
        $this->mkdir("{$data_path}files/news/10");
        $this->copy("{$sample_path}sample-document.pdf", "{$data_path}files/news/10/gAQa_kYXqXTP1_DKKU1s1pGr.csv");
        $this->copy("{$sample_path}sample-document.pdf", "{$data_path}files/news/10/8kCalo9sQtu2mrgrmMjoGLUW.pdf");
        $this->mkdir("{$data_path}files/news/6403");
        $this->copy("{$sample_path}sample-document.pdf", "{$data_path}files/news/6403/MIGRATED0000000064030001.pdf");

        $this->mkdir("{$data_path}files/roles");
        $this->mkdir("{$data_path}files/roles/5");
        $this->copy("{$sample_path}sample-document.pdf", "{$data_path}files/roles/5/c44s3s8QjwZd2WYTEVg3iW9k.pdf");

        $this->mkdir("{$data_path}files/snippets");
        $this->mkdir("{$data_path}files/snippets/24");
        $this->copy("{$sample_path}sample-document.pdf", "{$data_path}files/snippets/24/AXfZYP3eyLKTWJmfBRGTua7H.pdf");

        $this->mkdir("{$data_path}files/termine");
        $this->mkdir("{$data_path}files/termine/2");
        $this->copy("{$sample_path}sample-document.pdf", "{$data_path}files/termine/2/MIGRATED0000000000020001.pdf");
        $this->mkdir("{$data_path}files/termine/5");
        $this->copy("{$sample_path}sample-document.pdf", "{$data_path}files/termine/5/MIGRATED0000000000050001.pdf");
        $this->mkdir("{$data_path}files/termine/7");
        $this->copy("{$sample_path}sample-document.pdf", "{$data_path}files/termine/7/Kzt5p5g6cjM5k9CXdVaSsGFx.pdf");

        $this->mkdir("{$data_path}files/termin_labels");
        $this->mkdir("{$data_path}files/termin_labels/3");
        $this->copy("{$sample_path}sample-document.pdf", "{$data_path}files/termin_labels/3/6f6novQPv2fjHGzzguXE6nzi.pdf");
        $this->mkdir("{$data_path}files/termin_labels/4");
        $this->copy("{$sample_path}sample-icon_20.svg", "{$data_path}files/termin_labels/4/EM8hA6vye74doeon2RWzZyRf.svg");

        $this->mkdir("{$data_path}files/termin_templates");
        $this->mkdir("{$data_path}files/termin_templates/2");
        $this->copy("{$sample_path}sample-document.pdf", "{$data_path}files/termin_templates/2/qjhUey6Lc6svXsmUcSaguWkJ.pdf");

        // Build img/
        $this->mkdir("{$data_path}img");
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/trophy.png", 140, 140);
        $this->mkdir("{$data_path}img/weekly_picture");
        $this->mkdir("{$data_path}img/weekly_picture/1");
        $this->mkdir("{$data_path}img/weekly_picture/1/img");
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/weekly_picture/1/img/001.jpg", 800, 600);
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

        $this->mkdir("{$data_path}img/karten");
        $this->mkdir("{$data_path}img/karten/1");
        $this->mkdir("{$data_path}img/karten/1/img");
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/karten/1/img/MIGRATED0000000000010001.jpg", 800, 600);
        $this->mkdir("{$data_path}img/karten/3");
        $this->mkdir("{$data_path}img/karten/3/img");
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/karten/3/img/6R3bpgwcCU3SfUF8vCpepzRJ.jpg", 800, 600);

        $this->mkdir("{$data_path}img/news");
        $this->mkdir("{$data_path}img/news/3");
        $this->mkdir("{$data_path}img/news/3/img");
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/news/3/img/MIGRATED0000000000030001.jpg", 800, 600);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/news/3/img/MIGRATED0000000000030002.jpg", 800, 600);
        $this->mkdir("{$data_path}img/news/4");
        $this->mkdir("{$data_path}img/news/4/img");
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/news/4/img/xkbGJQgO5LFXpTSz2dCnvJzu.jpg", 800, 600);
        $this->mkdir("{$data_path}img/news/6");
        $this->mkdir("{$data_path}img/news/6/img");
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/news/6/img/eGbiJQgOyLF5p6S92kC3vTzE.jpg", 600, 800);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/news/6/img/Frw83uTOyLF5p6S92kC7zpEW.jpg", 800, 600);
        $this->mkdir("{$data_path}img/news/7");
        $this->mkdir("{$data_path}img/news/7/img");
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/news/7/img/aRJIflbxtkF5p6S92k470912.jpg", 800, 600);
        $this->mkdir("{$data_path}img/news/8");
        $this->mkdir("{$data_path}img/news/8/img");
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/news/8/img/9GjbtlsSu96AWZ-oH0rHjxup.jpg", 800, 600);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/news/8/img/zUXE3aKfbK3edmqS35FhaF8g.jpg", 800, 600);
        $this->mkdir("{$data_path}img/news/10");
        $this->mkdir("{$data_path}img/news/10/img");
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/news/10/img/DvDB8QkHcGuxQ4lAFwyvHnVd.jpg", 800, 600);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/news/10/img/OOVJIqrWlitR_iTZuIIhztKC.jpg", 800, 600);
        $this->mkdir("{$data_path}img/news/1201");
        $this->mkdir("{$data_path}img/news/1201/img");
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/news/1201/img/MIGRATED0000000012010001.jpg", 800, 600);
        $this->mkdir("{$data_path}img/news/1202");
        $this->mkdir("{$data_path}img/news/1202/img");
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/news/1202/img/MIGRATED0000000012020001.jpg", 800, 600);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/news/1202/img/MIGRATED0000000012020002.jpg", 800, 600);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/news/1202/img/MIGRATED0000000012020003.jpg", 600, 800);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/news/1202/img/MIGRATED0000000012020004.jpg", 800, 600);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/news/1202/img/MIGRATED0000000012020005.jpg", 800, 300);
        $this->mkdir("{$data_path}img/news/1203");
        $this->mkdir("{$data_path}img/news/1203/img");
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/news/1203/img/MIGRATED0000000012030001.jpg", 800, 600);
        $this->mkdir("{$data_path}img/news/1203/thumb");
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/news/1203/thumb/MIGRATED0000000012030001.jpg", 120, 80);
        $this->mkdir("{$data_path}img/news/1206");
        $this->mkdir("{$data_path}img/news/1206/img");
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/news/1206/img/MIGRATED0000000012060001.jpg", 800, 600);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/news/1206/img/MIGRATED0000000012060002.jpg", 800, 600);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/news/1206/img/MIGRATED0000000012060003.jpg", 800, 600);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/news/1206/img/MIGRATED0000000012060004.jpg", 800, 600);
        $this->mkdir("{$data_path}img/news/1206/thumb");
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/news/1206/thumb/MIGRATED0000000012060001.jpg", 120, 80);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/news/1206/thumb/MIGRATED0000000012060002.jpg", 120, 80);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/news/1206/thumb/MIGRATED0000000012060003.jpg", 120, 80);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/news/1206/thumb/MIGRATED0000000012060004.jpg", 120, 80);
        $this->mkdir("{$data_path}img/news/6401");
        $this->mkdir("{$data_path}img/news/6401/img");
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/news/6401/img/MIGRATED0000000064010001.jpg", 800, 600);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/news/6401/img/MIGRATED0000000064010002.jpg", 800, 600);
        $this->mkdir("{$data_path}img/news/6403");
        $this->mkdir("{$data_path}img/news/6403/img");
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/news/6403/img/MIGRATED0000000064030001.jpg", 800, 600);

        $this->mkdir("{$data_path}img/roles");
        $this->mkdir("{$data_path}img/roles/5");
        $this->mkdir("{$data_path}img/roles/5/img");
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/roles/5/img/ZntVatFCHj3h8KZh7LyiB9x5.jpg", 800, 600);

        $this->mkdir("{$data_path}img/snippets");
        $this->mkdir("{$data_path}img/snippets/24");
        $this->mkdir("{$data_path}img/snippets/24/img");
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/snippets/24/img/oCGvpb96V6bZNLoQNe8djJgw.jpg", 800, 600);

        $this->mkdir("{$data_path}img/termine");
        $this->mkdir("{$data_path}img/termine/5");
        $this->mkdir("{$data_path}img/termine/5/img");
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/termine/5/img/Ffpi3PK5wBjKfN4etpvGK3ti.jpg", 800, 600);

        $this->mkdir("{$data_path}img/termin_labels");
        $this->mkdir("{$data_path}img/termin_labels/3");
        $this->mkdir("{$data_path}img/termin_labels/3/img");
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/termin_labels/3/img/QQ8ZApZjsNSBM2wKrkRQxXZG.jpg", 800, 600);

        $this->mkdir("{$data_path}img/termin_locations");
        $this->mkdir("{$data_path}img/termin_locations/1");
        $this->mkdir("{$data_path}img/termin_locations/1/img");
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/termin_locations/1/img/2ZiW6T9biPNjEERzj5xjLRDz.jpg", 800, 600);

        $this->mkdir("{$data_path}img/termin_templates");
        $this->mkdir("{$data_path}img/termin_templates/1");
        $this->mkdir("{$data_path}img/termin_templates/1/img");
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/termin_templates/1/img/bv3KeYVKDJNg3MTyjhSQsDRx.jpg", 800, 600);

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

        // Build panini_data/
        $this->mkdir("{$data_path}panini_data");
        $this->mkdir("{$data_path}panini_data/cache");
        $this->mkdir("{$data_path}panini_data/fonts");
        $this->mkdir("{$data_path}panini_data/fonts/OpenSans");
        $this->copy("{$sample_path}sample-font.ttf", "{$data_path}panini_data/fonts/OpenSans/OpenSans-SemiBold.ttf");
        $this->copy("{$sample_path}sample-font.php", "{$data_path}panini_data/fonts/OpenSans/OpenSans-SemiBold.php");
        $this->copy("{$sample_path}sample-font.z", "{$data_path}panini_data/fonts/OpenSans/OpenSans-SemiBold.z");
        $this->mkdir("{$data_path}panini_data/masks");
        $this->mkimg("{$sample_path}sample-mask.png", $data_path, "panini_data/masks/topP_1517x2091.png", 1517, 2091);
        $this->mkimg("{$sample_path}sample-mask.png", $data_path, "panini_data/masks/bottomP_1517x2091.png", 1517, 2091);
        $this->mkimg("{$sample_path}sample-mask.png", $data_path, "panini_data/masks/associationP_1517x2091.png", 1517, 2091);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "panini_data/masks/associationStencilP_1517x2091.png", 1517, 2091);
        $this->mkimg("{$sample_path}sample-mask.png", $data_path, "panini_data/masks/topP_1594x2303.png", 1594, 2303);
        $this->mkimg("{$sample_path}sample-mask.png", $data_path, "panini_data/masks/topL_2303x1594.png", 2303, 1594);
        $this->mkimg("{$sample_path}sample-mask.png", $data_path, "panini_data/masks/bottomP_1594x2303.png", 1594, 2303);
        $this->mkimg("{$sample_path}sample-mask.png", $data_path, "panini_data/masks/bottomL_2303x1594.png", 2303, 1594);
        $this->mkimg("{$sample_path}sample-mask.png", $data_path, "panini_data/masks/associationP_1594x2303.png", 1594, 2303);
        $this->mkimg("{$sample_path}sample-mask.png", $data_path, "panini_data/masks/associationL_2303x1594.png", 2303, 1594);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "panini_data/masks/associationStencilP_1594x2303.png", 1594, 2303);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "panini_data/masks/associationStencilL_2303x1594.png", 2303, 1594);
        $this->mkdir("{$data_path}panini_data/wappen");
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "panini_data/wappen/thalwil.jpg", 100, 100);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "panini_data/wappen/other.jpg", 100, 100);
        $this->mkdir("{$data_path}panini_data/portraits");
        $this->mkdir("{$data_path}panini_data/portraits/1001");
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "panini_data/portraits/1001/vptD8fzvXIhv_6X32Zkw2s5s.jpg", 800, 600);
        $this->mkdir("{$data_path}panini_data/portraits/1002");
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "panini_data/portraits/1002/LkGdXukqgYEdnWpuFHfrJkr7.jpg", 800, 600);
        $this->mkdir("{$data_path}panini_data/other");
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "panini_data/other/portrait.jpg", 600, 800);
        $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "panini_data/other/landscape.jpg", 800, 600);
        for ($i = 1003; $i <= 1012; $i++) {
            $this->mkdir("{$data_path}panini_data/portraits/{$i}");
            $this->mkimg("{$sample_path}sample-picture.jpg", $data_path, "panini_data/portraits/{$i}/LkGdXukqgYEdnWpuFHfrJkr7.jpg", 800, 600);
        }

        // Build pdf/
        $this->mkdir("{$data_path}pdf");
        $this->copy("{$sample_path}sample-document.pdf", "{$data_path}pdf/trainingsprogramm.pdf");

        // Build results/
        $this->mkdir("{$data_path}results");
        $this->copy("{$sample_path}sample-results.xml", "{$data_path}results/results.xml");
        $this->copy("{$sample_path}sample-results.xml", "{$data_path}results/2020-termine-7.xml");

        // Build temp/
        $this->mkdir("{$data_path}temp");

        // Build logs/
        $this->mkdir("{$data_path}logs");
        $this->mklog("{$data_path}logs/merged-2020-08-13.log", "2020-08-13");
        $this->mklog("{$data_path}logs/merged-2020-08-14.log", "2020-08-14");
        $this->mklog("{$data_path}logs/merged-2020-08-15.log", "2020-08-15");
        $this->mkdir("{$data_path}logs/server");
        $this->mklog("{$data_path}logs/server/access_ssl_log", "2020-08-15");
        $this->mklog("{$data_path}logs/server/access_ssl_log.processed", "2020-08-14");
        $this->mklog("{$data_path}logs/server/access_ssl_log.processed.1", "2020-08-13");
        $this->mklog("{$data_path}logs/server/access_ssl_log.processed.2", "2020-08-12");
        $this->touchEnqueued(1584118800);
    }

    protected function mkdir(string $path, int $mode = 0o777, bool $recursive = false): void {
        if (!is_dir($path)) {
            mkdir($path, $mode, $recursive);
        }
        $this->enqueueForTouch($path);
    }

    protected function copy(string $source, string $dest): void {
        if (!is_file($dest)) {
            copy($source, $dest);
        }
        $this->enqueueForTouch($dest);
    }

    protected function mkimg(
        string $source_path,
        string $data_path,
        string $destination_relative_path,
        int $width,
        int $height,
    ): void {
        $destination_path = "{$data_path}{$destination_relative_path}";
        if (is_file($destination_path)) {
            return;
        }
        $tmp_dir = __DIR__.'/data/tmp/';
        if (!is_dir($tmp_dir)) {
            mkdir($tmp_dir);
        }
        $flat_destination_relative_path = str_replace('/', '___', $destination_relative_path);
        $extension_pos = strrpos($flat_destination_relative_path, '.');
        $ident = substr($flat_destination_relative_path, 0, $extension_pos);
        $extension = substr($flat_destination_relative_path, $extension_pos);
        $tmp_basename = "{$ident}___{$width}x{$height}{$extension}";
        $tmp_path = "{$tmp_dir}{$tmp_basename}";
        if (!is_file($tmp_path)) {
            $info = getimagesize($source_path);
            $source_width = $info[0];
            $source_height = $info[1];
            try {
                $source = imagecreatefromjpeg($source_path);
            } catch (\Throwable $th) {
                $source = imagecreatefrompng($source_path);
            }
            $destination = imagecreatetruecolor($width, $height);
            imagealphablending($destination, false);
            imagesavealpha($destination, true);
            imagecopyresampled(
                $destination,
                $source,
                0,
                0,
                0,
                0,
                $width,
                $height,
                $source_width,
                $source_height,
            );
            $red = imagecolorallocate($destination, 255, 0, 0);
            $hash = intval(substr(md5($destination_relative_path), 0, 1), 16);
            $x = floor($hash / 4) * $width / 4;
            $y = floor($hash % 4) * $height / 4;
            imagefilledrectangle(
                $destination,
                intval(round($x)),
                intval(round($y)),
                intval(round($x + $width / 4)),
                intval(round($y + $height / 4)),
                $red
            );
            if (preg_match('/\.jpg$/', $destination_relative_path)) {
                imagejpeg($destination, $tmp_path, 90);
            } else {
                imagepng($destination, $tmp_path);
            }
            imagedestroy($destination);
        }
        $this->copy($tmp_path, $destination_path);
    }

    protected function mklog(string $file_path, string $iso_date): void {
        $log_levels = [
            'DEBUG',
            'INFO',
            'NOTICE',
            'WARNING',
            'ERROR',
            'CRITICAL',
            'ALERT',
            'EMERGENCY',
        ];
        $num_log_levels = count($log_levels);
        $fp = fopen($file_path, 'w+');
        $long_line = 'Wow,';
        for ($i = 0; $i < 1000; $i++) {
            $long_line .= ' so much content';
        }
        for ($i = 0; $i < 1440; $i++) {
            $time = str_pad(strval(floor($i / 60)), 2, '0', STR_PAD_LEFT).':'.
                str_pad(strval(floor($i % 60)), 2, '0', STR_PAD_LEFT).':'.
                str_pad(strval(random_int(0, 59)), 2, '0', STR_PAD_LEFT).'.'.
                str_pad(strval(random_int(0, 999999)), 6, '0', STR_PAD_LEFT);
            $level = $log_levels[$i % $num_log_levels];
            $fill_up = ($i % ($num_log_levels + 1)) === 0 ? $long_line : '';
            $line = "[{$iso_date}T{$time}+01:00] Command:ProcessEmail.{$level}: Something happened... {$fill_up} [] []\n";
            fwrite($fp, $line);
        }
        fclose($fp);
    }

    protected function enqueueForTouch(string $path): void {
        $this->enqueuedForTouch[] = $path;
    }

    protected function touchEnqueued(?int $timestamp): void {
        foreach ($this->enqueuedForTouch as $path) {
            touch($path, $timestamp, $timestamp);
        }
    }

    public static function fromEnv(): self {
        return new self();
    }
}
