<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221207235912 extends AbstractMigration {
    public function getDescription(): string {
        return 'Migrate galerie -> aktuell';
    }

    public function up(Schema $schema): void {
        $rows = $this->connection->fetchAllAssociative('SELECT * FROM galerie');
        $aktuell_typ_by_galerie_typ = [
            'foto' => 'galerie',
            'movie' => 'video',
        ];
        foreach ($rows as $row) {
            $datetime = new \DateTime($row['datum']);
            $isodatetime = $datetime->format('Y-m-d H:i:s');
            $aktuell = [
                'id' => intval($row['id']) + 1200,
                'titel' => $row['titel'],
                'datum' => $row['datum'],
                'textlang' => $row['content'],
                'autor' => $row['autor'],
                'typ' => $aktuell_typ_by_galerie_typ[$row['typ']] ?? 'galerie',
                'on_off' => $row['on_off'],
                'counter' => $row['counter'] ?? 0,
                'created_at' => $isodatetime,
                'last_modified_at' => $isodatetime,
            ];
            $this->addSql("INSERT INTO aktuell (id, titel, datum, textlang, autor, typ, on_off, counter, created_at, last_modified_at, tags, termin, newsletter, text) VALUES (:id, :titel, :datum, :textlang, :autor, :typ, :on_off, :counter, :created_at, :last_modified_at, '', '0', '0', '')", $aktuell);
        }
    }

    public function down(Schema $schema): void {
    }
}
