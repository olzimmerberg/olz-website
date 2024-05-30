<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240530231008 extends AbstractMigration {
    public function getDescription(): string {
        return 'typing changes';
    }

    public function up(Schema $schema): void {
        $this->addSql('ALTER TABLE notification_subscriptions CHANGE user_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE strava_links CHANGE user_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE telegram_links CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
    }

    public function down(Schema $schema): void {
        $this->addSql('ALTER TABLE notification_subscriptions CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE strava_links CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE telegram_links CHANGE created_at created_at DATETIME NOT NULL');
    }
}
