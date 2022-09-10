<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220910163629 extends AbstractMigration {
    public function getDescription(): string {
        return 'Rename zugriff to permissions, add role permissions';
    }

    public function up(Schema $schema): void {
        $this->addSql('ALTER TABLE roles ADD permissions LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE users CHANGE zugriff permissions LONGTEXT NOT NULL');
    }

    public function down(Schema $schema): void {
        $this->addSql('ALTER TABLE roles DROP permissions');
        $this->addSql('ALTER TABLE users CHANGE permissions zugriff LONGTEXT NOT NULL');
    }
}
