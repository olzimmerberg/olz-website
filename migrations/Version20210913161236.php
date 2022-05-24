<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210913161236 extends AbstractMigration {
    public function getDescription(): string {
        return 'Add AccessToken';
    }

    public function up(Schema $schema): void {
        $this->addSql('CREATE TABLE access_tokens (id BIGINT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, purpose VARCHAR(255) NOT NULL, token VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, expires_at DATETIME DEFAULT NULL, INDEX token_index (token), INDEX user_id_index (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE access_tokens ADD CONSTRAINT FK_58D184BCA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
    }

    public function down(Schema $schema): void {
        $this->addSql('DROP TABLE access_tokens');
    }
}
