<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200423071546 extends AbstractMigration {
    public function getDescription(): string {
        return 'Introduce users and roles';
    }

    public function up(Schema $schema): void {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE roles (id INT AUTO_INCREMENT NOT NULL, username LONGTEXT NOT NULL, old_username LONGTEXT DEFAULT NULL, name LONGTEXT NOT NULL, parent_role INT DEFAULT NULL, index_within_parent INT DEFAULT NULL, featured_index INT DEFAULT NULL, can_have_child_roles TINYINT(1) DEFAULT \'0\' NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, username LONGTEXT NOT NULL, old_username LONGTEXT DEFAULT NULL, password LONGTEXT NOT NULL, email LONGTEXT NOT NULL, first_name LONGTEXT NOT NULL, last_name LONGTEXT NOT NULL, zugriff LONGTEXT NOT NULL, root LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users_roles (user_id INT NOT NULL, role_id INT NOT NULL, INDEX IDX_51498A8EA76ED395 (user_id), INDEX IDX_51498A8ED60322AC (role_id), PRIMARY KEY(user_id, role_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE users_roles ADD CONSTRAINT FK_51498A8EA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE users_roles ADD CONSTRAINT FK_51498A8ED60322AC FOREIGN KEY (role_id) REFERENCES roles (id) ON DELETE CASCADE');

        $rows = $this->connection->fetchAll('SELECT * FROM user');
        foreach ($rows as $row) {
            $user = [
                'username' => $row['benutzername'],
                'password' => password_hash($row['passwort'], PASSWORD_DEFAULT),
                'email' => '',
                'first_name' => '',
                'last_name' => '',
                'zugriff' => $row['zugriff'],
                'root' => $row['root'],
            ];
            $this->addSql('INSERT INTO users (username, password, email, first_name, last_name, zugriff, root) VALUES (:username, :password, :email, :first_name, :last_name, :zugriff, :root)', $user);
        }
    }

    public function down(Schema $schema): void {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE users_roles DROP FOREIGN KEY FK_51498A8ED60322AC');
        $this->addSql('ALTER TABLE users_roles DROP FOREIGN KEY FK_51498A8EA76ED395');
        $this->addSql('DROP TABLE roles');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE users_roles');
    }
}
