<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251125174358 extends AbstractMigration {
    public function getDescription(): string {
        return 'Strava debugging, anniversary runs';
    }

    public function up(Schema $schema): void {
        $this->addSql('CREATE TABLE anniversary_runs (on_off INT DEFAULT 1 NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, last_modified_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, id BIGINT AUTO_INCREMENT NOT NULL, run_at DATETIME NOT NULL, distance_meters INT NOT NULL, elevation_meters INT NOT NULL, source VARCHAR(255) DEFAULT NULL, info LONGTEXT DEFAULT NULL, owner_user_id INT DEFAULT NULL, owner_role_id INT DEFAULT NULL, created_by_user_id INT DEFAULT NULL, last_modified_by_user_id INT DEFAULT NULL, user_id INT DEFAULT NULL, INDEX IDX_9B3F75262B18554A (owner_user_id), INDEX IDX_9B3F75265A75A473 (owner_role_id), INDEX IDX_9B3F75267D182D95 (created_by_user_id), INDEX IDX_9B3F75261A04EF5A (last_modified_by_user_id), INDEX IDX_9B3F7526A76ED395 (user_id), INDEX run_at_index (run_at), INDEX source_index (source), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE anniversary_runs ADD CONSTRAINT FK_9B3F75262B18554A FOREIGN KEY (owner_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE anniversary_runs ADD CONSTRAINT FK_9B3F75265A75A473 FOREIGN KEY (owner_role_id) REFERENCES roles (id)');
        $this->addSql('ALTER TABLE anniversary_runs ADD CONSTRAINT FK_9B3F75267D182D95 FOREIGN KEY (created_by_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE anniversary_runs ADD CONSTRAINT FK_9B3F75261A04EF5A FOREIGN KEY (last_modified_by_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE anniversary_runs ADD CONSTRAINT FK_9B3F7526A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE strava_links ADD created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, ADD linked_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void {
        $this->addSql('ALTER TABLE anniversary_runs DROP FOREIGN KEY FK_9B3F75262B18554A');
        $this->addSql('ALTER TABLE anniversary_runs DROP FOREIGN KEY FK_9B3F75265A75A473');
        $this->addSql('ALTER TABLE anniversary_runs DROP FOREIGN KEY FK_9B3F75267D182D95');
        $this->addSql('ALTER TABLE anniversary_runs DROP FOREIGN KEY FK_9B3F75261A04EF5A');
        $this->addSql('ALTER TABLE anniversary_runs DROP FOREIGN KEY FK_9B3F7526A76ED395');
        $this->addSql('DROP TABLE anniversary_runs');
        $this->addSql('ALTER TABLE strava_links DROP created_at, DROP linked_at');
    }
}
