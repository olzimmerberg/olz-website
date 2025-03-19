<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250317225100 extends AbstractMigration {
    public function getDescription(): string {
        return 'Remove page, title from roles';
    }

    public function up(Schema $schema): void {
        $this->addSql("UPDATE roles SET description=CONCAT('# ', IFNULL(title, name), '\n\n', description)");
        $this->addSql('ALTER TABLE roles DROP page, DROP title');
    }

    public function down(Schema $schema): void {
        $this->addSql('ALTER TABLE roles ADD page LONGTEXT NOT NULL, ADD title LONGTEXT DEFAULT NULL COMMENT \'page title for SEO\'');
    }
}
