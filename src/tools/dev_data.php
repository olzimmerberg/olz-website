<?php

require_once __DIR__.'/../config/paths.php';
require_once __DIR__.'/../config/database.php';
require_once __DIR__.'/common.php';

/** DO NOT CALL THIS FUNCTION ON PROD! */
/** TODO: DELETE (only used in reset.php) */
function init_dev_data($db, $data_path) {
    return reset_db($db, $data_path);
}

/** DO NOT CALL THIS FUNCTION ON PROD! */
function reset_db($db, $data_path) {
    $dev_data_dir = __DIR__.'/dev-data/';

    // Overwrite database with dev content.
    clear_db($db);
    // init_dev_data_db_structure_for_content($db, $dev_data_dir);
    init_dev_data_db_structure($db, $dev_data_dir);
    init_dev_data_db_content($db, $dev_data_dir);
    apply_db_migrations();

    // Initialize the non-code data file system at $data_path
    init_dev_data_filesystem($data_path);
}

function dump_db($db) {
    $dev_data_dir = __DIR__.'/dev-data/';

    $sql_structure = dump_db_structure_sql($db);
    $sql_content = dump_db_content_sql($db);
    $sql_structure_path = "{$dev_data_dir}db_structure.sql";
    $sql_content_path = "{$dev_data_dir}db_content.sql";
    file_put_contents($sql_structure_path, $sql_structure);
    file_put_contents($sql_content_path, $sql_content);
}

