<?php

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
    $db->query('SET foreign_key_checks = 0');
    while ($row = $result->fetch_array()) {
        $table_name = $row[0];
        $db->query("DROP TABLE `{$table_name}`");
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

function init_dev_data_db_content($db, $dev_data_dir) {
    // Insert dev content into database.
    $sql_content = file_get_contents("{$dev_data_dir}db_content.sql");
    if ($db->multi_query($sql_content)) {
        while ($db->next_result()) {
            $result = $db->store_result();
            if ($result) {
                $result->free();
            }
        }
    }
}

function apply_db_migrations() {
    // Migrate database to latest state.
    require_once __DIR__.'/doctrine_migrations.php';
    migrate_to_latest();
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

    $sample_path = __DIR__.'/dev-data/sample-data/';

    // Build downloads/
    mkdir("{$data_path}downloads");

    // Build files/
    mkdir("{$data_path}files");

    // Build img/
    mkdir("{$data_path}img");
    mkimg("{$sample_path}sample-picture.jpg", "{$data_path}img/trophy.png", 140, 140);
    mkdir("{$data_path}img/aktuell");
    mkdir("{$data_path}img/aktuell/3");
    mkdir("{$data_path}img/aktuell/3/img");
    mkimg("{$sample_path}sample-picture.jpg", "{$data_path}img/aktuell/3/img/001.jpg", 800, 600);
    mkimg("{$sample_path}sample-picture.jpg", "{$data_path}img/aktuell/3/img/002.jpg", 800, 600);
    mkdir("{$data_path}img/bild_der_woche");
    mkdir("{$data_path}img/bild_der_woche/2");
    mkdir("{$data_path}img/bild_der_woche/2/img");
    mkimg("{$sample_path}sample-picture.jpg", "{$data_path}img/bild_der_woche/2/img/001.jpg", 800, 600);
    mkdir("{$data_path}img/galerie");
    mkdir("{$data_path}img/galerie/1");
    mkdir("{$data_path}img/galerie/1/img");
    mkimg("{$sample_path}sample-picture.jpg", "{$data_path}img/galerie/1/img/001.jpg", 800, 600);
    mkdir("{$data_path}img/galerie/2");
    mkdir("{$data_path}img/galerie/2/img");
    mkimg("{$sample_path}sample-picture.jpg", "{$data_path}img/galerie/2/img/001.jpg", 800, 600);
    mkimg("{$sample_path}sample-picture.jpg", "{$data_path}img/galerie/2/img/002.jpg", 800, 600);
    mkimg("{$sample_path}sample-picture.jpg", "{$data_path}img/galerie/2/img/003.jpg", 800, 600);
    mkimg("{$sample_path}sample-picture.jpg", "{$data_path}img/galerie/2/img/004.jpg", 800, 600);
    mkimg("{$sample_path}sample-picture.jpg", "{$data_path}img/galerie/2/img/005.jpg", 800, 600);
    mkdir("{$data_path}img/karten");
    mkimg("{$sample_path}sample-picture.jpg", "{$data_path}img/karten/landforst_2017_10000.jpg", 800, 600);
    mkimg("{$sample_path}sample-picture.jpg", "{$data_path}img/karten/horgen_dorfkern_2011_2000.jpg", 800, 600);
    mkdir("{$data_path}img/users");
    mkimg("{$sample_path}sample-picture.jpg", "{$data_path}img/users/1.jpg", 84, 120);
    mkimg("{$sample_path}sample-picture.jpg", "{$data_path}img/users/3.jpg", 84, 120);

    // Build movies/
    mkdir("{$data_path}movies");

    // Build olz_mitglieder/
    mkdir("{$data_path}olz_mitglieder");
    mkimg("{$sample_path}sample-picture.jpg", "{$data_path}olz_mitglieder/max_muster.jpg", 84, 120);

    // Build OLZimmerbergAblage/
    mkdir("{$data_path}OLZimmerbergAblage");
    mkdir("{$data_path}OLZimmerbergAblage/vorstand");
    copy("{$sample_path}sample-document.pdf", "{$data_path}OLZimmerbergAblage/vorstand/protokoll.pdf");
    mkdir("{$data_path}OLZimmerbergAblage/karten");
    copy("{$sample_path}sample-document.pdf", "{$data_path}OLZimmerbergAblage/karten/buchstabenwald.pdf");

    // Build pdf/
    mkdir("{$data_path}pdf");
    copy("{$sample_path}sample-document.pdf", "{$data_path}pdf/trainingsprogramm.pdf");
}

function mkimg($source_path, $destination_path, $width, $height) {
    $info = getimagesize($source_path);
    $source_width = $info[0];
    $source_height = $info[1];
    $source = imagecreatefromjpeg($source_path);
    $destination = imagecreatetruecolor($width, $height);
    imagesavealpha($destination, true);
    imagecopyresampled($destination, $source, 0, 0, 0, 0, $width, $height, $source_width, $source_height);
    if (preg_match('/\.jpg$/', $destination_path)) {
        imagejpeg($destination, $destination_path, 90);
    } else {
        imagepng($destination, $destination_path);
    }
    imagedestroy($destination);
}

function dump_db_structure_sql($db) {
    $sql_content = (
        "-- Die Struktur der Datenbank der Webseite der OL Zimmerberg\n"
        ."\n"
        ."-- NOTE: Database structure is managed by doctrine migrations.\n"
        ."--       This file is only used if migrations bootstrap fails.\n"
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
        $sql_content .= "DROP TABLE IF EXISTS `{$table_name}`;\n";
        $res_structure = $db->query("SHOW CREATE TABLE `{$table_name}`");
        $row_structure = $res_structure->fetch_assoc();
        $structure_sql = $row_structure['Create Table'];
        $sql_content .= "{$structure_sql};\n";
    }
    $sql_content .= "\n";
    $sql_content .= "COMMIT;\n";
    return $sql_content;
}

function dump_db_content_sql($db) {
    $sql_content = (
        "-- Der Test-Inhalt der Datenbank der Webseite der OL Zimmerberg\n"
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
