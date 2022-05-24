<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210317183728 extends AbstractMigration {
    public function getDescription(): string {
        return 'Add role description and page';
    }

    public function up(Schema $schema): void {
        $this->addSql('ALTER TABLE roles ADD description LONGTEXT NOT NULL, ADD page LONGTEXT NOT NULL');
    }

    public function down(Schema $schema): void {
        $this->addSql('ALTER TABLE roles DROP description, DROP page');
    }
}
