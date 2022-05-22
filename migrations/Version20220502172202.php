<?php

declare(strict_types=1);

namespace OLZ\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220502172202 extends AbstractMigration {
    public function getDescription(): string {
        return 'Mitgliederverwaltung';
    }

    public function up(Schema $schema): void {
        $this->addSql('ALTER TABLE users ADD parent_user INT DEFAULT NULL, ADD member_type VARCHAR(3) DEFAULT NULL COMMENT \'Aktiv, Ehrenmitglied, Verein, Sponsor\', ADD member_last_paid DATE DEFAULT NULL, ADD wants_postal_mail TINYINT(1) DEFAULT 0 NOT NULL, ADD postal_title LONGTEXT DEFAULT NULL COMMENT \'if not {m: Herr, f: Frau, o: }\', ADD postal_name LONGTEXT DEFAULT NULL COMMENT \'if not \'\'First Last\'\'\', ADD joined_on DATE DEFAULT NULL, ADD joined_reason LONGTEXT DEFAULT NULL, ADD left_on DATE DEFAULT NULL, ADD left_reason LONGTEXT DEFAULT NULL, ADD solv_number LONGTEXT DEFAULT NULL, ADD si_card_number LONGTEXT DEFAULT NULL, ADD notes LONGTEXT NOT NULL, CHANGE password password LONGTEXT DEFAULT NULL, CHANGE email email LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void {
        $this->addSql('ALTER TABLE users DROP parent_user, DROP member_type, DROP member_last_paid, DROP wants_postal_mail, DROP postal_title, DROP postal_name, DROP joined_on, DROP joined_reason, DROP left_on, DROP left_reason, DROP solv_number, DROP si_card_number, DROP notes, CHANGE password password LONGTEXT NOT NULL, CHANGE email email LONGTEXT NOT NULL');
    }
}
