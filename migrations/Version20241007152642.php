<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241007152642 extends AbstractMigration {
    public function getDescription(): string {
        return 'Add avatar image ID';
    }

    public function up(Schema $schema): void {
        $this->addSql('ALTER TABLE users ADD avatar_image_id LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void {
        $this->addSql('ALTER TABLE users DROP avatar_image_id');
    }
}
