<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250428113621 extends AbstractMigration {
    public function getDescription(): string {
        return 'Drop fileId of downloads';
    }

    public function up(Schema $schema): void {
        $this->addSql('ALTER TABLE downloads DROP file_id');
    }

    public function down(Schema $schema): void {
        $this->addSql('ALTER TABLE downloads ADD file_id LONGTEXT DEFAULT NULL');
    }
}
