<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221024123804 extends AbstractMigration {
    public function getDescription(): string {
        return 'bild_der_woche => weekly_picture';
    }

    public function up(Schema $schema): void {
        $this->addSql('CREATE TABLE weekly_picture (id INT AUTO_INCREMENT NOT NULL, owner_user_id INT DEFAULT NULL, owner_role_id INT DEFAULT NULL, created_by_user_id INT DEFAULT NULL, last_modified_by_user_id INT DEFAULT NULL, datum DATE DEFAULT NULL, image_id LONGTEXT DEFAULT NULL, alternative_image_id LONGTEXT DEFAULT NULL, text LONGTEXT DEFAULT NULL, on_off INT DEFAULT 1 NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, last_modified_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, INDEX IDX_1EABE3862B18554A (owner_user_id), INDEX IDX_1EABE3865A75A473 (owner_role_id), INDEX IDX_1EABE3867D182D95 (created_by_user_id), INDEX IDX_1EABE3861A04EF5A (last_modified_by_user_id), INDEX datum_index (datum), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE weekly_picture ADD CONSTRAINT FK_1EABE3862B18554A FOREIGN KEY (owner_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE weekly_picture ADD CONSTRAINT FK_1EABE3865A75A473 FOREIGN KEY (owner_role_id) REFERENCES roles (id)');
        $this->addSql('ALTER TABLE weekly_picture ADD CONSTRAINT FK_1EABE3867D182D95 FOREIGN KEY (created_by_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE weekly_picture ADD CONSTRAINT FK_1EABE3861A04EF5A FOREIGN KEY (last_modified_by_user_id) REFERENCES users (id)');

        $rows = $this->connection->fetchAllAssociative('SELECT * FROM bild_der_woche ORDER BY id ASC');
        foreach ($rows as $row) {
            $weekly_picture = [
                'id' => $row['id'],
                'datum' => $row['datum'],
                'text' => $row['text'],
                'on_off' => 1,
                'image_id' => '001.jpg',
            ];
            $this->addSql('INSERT INTO weekly_picture (id, datum, text, on_off, image_id) VALUES (:id, :datum, :text, :on_off, :image_id)', $weekly_picture);
        }

        $this->addSql('DROP TABLE bild_der_woche');
    }

    public function down(Schema $schema): void {
        $this->addSql('CREATE TABLE bild_der_woche (id INT AUTO_INCREMENT NOT NULL, datum DATE DEFAULT NULL, bild1 LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, bild2 LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, on_off INT DEFAULT 0 NOT NULL, text LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, titel LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, bild1_breite INT DEFAULT NULL, bild2_breite INT DEFAULT NULL, INDEX datum_index (datum), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE weekly_picture DROP FOREIGN KEY FK_1EABE3862B18554A');
        $this->addSql('ALTER TABLE weekly_picture DROP FOREIGN KEY FK_1EABE3865A75A473');
        $this->addSql('ALTER TABLE weekly_picture DROP FOREIGN KEY FK_1EABE3867D182D95');
        $this->addSql('ALTER TABLE weekly_picture DROP FOREIGN KEY FK_1EABE3861A04EF5A');
        $this->addSql('DROP TABLE weekly_picture');
    }
}
