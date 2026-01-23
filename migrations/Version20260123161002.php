<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260123161002 extends AbstractMigration {
    public function getDescription(): string {
        return 'Add forwarded emails';
    }

    public function up(Schema $schema): void {
        $this->addSql('CREATE TABLE forwarded_emails (sender_address VARCHAR(255) NOT NULL, subject VARCHAR(255) NOT NULL, body LONGTEXT NOT NULL, forwarded_at DATETIME DEFAULT NULL, error_message VARCHAR(255) DEFAULT NULL, id BIGINT AUTO_INCREMENT NOT NULL, recipient_user_id INT DEFAULT NULL, INDEX IDX_FB65F126B15EFB97 (recipient_user_id), INDEX recipient_user_id_forwarded_at_index (recipient_user_id, forwarded_at), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE forwarded_emails ADD CONSTRAINT FK_FB65F126B15EFB97 FOREIGN KEY (recipient_user_id) REFERENCES users (id)');
    }

    public function down(Schema $schema): void {
        $this->addSql('ALTER TABLE forwarded_emails DROP FOREIGN KEY FK_FB65F126B15EFB97');
        $this->addSql('DROP TABLE forwarded_emails');
    }
}
