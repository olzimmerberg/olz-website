<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241117162027 extends AbstractMigration {
    public function getDescription(): string {
        return 'Add option early advertise';
    }

    public function up(Schema $schema): void {
        $this->addSql('ALTER TABLE termin_templates ADD should_promote TINYINT(1) DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE termine ADD should_promote TINYINT(1) DEFAULT 0 NOT NULL');
    }

    public function down(Schema $schema): void {
        $this->addSql('ALTER TABLE termine DROP should_promote');
        $this->addSql('ALTER TABLE termin_templates DROP should_promote');
    }
}
