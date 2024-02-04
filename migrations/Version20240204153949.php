<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240204153949 extends AbstractMigration {
    public function getDescription(): string {
        return 'Add role title (for SEO)';
    }

    public function up(Schema $schema): void {
        $this->addSql('ALTER TABLE roles ADD title LONGTEXT DEFAULT NULL COMMENT \'page title for SEO\'');
    }

    public function down(Schema $schema): void {
        $this->addSql('ALTER TABLE roles DROP title');
    }
}
