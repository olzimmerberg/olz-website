<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220820142330 extends AbstractMigration {
    public function getDescription(): string {
        return 'Doctrine Version Bump';
    }

    public function up(Schema $schema): void {
        $this->addSql('ALTER TABLE aktuell CHANGE tags tags LONGTEXT DEFAULT \'\' NOT NULL');
        $this->addSql('ALTER TABLE users CHANGE notes notes LONGTEXT DEFAULT \'\' NOT NULL');
    }

    public function down(Schema $schema): void {
        $this->addSql('ALTER TABLE aktuell CHANGE tags tags LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE users CHANGE notes notes LONGTEXT NOT NULL');
    }
}
