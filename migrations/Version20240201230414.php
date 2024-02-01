<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240201230414 extends AbstractMigration {
    public function getDescription(): string {
        return 'Vote on weekly picutres';
    }

    public function up(Schema $schema): void {
        $this->addSql('CREATE TABLE weekly_picture_votes (id INT AUTO_INCREMENT NOT NULL, created_by_user_id INT NOT NULL, weekly_picture_id INT NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, vote INT NOT NULL, INDEX IDX_F0FCCCE77D182D95 (created_by_user_id), INDEX IDX_F0FCCCE7B7AE7853 (weekly_picture_id), INDEX weekly_picture_created_at_index (weekly_picture_id, created_at), INDEX weekly_picture_created_by_index (weekly_picture_id, created_by_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE weekly_picture_votes ADD CONSTRAINT FK_F0FCCCE77D182D95 FOREIGN KEY (created_by_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE weekly_picture_votes ADD CONSTRAINT FK_F0FCCCE7B7AE7853 FOREIGN KEY (weekly_picture_id) REFERENCES weekly_picture (id)');
    }

    public function down(Schema $schema): void {
        $this->addSql('ALTER TABLE weekly_picture_votes DROP FOREIGN KEY FK_F0FCCCE77D182D95');
        $this->addSql('ALTER TABLE weekly_picture_votes DROP FOREIGN KEY FK_F0FCCCE7B7AE7853');
        $this->addSql('DROP TABLE weekly_picture_votes');
    }
}
