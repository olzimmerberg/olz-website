<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240219120442 extends AbstractMigration {
    public function getDescription(): string {
        return 'Remove alternative image';
    }

    public function up(Schema $schema): void {
        $this->addSql('ALTER TABLE weekly_picture DROP alternative_image_id');
    }

    public function down(Schema $schema): void {
        $this->addSql('ALTER TABLE weekly_picture ADD alternative_image_id LONGTEXT DEFAULT NULL');
    }
}
