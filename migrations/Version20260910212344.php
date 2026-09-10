<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260910212344 extends AbstractMigration {
    public function getDescription(): string {
        return 'Termin-Notifications update';
    }

    public function up(Schema $schema): void {
        $this->addSql('ALTER TABLE termin_notification_templates CHANGE fires_earlier_seconds fires_earlier_seconds INT NOT NULL');
        $this->addSql('DROP INDEX fires_at_index ON termin_notifications');
        $this->addSql('ALTER TABLE termin_notifications ADD fires_earlier_seconds INT NOT NULL, DROP fires_at');
        $this->addSql('CREATE INDEX fires_earlier_seconds_index ON termin_notifications (fires_earlier_seconds)');
    }

    public function down(Schema $schema): void {
        $this->addSql('ALTER TABLE termin_notification_templates CHANGE fires_earlier_seconds fires_earlier_seconds INT DEFAULT NULL');
        $this->addSql('DROP INDEX fires_earlier_seconds_index ON termin_notifications');
        $this->addSql('ALTER TABLE termin_notifications ADD fires_at DATETIME NOT NULL, DROP fires_earlier_seconds');
        $this->addSql('CREATE INDEX fires_at_index ON termin_notifications (fires_at)');
    }
}
