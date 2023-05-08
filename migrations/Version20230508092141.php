<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230508092141 extends AbstractMigration {
    public function getDescription(): string {
        return 'Make Termin a OlzEntity';
    }

    public function up(Schema $schema): void {
        $this->addSql('ALTER TABLE termine ADD owner_user_id INT DEFAULT NULL, ADD owner_role_id INT DEFAULT NULL, ADD created_by_user_id INT DEFAULT NULL, ADD last_modified_by_user_id INT DEFAULT NULL, ADD created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, ADD last_modified_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE on_off on_off INT DEFAULT 1 NOT NULL');
        $this->addSql('ALTER TABLE termine ADD CONSTRAINT FK_168C0A8F2B18554A FOREIGN KEY (owner_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE termine ADD CONSTRAINT FK_168C0A8F5A75A473 FOREIGN KEY (owner_role_id) REFERENCES roles (id)');
        $this->addSql('ALTER TABLE termine ADD CONSTRAINT FK_168C0A8F7D182D95 FOREIGN KEY (created_by_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE termine ADD CONSTRAINT FK_168C0A8F1A04EF5A FOREIGN KEY (last_modified_by_user_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_168C0A8F2B18554A ON termine (owner_user_id)');
        $this->addSql('CREATE INDEX IDX_168C0A8F5A75A473 ON termine (owner_role_id)');
        $this->addSql('CREATE INDEX IDX_168C0A8F7D182D95 ON termine (created_by_user_id)');
        $this->addSql('CREATE INDEX IDX_168C0A8F1A04EF5A ON termine (last_modified_by_user_id)');
    }

    public function down(Schema $schema): void {
        $this->addSql('ALTER TABLE termine DROP FOREIGN KEY FK_168C0A8F2B18554A');
        $this->addSql('ALTER TABLE termine DROP FOREIGN KEY FK_168C0A8F5A75A473');
        $this->addSql('ALTER TABLE termine DROP FOREIGN KEY FK_168C0A8F7D182D95');
        $this->addSql('ALTER TABLE termine DROP FOREIGN KEY FK_168C0A8F1A04EF5A');
        $this->addSql('DROP INDEX IDX_168C0A8F2B18554A ON termine');
        $this->addSql('DROP INDEX IDX_168C0A8F5A75A473 ON termine');
        $this->addSql('DROP INDEX IDX_168C0A8F7D182D95 ON termine');
        $this->addSql('DROP INDEX IDX_168C0A8F1A04EF5A ON termine');
        $this->addSql('ALTER TABLE termine DROP owner_user_id, DROP owner_role_id, DROP created_by_user_id, DROP last_modified_by_user_id, DROP created_at, DROP last_modified_at, CHANGE on_off on_off INT DEFAULT 0 NOT NULL');
    }
}
