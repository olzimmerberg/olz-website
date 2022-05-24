<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201123220256 extends AbstractMigration {
    public function getDescription(): string {
        return 'Add external login links, extend user data';
    }

    public function up(Schema $schema): void {
        $this->addSql('CREATE TABLE facebook_links (id BIGINT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, access_token LONGTEXT NOT NULL, expires_at DATETIME NOT NULL, refresh_token LONGTEXT NOT NULL, facebook_user LONGTEXT NOT NULL, INDEX user_id_index (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE google_links (id BIGINT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, access_token LONGTEXT NOT NULL, expires_at DATETIME NOT NULL, refresh_token LONGTEXT NOT NULL, google_user LONGTEXT NOT NULL, INDEX user_id_index (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE strava_links (id BIGINT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, access_token LONGTEXT NOT NULL, expires_at DATETIME NOT NULL, refresh_token LONGTEXT NOT NULL, strava_user LONGTEXT NOT NULL, INDEX user_id_index (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE facebook_links ADD CONSTRAINT FK_3444E616A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE google_links ADD CONSTRAINT FK_486FA817A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE strava_links ADD CONSTRAINT FK_72D84739A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE users ADD email_is_verified TINYINT(1) NOT NULL, ADD email_verification_token LONGTEXT DEFAULT NULL, ADD gender VARCHAR(2) DEFAULT NULL COMMENT \'M(ale), F(emale), or O(ther)\', ADD street LONGTEXT DEFAULT NULL, ADD postal_code LONGTEXT DEFAULT NULL, ADD city LONGTEXT DEFAULT NULL, ADD region LONGTEXT DEFAULT NULL, ADD country_code VARCHAR(3) DEFAULT NULL COMMENT \'two-letter code (ISO-3166-alpha-2)\', ADD birthdate DATE DEFAULT NULL, ADD phone LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void {
        $this->addSql('DROP TABLE facebook_links');
        $this->addSql('DROP TABLE google_links');
        $this->addSql('DROP TABLE strava_links');
        $this->addSql('ALTER TABLE users DROP email_is_verified, DROP email_verification_token, DROP gender, DROP street, DROP postal_code, DROP city, DROP region, DROP country_code, DROP birthdate, DROP phone');
    }
}
