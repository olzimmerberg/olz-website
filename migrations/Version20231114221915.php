<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20231114221915 extends AbstractMigration {
    public function getDescription(): string {
        return 'Make Downloads & Links OlzEntity';
    }

    public function up(Schema $schema): void {
        $this->addSql('ALTER TABLE downloads ADD owner_user_id INT DEFAULT NULL, ADD owner_role_id INT DEFAULT NULL, ADD created_by_user_id INT DEFAULT NULL, ADD last_modified_by_user_id INT DEFAULT NULL, ADD created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, ADD last_modified_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, DROP datum, CHANGE on_off on_off INT DEFAULT 1 NOT NULL, CHANGE file1 file_id LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE downloads ADD CONSTRAINT FK_4B73A4B52B18554A FOREIGN KEY (owner_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE downloads ADD CONSTRAINT FK_4B73A4B55A75A473 FOREIGN KEY (owner_role_id) REFERENCES roles (id)');
        $this->addSql('ALTER TABLE downloads ADD CONSTRAINT FK_4B73A4B57D182D95 FOREIGN KEY (created_by_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE downloads ADD CONSTRAINT FK_4B73A4B51A04EF5A FOREIGN KEY (last_modified_by_user_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_4B73A4B52B18554A ON downloads (owner_user_id)');
        $this->addSql('CREATE INDEX IDX_4B73A4B55A75A473 ON downloads (owner_role_id)');
        $this->addSql('CREATE INDEX IDX_4B73A4B57D182D95 ON downloads (created_by_user_id)');
        $this->addSql('CREATE INDEX IDX_4B73A4B51A04EF5A ON downloads (last_modified_by_user_id)');
        $this->addSql('ALTER TABLE links ADD owner_user_id INT DEFAULT NULL, ADD owner_role_id INT DEFAULT NULL, ADD created_by_user_id INT DEFAULT NULL, ADD last_modified_by_user_id INT DEFAULT NULL, ADD created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, ADD last_modified_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, DROP datum, CHANGE on_off on_off INT DEFAULT 1 NOT NULL');
        $this->addSql('ALTER TABLE links ADD CONSTRAINT FK_D182A1182B18554A FOREIGN KEY (owner_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE links ADD CONSTRAINT FK_D182A1185A75A473 FOREIGN KEY (owner_role_id) REFERENCES roles (id)');
        $this->addSql('ALTER TABLE links ADD CONSTRAINT FK_D182A1187D182D95 FOREIGN KEY (created_by_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE links ADD CONSTRAINT FK_D182A1181A04EF5A FOREIGN KEY (last_modified_by_user_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_D182A1182B18554A ON links (owner_user_id)');
        $this->addSql('CREATE INDEX IDX_D182A1185A75A473 ON links (owner_role_id)');
        $this->addSql('CREATE INDEX IDX_D182A1187D182D95 ON links (created_by_user_id)');
        $this->addSql('CREATE INDEX IDX_D182A1181A04EF5A ON links (last_modified_by_user_id)');
    }

    public function down(Schema $schema): void {
        $this->addSql('ALTER TABLE downloads DROP FOREIGN KEY FK_4B73A4B52B18554A');
        $this->addSql('ALTER TABLE downloads DROP FOREIGN KEY FK_4B73A4B55A75A473');
        $this->addSql('ALTER TABLE downloads DROP FOREIGN KEY FK_4B73A4B57D182D95');
        $this->addSql('ALTER TABLE downloads DROP FOREIGN KEY FK_4B73A4B51A04EF5A');
        $this->addSql('DROP INDEX IDX_4B73A4B52B18554A ON downloads');
        $this->addSql('DROP INDEX IDX_4B73A4B55A75A473 ON downloads');
        $this->addSql('DROP INDEX IDX_4B73A4B57D182D95 ON downloads');
        $this->addSql('DROP INDEX IDX_4B73A4B51A04EF5A ON downloads');
        $this->addSql('ALTER TABLE downloads ADD datum DATE DEFAULT NULL, DROP owner_user_id, DROP owner_role_id, DROP created_by_user_id, DROP last_modified_by_user_id, DROP created_at, DROP last_modified_at, CHANGE on_off on_off INT DEFAULT NULL, CHANGE file_id file1 LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE links DROP FOREIGN KEY FK_D182A1182B18554A');
        $this->addSql('ALTER TABLE links DROP FOREIGN KEY FK_D182A1185A75A473');
        $this->addSql('ALTER TABLE links DROP FOREIGN KEY FK_D182A1187D182D95');
        $this->addSql('ALTER TABLE links DROP FOREIGN KEY FK_D182A1181A04EF5A');
        $this->addSql('DROP INDEX IDX_D182A1182B18554A ON links');
        $this->addSql('DROP INDEX IDX_D182A1185A75A473 ON links');
        $this->addSql('DROP INDEX IDX_D182A1187D182D95 ON links');
        $this->addSql('DROP INDEX IDX_D182A1181A04EF5A ON links');
        $this->addSql('ALTER TABLE links ADD datum DATE DEFAULT NULL, DROP owner_user_id, DROP owner_role_id, DROP created_by_user_id, DROP last_modified_by_user_id, DROP created_at, DROP last_modified_at, CHANGE on_off on_off INT DEFAULT NULL');
    }
}
