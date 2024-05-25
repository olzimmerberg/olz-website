<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240524112831 extends AbstractMigration {
    public function getDescription(): string {
        return 'Migrate file tags to markdown';
    }

    public function up(Schema $schema): void {
        // News
        $rows = $this->connection->fetchAllAssociative('SELECT * FROM news');
        foreach ($rows as $row) {
            $args = [
                'id' => $row['id'],
                'teaser' => $this->html2Md($this->migrate($row['teaser'], json_decode($row['image_ids'], true))),
                'content' => $this->html2Md($this->migrate($row['content'], json_decode($row['image_ids'], true))),
            ];
            $this->addSql("UPDATE news SET teaser = :teaser, content = :content WHERE id = :id", $args);
        }
        // Termine
        $rows = $this->connection->fetchAllAssociative('SELECT * FROM termine');
        foreach ($rows as $row) {
            $args = [
                'id' => $row['id'],
                'text' => $row['text']."\n\n".$this->html2Md($this->migrate($row['link'], null)),
                'link' => '',
            ];
            $this->addSql("UPDATE termine SET text = :text, link = :link WHERE id = :id", $args);
        }
        // Termin templates
        $rows = $this->connection->fetchAllAssociative('SELECT * FROM termin_templates');
        foreach ($rows as $row) {
            $args = [
                'id' => $row['id'],
                'text' => $row['text']."\n\n".$this->html2Md($this->migrate($row['link'], null)),
                'link' => '',
            ];
            $this->addSql("UPDATE termin_templates SET text = :text, link = :link WHERE id = :id", $args);
        }
        // Termin locations
        $rows = $this->connection->fetchAllAssociative('SELECT * FROM termin_locations');
        foreach ($rows as $row) {
            $args = [
                'id' => $row['id'],
                'details' => $this->migrate($row['details'], null),
            ];
            $this->addSql("UPDATE termin_locations SET details = :details WHERE id = :id", $args);
        }
    }

    public function down(Schema $schema): void {
    }

    protected function migrate(?string $input, ?array $image_ids): ?string {
        if (!$input) {
            return $input;
        }
        $input = $this->migrateFileTags($input);
        return $this->migrateImageTags($input, $image_ids);
    }

    protected $file_tag_pattern =
        "/<datei\\=([0-9A-Za-z_\\-]{24}\\.\\S{1,10})(\\s+text=(\"|\\')([^\"\\']+)(\"|\\'))?([^>]*)>/i";

    protected function migrateFileTags(?string $input): ?string {
        if (!$input) {
            return $input;
        }
        return preg_replace(
            $this->file_tag_pattern,
            "[$4](./$1)",
            $input
        );
    }

    protected function removeFileTags(?string $input): ?string {
        if (!$input) {
            return $input;
        }
        return preg_replace(
            $this->file_tag_pattern,
            "",
            $input
        );
    }

    protected function migrateImageTags(?string $input, ?array $image_ids): ?string {
        if (!$input || !$image_ids) {
            return $input;
        }
        for ($i = 0; $i < count($image_ids); $i++) {
            $number = $i + 1; // human readable index
            $image_tag_pattern = "/<bild{$number}(\\s+size=([0-9]+))?([^>]*)>/i";
            $image_id = $image_ids[$i];
            $input = preg_replace(
                $image_tag_pattern,
                "![](./{$image_id})",
                $input
            );
        }
        return $input;
    }

    protected function html2Md(?string $input): ?string {
        if (!$input) {
            return $input;
        }
        return preg_replace(
            "/<a[^>]*\\s+href=(\\\"|\"|\"\"|\\'|'|)([^\"\\']+)(\\\"|\"|\"\"|\\'|'|)[^>]*>(?:<(?:div|p|span)[^>]*>)?([^<]+)(?:<\\/(?:div|p|span)[^>]*>)?<\\/a>/i",
            "[$4]($2)",
            $input
        );
    }
}
