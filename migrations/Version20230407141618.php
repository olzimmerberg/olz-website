<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Olz\Utils\EnvUtils;
use Olz\Utils\FileUtils;
use Olz\Utils\ImageUtils;

final class Version20230407141618 extends AbstractMigration {
    public function getDescription(): string {
        return 'Migrate news';
    }

    public function up(Schema $schema): void {
        $env_utils = EnvUtils::fromEnv();
        $data_path = $env_utils->getDataPath();
        $rows = $this->connection->fetchAllAssociative('SELECT * FROM aktuell');
        foreach ($rows as $row) {
            $id = $row['id'];
            $format = $row['typ'];
            $text = $row['text'];
            $textlang = $row['textlang'];
            $is_migrated = is_array(json_decode($row['image_ids'] ?? '', true));
            $is_kaderblog_range = $id >= 6400 && $id < 6700;
            $is_forum_range = $id >= 2900 && $id < 6300;
            $is_galerie_range = $id >= 1200 && $id < 2800;

            if ($is_migrated) {
                continue;
            }

            if ($is_kaderblog_range !== ($format === 'kaderblog')) {
                throw new \Exception("Inconsistent kaderblog state for {$id}, format is {$format}");
            }
            if ($is_forum_range !== ($format === 'forum')) {
                throw new \Exception("Inconsistent forum state for {$id}, format is {$format}");
            }
            if ($is_galerie_range !== ($format === 'galerie' || $format === 'video')) {
                throw new \Exception("Inconsistent galerie state for {$id}, format is {$format}");
            }

            $old_id = $id;
            if ($is_kaderblog_range) {
                $old_id = $id - 6400;
            } elseif ($is_forum_range) {
                $old_id = $id - 2900;
            } elseif ($is_galerie_range) {
                $old_id = $id - 1200;
            }

            $old_db_imgpath = ImageUtils::TABLES_IMG_DIRS[$format];
            $old_img_path = "{$data_path}{$old_db_imgpath}{$old_id}/";
            $new_db_imgpath = ImageUtils::TABLES_IMG_DIRS['news'];
            $new_img_path = "{$data_path}{$new_db_imgpath}{$id}/";

            $old_db_filepath = FileUtils::TABLES_FILE_DIRS[$format];
            $old_file_path = "{$data_path}{$old_db_filepath}{$old_id}/";
            $new_db_filepath = FileUtils::TABLES_FILE_DIRS['news'];
            $new_file_path = "{$data_path}{$new_db_filepath}{$id}/";

            $image_ids = [];
            if (is_dir($old_img_path)) {
                @mkdir($new_img_path, 0777, true);
                @mkdir("{$new_img_path}img/");
                @mkdir("{$new_img_path}thumb/");
                for ($size = 1; is_file("{$old_img_path}img/{$this->pad3($size)}.jpg"); $size++) {
                }
                $size--;
                for ($index = 1; $index <= $size; $index++) {
                    $this->rename(
                        "{$old_img_path}img/{$this->pad3($index)}.jpg",
                        "{$new_img_path}img/{$this->pad24($id, $index)}.jpg",
                    );
                    $this->rename(
                        "{$old_img_path}thumb/{$this->pad3($index)}.jpg",
                        "{$new_img_path}thumb/{$this->pad24($id, $index)}.jpg",
                    );
                    $image_ids[] = "{$this->pad24($id, $index)}.jpg";
                }
            }

            if (is_dir($old_file_path)) {
                @mkdir($new_file_path, 0777, true);
                $files = scandir($old_file_path);
                $num_files = count($files);
                for ($i = 0; $i < count($files); $i++) {
                    $res = preg_match("/^([0-9]{3})\\.([a-zA-Z0-9]+)$/", $files[$i], $matches);
                    if (!$res) {
                        continue;
                    }
                    $index = intval($matches[1]);
                    $ext = $matches[2];
                    if ($index <= 0) {
                        continue;
                    }
                    $this->rename(
                        "{$old_file_path}{$this->pad3($index)}.{$ext}",
                        "{$new_file_path}{$this->pad24($id, $index)}.{$ext}",
                    );
                    $text = str_replace(
                        "<DATEI{$index}",
                        "<DATEI={$this->pad24($id, $index)}.{$ext}",
                        $text,
                    );
                    $textlang = str_replace(
                        "<DATEI{$index}",
                        "<DATEI={$this->pad24($id, $index)}.{$ext}",
                        $textlang,
                    );
                }
            }

            $args = [
                'id' => $id,
                'text' => $text,
                'textlang' => $textlang,
                'image_ids' => json_encode($image_ids),
            ];
            $this->addSql("UPDATE aktuell SET text = :text, textlang = :textlang, image_ids = :image_ids WHERE id = :id", $args);
        }
    }

    protected function rename($old_path, $new_path) {
        $is_old_file = is_file($old_path);
        if ($is_old_file) {
            @rename($old_path, $new_path);
        }
    }

    protected function pad3($index) {
        return str_pad("{$index}", 3, '0', STR_PAD_LEFT);
    }

    protected function pad24($id, $index) {
        $id_padded = str_pad("{$id}", 12, '0', STR_PAD_LEFT);
        $index_padded = str_pad("{$index}", 4, '0', STR_PAD_LEFT);
        return "MIGRATED{$id_padded}{$index_padded}";
    }

    public function down(Schema $schema): void {
    }
}
