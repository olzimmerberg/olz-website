<?php

declare(strict_types=1);

namespace OLZ\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210129192635 extends AbstractMigration {
    public function getDescription(): string {
        return 'Notification subscriptions and throttling';
    }

    public function up(Schema $schema): void {
        $this->addSql('CREATE TABLE notification_subscriptions (id BIGINT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, delivery_type VARCHAR(255) NOT NULL, notification_type VARCHAR(255) NOT NULL, notification_type_args LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, INDEX user_id_index (user_id), INDEX notification_type_index (notification_type), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE throttlings (id BIGINT AUTO_INCREMENT NOT NULL, event_name VARCHAR(255) NOT NULL, last_occurrence DATETIME DEFAULT NULL, UNIQUE INDEX event_name_index (event_name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE notification_subscriptions ADD CONSTRAINT FK_52C540C8A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
    }

    public function down(Schema $schema): void {
        $this->addSql('DROP TABLE notification_subscriptions');
        $this->addSql('DROP TABLE throttlings');
    }
}
