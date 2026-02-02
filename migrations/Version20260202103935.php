<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260202103935 extends AbstractMigration {
    public function getDescription(): string {
        return 'Termin-Organisator';
    }

    public function up(Schema $schema): void {
        $this->addSql('ALTER TABLE termin_templates ADD organizer_user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE termin_templates ADD CONSTRAINT FK_A2ECDD29EE5F645C FOREIGN KEY (organizer_user_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_A2ECDD29EE5F645C ON termin_templates (organizer_user_id)');
        $this->addSql('ALTER TABLE termine ADD organizer_user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE termine ADD CONSTRAINT FK_168C0A8FEE5F645C FOREIGN KEY (organizer_user_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_168C0A8FEE5F645C ON termine (organizer_user_id)');
    }

    public function down(Schema $schema): void {
        $this->addSql('ALTER TABLE termine DROP FOREIGN KEY FK_168C0A8FEE5F645C');
        $this->addSql('DROP INDEX IDX_168C0A8FEE5F645C ON termine');
        $this->addSql('ALTER TABLE termine DROP organizer_user_id');
        $this->addSql('ALTER TABLE termin_templates DROP FOREIGN KEY FK_A2ECDD29EE5F645C');
        $this->addSql('DROP INDEX IDX_A2ECDD29EE5F645C ON termin_templates');
        $this->addSql('ALTER TABLE termin_templates DROP organizer_user_id');
    }
}
