<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240207225304 extends AbstractMigration {
    public function getDescription(): string {
        return 'Remove GoogleLink / FacebookLink';
    }

    public function up(Schema $schema): void {
        $this->addSql('ALTER TABLE facebook_links DROP FOREIGN KEY FK_3444E616A76ED395');
        $this->addSql('ALTER TABLE google_links DROP FOREIGN KEY FK_486FA817A76ED395');
        $this->addSql('DROP TABLE facebook_links');
        $this->addSql('DROP TABLE google_links');
    }

    public function down(Schema $schema): void {
        $this->addSql('CREATE TABLE facebook_links (id BIGINT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, access_token LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, expires_at DATETIME NOT NULL, refresh_token LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, facebook_user LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX user_id_index (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE google_links (id BIGINT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, access_token LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, expires_at DATETIME NOT NULL, refresh_token LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, google_user LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX user_id_index (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE facebook_links ADD CONSTRAINT FK_3444E616A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE google_links ADD CONSTRAINT FK_486FA817A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
    }
}
