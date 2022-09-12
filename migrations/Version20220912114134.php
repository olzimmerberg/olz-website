<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220912114134 extends AbstractMigration {
    public function getDescription(): string {
        return 'Add comment';
    }

    public function up(Schema $schema): void {
        $this->addSql('ALTER TABLE roles CHANGE index_within_parent index_within_parent INT DEFAULT NULL COMMENT \'negative value: hide role\'');
    }

    public function down(Schema $schema): void {
        $this->addSql('ALTER TABLE roles CHANGE index_within_parent index_within_parent INT DEFAULT NULL');
    }
}
