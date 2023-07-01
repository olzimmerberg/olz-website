<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Olz\Utils\EnvUtils;
use Olz\Utils\FileUtils;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230701122001 extends AbstractMigration {
    public function getDescription(): string {
        return 'Migrate termine';
    }

    public function up(Schema $schema): void {
        $env_utils = EnvUtils::fromEnv();
        $data_path = $env_utils->getDataPath();
        $rows = $this->connection->fetchAllAssociative('SELECT * FROM termine');
        foreach ($rows as $row) {
            $id = $row['id'];
            $link = $row['link'];

            $db_filepath = FileUtils::TABLES_FILE_DIRS['termine'];
            $file_path = "{$data_path}{$db_filepath}{$id}/";

            if (is_dir($file_path)) {
                $files = scandir($file_path);
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
                        "{$file_path}{$this->pad3($index)}.{$ext}",
                        "{$file_path}{$this->pad24($id, $index)}.{$ext}",
                    );
                    $link = str_replace(
                        "<DATEI{$index}",
                        "<DATEI={$this->pad24($id, $index)}.{$ext}",
                        $link,
                    );
                }
            }

            $args = [
                'id' => $id,
                'link' => $link,
            ];
            $this->addSql("UPDATE termine SET link = :link WHERE id = :id", $args);
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
