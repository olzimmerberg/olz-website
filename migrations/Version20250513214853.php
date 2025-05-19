<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250513214853 extends AbstractMigration {
    public function getDescription(): string {
        return 'Add members table';
    }

    public function up(Schema $schema): void {
        $this->addSql(<<<'SQL'
                CREATE TABLE members (on_off INT DEFAULT 1 NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, last_modified_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, id INT AUTO_INCREMENT NOT NULL, ident VARCHAR(255) NOT NULL, data LONGTEXT NOT NULL, updates LONGTEXT DEFAULT NULL, owner_user_id INT DEFAULT NULL, owner_role_id INT DEFAULT NULL, created_by_user_id INT DEFAULT NULL, last_modified_by_user_id INT DEFAULT NULL, user_id INT DEFAULT NULL, INDEX IDX_45A0D2FF2B18554A (owner_user_id), INDEX IDX_45A0D2FF5A75A473 (owner_role_id), INDEX IDX_45A0D2FF7D182D95 (created_by_user_id), INDEX IDX_45A0D2FF1A04EF5A (last_modified_by_user_id), INDEX ident_index (ident), INDEX user_id_index (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE members ADD CONSTRAINT FK_45A0D2FF2B18554A FOREIGN KEY (owner_user_id) REFERENCES users (id)
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE members ADD CONSTRAINT FK_45A0D2FF5A75A473 FOREIGN KEY (owner_role_id) REFERENCES roles (id)
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE members ADD CONSTRAINT FK_45A0D2FF7D182D95 FOREIGN KEY (created_by_user_id) REFERENCES users (id)
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE members ADD CONSTRAINT FK_45A0D2FF1A04EF5A FOREIGN KEY (last_modified_by_user_id) REFERENCES users (id)
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE members ADD CONSTRAINT FK_45A0D2FFA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)
            SQL);
    }

    public function down(Schema $schema): void {
        $this->addSql(<<<'SQL'
                ALTER TABLE members DROP FOREIGN KEY FK_45A0D2FF2B18554A
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE members DROP FOREIGN KEY FK_45A0D2FF5A75A473
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE members DROP FOREIGN KEY FK_45A0D2FF7D182D95
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE members DROP FOREIGN KEY FK_45A0D2FF1A04EF5A
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE members DROP FOREIGN KEY FK_45A0D2FFA76ED395
            SQL);
        $this->addSql(<<<'SQL'
                DROP TABLE members
            SQL);
    }
}
