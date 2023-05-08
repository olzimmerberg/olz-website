<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230508102943 extends AbstractMigration {
    public function getDescription(): string {
        return 'Update Termine table remove old created/modified, add participants / volunteers info';
    }

    public function up(Schema $schema): void {
        $this->addSql('UPDATE termine SET last_modified_at = modified, created_at = created');
        $this->addSql('ALTER TABLE termine ADD participants_registration_id INT DEFAULT NULL, ADD volunteers_registration_id INT DEFAULT NULL, ADD num_participants INT DEFAULT NULL, ADD min_participants INT DEFAULT NULL, ADD max_participants INT DEFAULT NULL, ADD num_volunteers INT DEFAULT NULL, ADD min_volunteers INT DEFAULT NULL, ADD max_volunteers INT DEFAULT NULL, DROP teilnehmer, DROP modified, DROP created');
        $this->addSql('ALTER TABLE termine ADD CONSTRAINT FK_168C0A8F80299162 FOREIGN KEY (participants_registration_id) REFERENCES anmelden_registrations (id)');
        $this->addSql('ALTER TABLE termine ADD CONSTRAINT FK_168C0A8F6D54E666 FOREIGN KEY (volunteers_registration_id) REFERENCES anmelden_registrations (id)');
        $this->addSql('CREATE INDEX IDX_168C0A8F80299162 ON termine (participants_registration_id)');
        $this->addSql('CREATE INDEX IDX_168C0A8F6D54E666 ON termine (volunteers_registration_id)');
    }

    public function down(Schema $schema): void {
        $this->addSql('ALTER TABLE termine DROP FOREIGN KEY FK_168C0A8F80299162');
        $this->addSql('ALTER TABLE termine DROP FOREIGN KEY FK_168C0A8F6D54E666');
        $this->addSql('DROP INDEX IDX_168C0A8F80299162 ON termine');
        $this->addSql('DROP INDEX IDX_168C0A8F6D54E666 ON termine');
        $this->addSql('ALTER TABLE termine ADD teilnehmer INT DEFAULT 0 NOT NULL, ADD modified DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, ADD created DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, DROP participants_registration_id, DROP volunteers_registration_id, DROP num_participants, DROP min_participants, DROP max_participants, DROP num_volunteers, DROP min_volunteers, DROP max_volunteers');
    }
}
