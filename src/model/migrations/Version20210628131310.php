<?php

declare(strict_types=1);

namespace OLZ\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210628131310 extends AbstractMigration {
    public function getDescription(): string {
        return 'Add new fields for news';
    }

    public function up(Schema $schema): void {
        $this->addSql('ALTER TABLE aktuell ADD author_user_id INT DEFAULT NULL, ADD author_role_id INT DEFAULT NULL, ADD owner_user_id INT DEFAULT NULL, ADD owner_role_id INT DEFAULT NULL, ADD created_by_user_id INT DEFAULT NULL, ADD last_modified_by_user_id INT DEFAULT NULL, ADD tags LONGTEXT DEFAULT \'\' NOT NULL, ADD created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, ADD last_modified_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE on_off on_off INT DEFAULT 1 NOT NULL');
        $this->addSql('ALTER TABLE aktuell ADD CONSTRAINT FK_417D7104E2544CD6 FOREIGN KEY (author_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE aktuell ADD CONSTRAINT FK_417D71049339BDEF FOREIGN KEY (author_role_id) REFERENCES roles (id)');
        $this->addSql('ALTER TABLE aktuell ADD CONSTRAINT FK_417D71042B18554A FOREIGN KEY (owner_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE aktuell ADD CONSTRAINT FK_417D71045A75A473 FOREIGN KEY (owner_role_id) REFERENCES roles (id)');
        $this->addSql('ALTER TABLE aktuell ADD CONSTRAINT FK_417D71047D182D95 FOREIGN KEY (created_by_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE aktuell ADD CONSTRAINT FK_417D71041A04EF5A FOREIGN KEY (last_modified_by_user_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_417D7104E2544CD6 ON aktuell (author_user_id)');
        $this->addSql('CREATE INDEX IDX_417D71049339BDEF ON aktuell (author_role_id)');
        $this->addSql('CREATE INDEX IDX_417D71042B18554A ON aktuell (owner_user_id)');
        $this->addSql('CREATE INDEX IDX_417D71045A75A473 ON aktuell (owner_role_id)');
        $this->addSql('CREATE INDEX IDX_417D71047D182D95 ON aktuell (created_by_user_id)');
        $this->addSql('CREATE INDEX IDX_417D71041A04EF5A ON aktuell (last_modified_by_user_id)');
    }

    public function down(Schema $schema): void {
        $this->addSql('ALTER TABLE aktuell DROP FOREIGN KEY FK_417D7104E2544CD6');
        $this->addSql('ALTER TABLE aktuell DROP FOREIGN KEY FK_417D71049339BDEF');
        $this->addSql('ALTER TABLE aktuell DROP FOREIGN KEY FK_417D71042B18554A');
        $this->addSql('ALTER TABLE aktuell DROP FOREIGN KEY FK_417D71045A75A473');
        $this->addSql('ALTER TABLE aktuell DROP FOREIGN KEY FK_417D71047D182D95');
        $this->addSql('ALTER TABLE aktuell DROP FOREIGN KEY FK_417D71041A04EF5A');
        $this->addSql('DROP INDEX IDX_417D7104E2544CD6 ON aktuell');
        $this->addSql('DROP INDEX IDX_417D71049339BDEF ON aktuell');
        $this->addSql('DROP INDEX IDX_417D71042B18554A ON aktuell');
        $this->addSql('DROP INDEX IDX_417D71045A75A473 ON aktuell');
        $this->addSql('DROP INDEX IDX_417D71047D182D95 ON aktuell');
        $this->addSql('DROP INDEX IDX_417D71041A04EF5A ON aktuell');
        $this->addSql('ALTER TABLE aktuell DROP author_user_id, DROP author_role_id, DROP owner_user_id, DROP owner_role_id, DROP created_by_user_id, DROP last_modified_by_user_id, DROP tags, DROP created_at, DROP last_modified_at, CHANGE on_off on_off INT DEFAULT 0 NOT NULL');
    }
}
