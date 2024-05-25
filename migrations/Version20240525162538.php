<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240525162538 extends AbstractMigration {
    public function getDescription(): string {
        return 'Remove link field from Termin, TerminTemplate';
    }

    public function up(Schema $schema): void {
        $this->addSql('ALTER TABLE termin_templates DROP link');
        $this->addSql('ALTER TABLE termine DROP link');
    }

    public function down(Schema $schema): void {
        $this->addSql('ALTER TABLE termine ADD link LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE termin_templates ADD link LONGTEXT DEFAULT NULL');
    }
}
