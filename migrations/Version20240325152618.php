<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240325152618 extends AbstractMigration {
    public function getDescription(): string {
        return 'Migrate olz_text to snippets';
    }

    public function up(Schema $schema): void {
        $this->addSql('CREATE TABLE snippets (id INT NOT NULL, owner_user_id INT DEFAULT NULL, owner_role_id INT DEFAULT NULL, created_by_user_id INT DEFAULT NULL, last_modified_by_user_id INT DEFAULT NULL, on_off INT DEFAULT 1 NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, last_modified_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, text LONGTEXT DEFAULT NULL, INDEX IDX_ED21F5DC2B18554A (owner_user_id), INDEX IDX_ED21F5DC5A75A473 (owner_role_id), INDEX IDX_ED21F5DC7D182D95 (created_by_user_id), INDEX IDX_ED21F5DC1A04EF5A (last_modified_by_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE snippets ADD CONSTRAINT FK_ED21F5DC2B18554A FOREIGN KEY (owner_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE snippets ADD CONSTRAINT FK_ED21F5DC5A75A473 FOREIGN KEY (owner_role_id) REFERENCES roles (id)');
        $this->addSql('ALTER TABLE snippets ADD CONSTRAINT FK_ED21F5DC7D182D95 FOREIGN KEY (created_by_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE snippets ADD CONSTRAINT FK_ED21F5DC1A04EF5A FOREIGN KEY (last_modified_by_user_id) REFERENCES users (id)');
        $this->addSql('INSERT INTO snippets (id, text, on_off, owner_user_id, owner_role_id, created_by_user_id, last_modified_by_user_id, created_at, last_modified_at) SELECT id, text, on_off, owner_user_id, owner_role_id, created_by_user_id, last_modified_by_user_id, created_at, last_modified_at FROM olz_text');
        $this->addSql('ALTER TABLE olz_text DROP FOREIGN KEY FK_2ACA45895A75A473');
        $this->addSql('ALTER TABLE olz_text DROP FOREIGN KEY FK_2ACA45891A04EF5A');
        $this->addSql('ALTER TABLE olz_text DROP FOREIGN KEY FK_2ACA45897D182D95');
        $this->addSql('ALTER TABLE olz_text DROP FOREIGN KEY FK_2ACA45892B18554A');
        $this->addSql('DROP TABLE olz_text');
    }

    public function down(Schema $schema): void {
        $this->addSql('CREATE TABLE olz_text (id INT NOT NULL, owner_user_id INT DEFAULT NULL, owner_role_id INT DEFAULT NULL, created_by_user_id INT DEFAULT NULL, last_modified_by_user_id INT DEFAULT NULL, text LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, on_off INT DEFAULT 1 NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, last_modified_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, INDEX IDX_2ACA45891A04EF5A (last_modified_by_user_id), INDEX IDX_2ACA45892B18554A (owner_user_id), INDEX IDX_2ACA45895A75A473 (owner_role_id), INDEX IDX_2ACA45897D182D95 (created_by_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE olz_text ADD CONSTRAINT FK_2ACA45895A75A473 FOREIGN KEY (owner_role_id) REFERENCES roles (id)');
        $this->addSql('ALTER TABLE olz_text ADD CONSTRAINT FK_2ACA45891A04EF5A FOREIGN KEY (last_modified_by_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE olz_text ADD CONSTRAINT FK_2ACA45897D182D95 FOREIGN KEY (created_by_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE olz_text ADD CONSTRAINT FK_2ACA45892B18554A FOREIGN KEY (owner_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE snippets DROP FOREIGN KEY FK_ED21F5DC2B18554A');
        $this->addSql('ALTER TABLE snippets DROP FOREIGN KEY FK_ED21F5DC5A75A473');
        $this->addSql('ALTER TABLE snippets DROP FOREIGN KEY FK_ED21F5DC7D182D95');
        $this->addSql('ALTER TABLE snippets DROP FOREIGN KEY FK_ED21F5DC1A04EF5A');
        $this->addSql('DROP TABLE snippets');
    }
}
