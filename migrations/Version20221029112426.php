<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221029112426 extends AbstractMigration {
    public function getDescription(): string {
        return 'Add termine deadline';
    }

    public function up(Schema $schema): void {
        $this->addSql('ALTER TABLE termine ADD deadline DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void {
        $this->addSql('ALTER TABLE termine DROP deadline');
    }
}