function get_database_backup($db, $key) {
    if (!$key || strlen($key) < 10) {
        throw new Exception("No valid key");
    }
    $sql = '';
    $sql .= dump_db_structure_sql($db);
    $sql .= "\n\n----------\n\n\n";
    $sql .= dump_db_content_sql($db);

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

function clear_db($db) {
    // Remove all database tables.
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
}

function init_dev_data_db_structure($db, $dev_data_dir) {
    // Overwrite database structure with dev content.
    $sql_content = file_get_contents("{$dev_data_dir}db_structure.sql");
    if ($db->multi_query($sql_content)) {
        while ($db->next_result()) {
            $result = $db->store_result();
            if ($result) {
                $result->free();
            }
        }
    }
}

function init_dev_data_db_structure_for_content($db, $dev_data_dir) {
    // Insert dev content into database.
    $sql_content = file_get_contents("{$dev_data_dir}db_content.sql");
    $has_migration = preg_match('/-- MIGRATION: ([a-zA-Z0-9\\\\]+)\\s+/', $sql_content, $matches);
    if (!$has_migration) {
        throw new Exception("The db_content.sql file MUST contain the migration version", 1);
    }
    $version = $matches[1];

    require_once __DIR__.'/doctrine_migrations.php';
    migrate_to($version);
}

function init_dev_data_db_content($db, $dev_data_dir) {
    // Insert dev content into database.
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
}

function apply_db_migrations() {
    // Migrate database to latest state.
    require_once __DIR__.'/doctrine_migrations.php';
    migrate_to('latest');
}

function init_dev_data_filesystem($data_path) {
    // Remove existing data.
    remove_r("{$data_path}downloads");
    remove_r("{$data_path}files");
    remove_r("{$data_path}img");
    remove_r("{$data_path}movies");
    remove_r("{$data_path}olz_mitglieder");
    remove_r("{$data_path}OLZimmerbergAblage");
    remove_r("{$data_path}pdf");
    remove_r("{$data_path}results");
    remove_r("{$data_path}temp");

    $sample_path = __DIR__.'/dev-data/sample-data/';

    // Build downloads/
    mkdir("{$data_path}downloads");

    // Build files/
    mkdir("{$data_path}files");
    mkdir("{$data_path}files/aktuell");
    mkdir("{$data_path}files/blog");
    mkdir("{$data_path}files/blog/1");
    copy("{$sample_path}sample-document.pdf", "{$data_path}files/blog/1/001.pdf");
    mkdir("{$data_path}files/downloads");
    mkdir("{$data_path}files/termine");
    mkdir("{$data_path}files/termine/2");
    copy("{$sample_path}sample-document.pdf", "{$data_path}files/termine/2/001.pdf");

    // Build img/
    mkdir("{$data_path}img");
    mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/trophy.png", 140, 140);
    mkdir("{$data_path}img/aktuell");
    mkdir("{$data_path}img/aktuell/3");
    mkdir("{$data_path}img/aktuell/3/img");
    mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/aktuell/3/img/001.jpg", 800, 600);
    mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/aktuell/3/img/002.jpg", 800, 600);
    mkdir("{$data_path}img/blog");
    mkdir("{$data_path}img/blog/1");
    mkdir("{$data_path}img/blog/1/img");
    mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/blog/1/img/001.jpg", 800, 600);
    mkdir("{$data_path}img/bild_der_woche");
    mkdir("{$data_path}img/bild_der_woche/2");
    mkdir("{$data_path}img/bild_der_woche/2/img");
    mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/bild_der_woche/2/img/001.jpg", 800, 600);
    mkdir("{$data_path}img/fuer_einsteiger");
    mkdir("{$data_path}img/fuer_einsteiger/img");
    mkdir("{$data_path}img/fuer_einsteiger/thumb");
    mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/orientierungslauf_001.jpg", 800, 600);
    mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/orientierungslauf_002.jpg", 800, 600);
    mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/orientierungslauf_003.jpg", 800, 600);
    mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/orientierungslauf_004.jpg", 800, 600);
    mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/was_ist_ol_001.jpg", 800, 600);
    mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/ol_zimmerberg_001.jpg", 800, 600);
    mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/ol_zimmerberg_002.jpg", 800, 600);
    mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/ol_zimmerberg_003.jpg", 800, 600);
    mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/ol_zimmerberg_004.jpg", 800, 600);
    mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/ol_zimmerberg_005.jpg", 800, 600);
    mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/ol_zimmerberg_006.jpg", 800, 600);
    mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/ol_zimmerberg_007.jpg", 800, 600);
    mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/ol_zimmerberg_008.jpg", 800, 600);
    mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/ol_zimmerberg_009.jpg", 800, 600);
    mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/ol_zimmerberg_010.jpg", 800, 600);
    mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/ol_zimmerberg_011.jpg", 800, 600);
    mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/ol_zimmerberg_012.jpg", 800, 600);
    mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/ol_zimmerberg_013.jpg", 800, 600);
    mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/ol_zimmerberg_014.jpg", 800, 600);
    mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/ol_zimmerberg_015.jpg", 800, 600);
    mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/ol_zimmerberg_016.jpg", 800, 600);
    mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/wie_anfangen_001.jpg", 800, 600);
    mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/wie_anfangen_002.jpg", 800, 600);
    mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/wie_anfangen_003.jpg", 800, 600);
    mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/wie_anfangen_004.jpg", 800, 600);
    mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/trainings_001.jpg", 800, 600);
    mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/trainings_002.jpg", 800, 600);
    mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/trainings_003.jpg", 800, 600);
    mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/trainings_004.jpg", 800, 600);
    mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/trainings_005.jpg", 800, 600);
    mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/trainings_006.jpg", 800, 600);
    mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/trainings_007.jpg", 800, 600);
    mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/trainings_008.jpg", 800, 600);
    mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/trainings_009.jpg", 800, 600);
    mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/trainings_010.jpg", 800, 600);
    mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/trainings_011.jpg", 800, 600);
    mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/trainings_012.jpg", 800, 600);
    mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/trainings_013.jpg", 800, 600);
    mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/trainings_014.jpg", 800, 600);
    mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/trainings_015.jpg", 800, 600);
    mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/trainings_016.jpg", 800, 600);
    mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/pack_die_chance_001.jpg", 800, 600);
    mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/ansprechperson_001.jpg", 800, 600);
    mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/ansprechperson_002.jpg", 800, 600);
    mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/ansprechperson_003.jpg", 800, 600);
    mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/fuer_einsteiger/img/ansprechperson_004.jpg", 800, 600);

    // Generate thumbs
    copy(__DIR__."/../../tools/fuer_einsteiger/thumbize.sh", "{$data_path}img/fuer_einsteiger/thumbize.sh");
    $pwd = getcwd();
    chdir("{$data_path}img/fuer_einsteiger");
    shell_exec("sh ./thumbize.sh");
    chdir($pwd);

    mkdir("{$data_path}img/galerie");
    mkdir("{$data_path}img/galerie/1");
    mkdir("{$data_path}img/galerie/1/img");
    mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/galerie/1/img/001.jpg", 800, 600);
    mkdir("{$data_path}img/galerie/2");
    mkdir("{$data_path}img/galerie/2/img");
    mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/galerie/2/img/001.jpg", 800, 600);
    mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/galerie/2/img/002.jpg", 800, 600);
    mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/galerie/2/img/003.jpg", 800, 600);
    mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/galerie/2/img/004.jpg", 800, 600);
    mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/galerie/2/img/005.jpg", 800, 600);
    mkdir("{$data_path}img/karten");
    mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/karten/landforst_2017_10000.jpg", 800, 600);
    mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/karten/horgen_dorfkern_2011_2000.jpg", 800, 600);
    mkdir("{$data_path}img/users");
    mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/users/1.jpg", 84, 120);
    mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/users/1@2x.jpg", 168, 240);
    mkimg("{$sample_path}sample-picture.jpg", $data_path, "img/users/3.jpg", 84, 120);

    // Build movies/
    mkdir("{$data_path}movies");

    // Build olz_mitglieder/
    mkdir("{$data_path}olz_mitglieder");
    mkimg("{$sample_path}sample-picture.jpg", $data_path, "olz_mitglieder/max_muster.jpg", 84, 120);

    // Build OLZimmerbergAblage/
    mkdir("{$data_path}OLZimmerbergAblage");
    mkdir("{$data_path}OLZimmerbergAblage/vorstand");
    copy("{$sample_path}sample-document.pdf", "{$data_path}OLZimmerbergAblage/vorstand/mitgliederliste.pdf");
    mkdir("{$data_path}OLZimmerbergAblage/vorstand/protokolle");
    copy("{$sample_path}sample-document.pdf", "{$data_path}OLZimmerbergAblage/vorstand/protokolle/protokoll.pdf");
    mkdir("{$data_path}OLZimmerbergAblage/karten");
    copy("{$sample_path}sample-document.pdf", "{$data_path}OLZimmerbergAblage/karten/uebersicht.pdf");
    mkdir("{$data_path}OLZimmerbergAblage/karten/wald");
    copy("{$sample_path}sample-document.pdf", "{$data_path}OLZimmerbergAblage/karten/wald/buchstabenwald.pdf");

    // Build pdf/
    mkdir("{$data_path}pdf");
    copy("{$sample_path}sample-document.pdf", "{$data_path}pdf/trainingsprogramm.pdf");

    // Build results/
    mkdir("{$data_path}results");
    copy("{$sample_path}sample-results.xml", "{$data_path}results/results.xml");

    // Build temp/
    mkdir("{$data_path}temp");
}

function mkimg($source_path, $data_path, $destination_relative_path, $width, $height) {
    $tmp_dir = __DIR__.'/dev-data/tmp/';
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
    $destination_path = "{$data_path}{$destination_relative_path}";
    copy($tmp_path, $destination_path);
}

function get_current_migration($db) {
    $migrations_config = require __DIR__.'/../config/migrations.php';
    $migrations_table_name = $migrations_config['table_storage']['table_name'];

    $current_migration_result = $db->query("
        SELECT version
        FROM `{$migrations_table_name}`
        ORDER BY `version` DESC
        LIMIT 1");
    $current_migration = $current_migration_result->fetch_assoc()['version'];
    return $current_migration;
}

function dump_db_structure_sql($db) {
    $current_migration = get_current_migration($db);
    $sql_content = (
        "-- Die Struktur der Datenbank der Webseite der OL Zimmerberg\n"
        ."-- MIGRATION: {$current_migration}\n"
        ."\n"
    );
    global $_CONFIG;
    require_once __DIR__.'/../config/database.php';
    require_once __DIR__.'/../config/vendor/autoload.php';
    $dump_filename = tempnam('/tmp', 'OLZ');
    $mysql_server = $_CONFIG->getMysqlServer();
    $mysql_schema = $_CONFIG->getMysqlSchema();
    $dump = new Ifsnop\Mysqldump\Mysqldump(
        "mysql:host={$mysql_server};dbname={$mysql_schema}",
        $_CONFIG->getMysqlUsername(),
        $_CONFIG->getMysqlPassword(),
        [
            'skip-comments' => true,
            'no-data' => true,
        ],
    );
    $dump->start($dump_filename);
    $sql_content .= file_get_contents($dump_filename);
    unlink($dump_filename);

    return $sql_content;
}

function dump_db_content_sql($db) {
    $current_migration = get_current_migration($db);
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
                        $sane_content = DBEsc("{$content}");
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
