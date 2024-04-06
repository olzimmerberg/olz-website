<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240406120652 extends AbstractMigration {
    public function getDescription(): string {
        return 'Make user an OlzEntity';
    }

    public function up(Schema $schema): void {
        $this->addSql('ALTER TABLE users ADD owner_user_id INT DEFAULT NULL, ADD owner_role_id INT DEFAULT NULL, ADD created_by_user_id INT DEFAULT NULL, ADD last_modified_by_user_id INT DEFAULT NULL, ADD on_off INT DEFAULT 1 NOT NULL');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E92B18554A FOREIGN KEY (owner_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E95A75A473 FOREIGN KEY (owner_role_id) REFERENCES roles (id)');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E97D182D95 FOREIGN KEY (created_by_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E91A04EF5A FOREIGN KEY (last_modified_by_user_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_1483A5E92B18554A ON users (owner_user_id)');
        $this->addSql('CREATE INDEX IDX_1483A5E95A75A473 ON users (owner_role_id)');
        $this->addSql('CREATE INDEX IDX_1483A5E97D182D95 ON users (created_by_user_id)');
        $this->addSql('CREATE INDEX IDX_1483A5E91A04EF5A ON users (last_modified_by_user_id)');
    }

    public function down(Schema $schema): void {
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E92B18554A');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E95A75A473');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E97D182D95');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E91A04EF5A');
        $this->addSql('DROP INDEX IDX_1483A5E92B18554A ON users');
        $this->addSql('DROP INDEX IDX_1483A5E95A75A473 ON users');
        $this->addSql('DROP INDEX IDX_1483A5E97D182D95 ON users');
        $this->addSql('DROP INDEX IDX_1483A5E91A04EF5A ON users');
        $this->addSql('ALTER TABLE users DROP owner_user_id, DROP owner_role_id, DROP created_by_user_id, DROP last_modified_by_user_id, DROP on_off');
    }
}
