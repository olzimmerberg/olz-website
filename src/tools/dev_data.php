<?php

/** DO NOT CALL THIS FUNCTION ON PROD! */
function init_dev_data() {
    require_once __DIR__.'/../admin/olz_init.php';

    set_time_limit(120); // This might take some time...

    // Overwrite database with dev content.
    $sql_content = file_get_contents(__DIR__.'/dev-data/db.sql');
    $db->multi_query($sql_content);

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
    mkdir("{$data_path}img/aktuell/1");
    mkdir("{$data_path}img/aktuell/1/img");
    mkimg("{$sample_path}sample-picture.jpg", "{$data_path}img/aktuell/1/img/001.jpg", 800, 600);
    mkimg("{$sample_path}sample-picture.jpg", "{$data_path}img/aktuell/1/img/002.jpg", 800, 600);
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

function remove_r($path) {
    if (is_dir($path)) {
        $contents = glob("{$path}/*");
        foreach ($contents as &$entry) {
            remove_r($entry);
        }
        rmdir($path);
    } elseif (is_file($path)) {
        unlink($path);
    }
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

function dump_dev_db() {
    require_once __DIR__.'/../config/database.php';

    set_time_limit(120); // This might take some time...

    $sql_content = (
        "-- Die Datenbank der Webseite der OL Zimmerberg\n"
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
        $sql_content .= "DROP TABLE IF EXISTS `{$table_name}`;\n";
        $res_structure = $db->query("SHOW CREATE TABLE `{$table_name}`");
        $row_structure = $res_structure->fetch_assoc();
        $structure_sql = $row_structure['Create Table'];
        $sql_content .= "{$structure_sql};\n";

        $res_contents = $db->query("SELECT * FROM `{$table_name}`");
        if ($res_contents->num_rows > 0) {
            $sql_content .= "\n";
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

    file_put_contents(__DIR__.'/dev-data/db.sql', $sql_content);
}
