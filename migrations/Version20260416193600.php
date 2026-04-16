<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260416193600 extends AbstractMigration {
    public function getDescription(): string {
        return 'Migrate termine to latlng (clean-up), random stuff';
    }

    public function up(Schema $schema): void {
        $this->addSql('ALTER TABLE termin_notification_templates ADD recipient_termin_owner_role TINYINT DEFAULT 0 NOT NULL, ADD recipient_termin_organizer TINYINT DEFAULT 0 NOT NULL, CHANGE recipient_termin_owners recipient_termin_owner_user TINYINT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE termin_notifications ADD recipient_termin_owner_role TINYINT DEFAULT 0 NOT NULL, ADD recipient_termin_organizer TINYINT DEFAULT 0 NOT NULL, CHANGE recipient_termin_owners recipient_termin_owner_user TINYINT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE termine DROP xkoord, DROP ykoord, DROP go2ol, ADD documentation LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void {
        $this->addSql('ALTER TABLE termine DROP documentation, ADD xkoord INT DEFAULT NULL, ADD ykoord INT DEFAULT NULL, ADD go2ol LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE termin_notifications ADD recipient_termin_owners TINYINT DEFAULT 0 NOT NULL, DROP recipient_termin_owner_user, DROP recipient_termin_owner_role, DROP recipient_termin_organizer');
        $this->addSql('ALTER TABLE termin_notification_templates ADD recipient_termin_owners TINYINT DEFAULT 0 NOT NULL, DROP recipient_termin_owner_user, DROP recipient_termin_owner_role, DROP recipient_termin_organizer');
    }
}
