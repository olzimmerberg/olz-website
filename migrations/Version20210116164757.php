<?php

declare(strict_types=1);

namespace OLZ\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210116164757 extends AbstractMigration {
    public function getDescription(): string {
        return 'Add telegram links';
    }

    public function up(Schema $schema): void {
        $this->addSql('CREATE TABLE telegram_links (id BIGINT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, pin VARCHAR(255) DEFAULT NULL, pin_expires_at DATETIME DEFAULT NULL, telegram_chat_id VARCHAR(255) DEFAULT NULL, telegram_user_id VARCHAR(255) DEFAULT NULL, telegram_chat_state LONGTEXT NOT NULL, created_at DATETIME NOT NULL, linked_at DATETIME DEFAULT NULL, INDEX pin_index (pin), INDEX user_id_index (user_id), INDEX telegram_user_id_index (telegram_user_id), INDEX telegram_chat_id_index (telegram_chat_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE telegram_links ADD CONSTRAINT FK_CC49A25AA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE users CHANGE username username VARCHAR(255) NOT NULL, CHANGE old_username old_username VARCHAR(255) DEFAULT NULL');
        $this->addSql('CREATE INDEX username_index ON users (username)');
    }

    public function down(Schema $schema): void {
        $this->addSql('DROP TABLE telegram_links');
        $this->addSql('DROP INDEX username_index ON users');
        $this->addSql('ALTER TABLE users CHANGE username username LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE old_username old_username LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
