<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241111170011 extends AbstractMigration {
    public function getDescription(): string {
        return 'Store template with termin';
    }

    public function up(Schema $schema): void {
        $this->addSql('ALTER TABLE termine ADD from_template_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE termine ADD CONSTRAINT FK_168C0A8F9B953EDD FOREIGN KEY (from_template_id) REFERENCES termin_templates (id)');
        $this->addSql('CREATE INDEX IDX_168C0A8F9B953EDD ON termine (from_template_id)');
    }

    public function down(Schema $schema): void {
        $this->addSql('ALTER TABLE termine DROP FOREIGN KEY FK_168C0A8F9B953EDD');
        $this->addSql('DROP INDEX IDX_168C0A8F9B953EDD ON termine');
        $this->addSql('ALTER TABLE termine DROP from_template_id');
    }
}
