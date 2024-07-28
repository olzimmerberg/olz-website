<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240728114645 extends AbstractMigration {
    public function getDescription(): string {
        return 'Remove deprecated termin type storages';
    }

    public function up(Schema $schema): void {
        $this->addSql('ALTER TABLE termin_templates DROP types');
        $this->addSql('ALTER TABLE termine DROP typ');
    }

    public function down(Schema $schema): void {
        $this->addSql('ALTER TABLE termine ADD typ VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE termin_templates ADD types VARCHAR(255) DEFAULT NULL');
    }
}
