<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211011125722 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE room_region');
        $this->addSql('DROP INDEX IDX_67F068BC54177093');
        $this->addSql('DROP INDEX IDX_67F068BCF675F31B');
        $this->addSql('CREATE TEMPORARY TABLE __temp__commentaire AS SELECT id, author_id, room_id, content, note, date, status FROM commentaire');
        $this->addSql('DROP TABLE commentaire');
        $this->addSql('CREATE TABLE commentaire (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, author_id INTEGER NOT NULL, room_id INTEGER NOT NULL, content CLOB NOT NULL COLLATE BINARY, note INTEGER NOT NULL, date DATETIME NOT NULL, status VARCHAR(255) NOT NULL COLLATE BINARY, CONSTRAINT FK_67F068BCF675F31B FOREIGN KEY (author_id) REFERENCES client (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_67F068BC54177093 FOREIGN KEY (room_id) REFERENCES room (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO commentaire (id, author_id, room_id, content, note, date, status) SELECT id, author_id, room_id, content, note, date, status FROM __temp__commentaire');
        $this->addSql('DROP TABLE __temp__commentaire');
        $this->addSql('CREATE INDEX IDX_67F068BC54177093 ON commentaire (room_id)');
        $this->addSql('CREATE INDEX IDX_67F068BCF675F31B ON commentaire (author_id)');
        $this->addSql('DROP INDEX IDX_42C8495554177093');
        $this->addSql('DROP INDEX IDX_42C8495519EB6921');
        $this->addSql('CREATE TEMPORARY TABLE __temp__reservation AS SELECT id, room_id, client_id, date_debut, date_fin, confirmed FROM reservation');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('CREATE TABLE reservation (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, room_id INTEGER NOT NULL, client_id INTEGER NOT NULL, date_debut DATETIME NOT NULL, date_fin DATETIME NOT NULL, confirmed BOOLEAN NOT NULL, CONSTRAINT FK_42C8495554177093 FOREIGN KEY (room_id) REFERENCES room (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_42C8495519EB6921 FOREIGN KEY (client_id) REFERENCES client (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO reservation (id, room_id, client_id, date_debut, date_fin, confirmed) SELECT id, room_id, client_id, date_debut, date_fin, confirmed FROM __temp__reservation');
        $this->addSql('DROP TABLE __temp__reservation');
        $this->addSql('CREATE INDEX IDX_42C8495554177093 ON reservation (room_id)');
        $this->addSql('CREATE INDEX IDX_42C8495519EB6921 ON reservation (client_id)');
        $this->addSql('DROP INDEX IDX_729F519B7E3C61F9');
        $this->addSql('CREATE TEMPORARY TABLE __temp__room AS SELECT id, owner_id, summary, description, capacity, superficy, price, address, image_name, image_updated_at FROM room');
        $this->addSql('DROP TABLE room');
        $this->addSql('CREATE TABLE room (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, owner_id INTEGER DEFAULT NULL, region_id INTEGER DEFAULT NULL, summary CLOB NOT NULL COLLATE BINARY, description CLOB DEFAULT NULL COLLATE BINARY, capacity INTEGER DEFAULT NULL, superficy INTEGER DEFAULT NULL, price INTEGER DEFAULT NULL, address CLOB DEFAULT NULL COLLATE BINARY, image_name VARCHAR(255) DEFAULT NULL COLLATE BINARY, image_updated_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        , CONSTRAINT FK_729F519B7E3C61F9 FOREIGN KEY (owner_id) REFERENCES owner (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_729F519B98260155 FOREIGN KEY (region_id) REFERENCES region (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO room (id, owner_id, summary, description, capacity, superficy, price, address, image_name, image_updated_at) SELECT id, owner_id, summary, description, capacity, superficy, price, address, image_name, image_updated_at FROM __temp__room');
        $this->addSql('DROP TABLE __temp__room');
        $this->addSql('CREATE INDEX IDX_729F519B7E3C61F9 ON room (owner_id)');
        $this->addSql('CREATE INDEX IDX_729F519B98260155 ON room (region_id)');
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74');
        $this->addSql('DROP INDEX UNIQ_8D93D6497E3C61F9');
        $this->addSql('DROP INDEX UNIQ_8D93D64919EB6921');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user AS SELECT id, owner_id, client_id, email, roles, password, username, firstname, lastname FROM user');
        $this->addSql('DROP TABLE user');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, owner_id INTEGER DEFAULT NULL, client_id INTEGER DEFAULT NULL, email VARCHAR(180) NOT NULL COLLATE BINARY, roles CLOB NOT NULL COLLATE BINARY --(DC2Type:json)
        , password VARCHAR(255) NOT NULL COLLATE BINARY, username VARCHAR(255) NOT NULL COLLATE BINARY, firstname VARCHAR(255) NOT NULL COLLATE BINARY, lastname VARCHAR(255) NOT NULL COLLATE BINARY, CONSTRAINT FK_8D93D6497E3C61F9 FOREIGN KEY (owner_id) REFERENCES owner (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_8D93D64919EB6921 FOREIGN KEY (client_id) REFERENCES client (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO user (id, owner_id, client_id, email, roles, password, username, firstname, lastname) SELECT id, owner_id, client_id, email, roles, password, username, firstname, lastname FROM __temp__user');
        $this->addSql('DROP TABLE __temp__user');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D6497E3C61F9 ON user (owner_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D64919EB6921 ON user (client_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE room_region (room_id INTEGER NOT NULL, region_id INTEGER NOT NULL, PRIMARY KEY(room_id, region_id))');
        $this->addSql('CREATE INDEX IDX_4E2C37B798260155 ON room_region (region_id)');
        $this->addSql('CREATE INDEX IDX_4E2C37B754177093 ON room_region (room_id)');
        $this->addSql('DROP INDEX IDX_67F068BCF675F31B');
        $this->addSql('DROP INDEX IDX_67F068BC54177093');
        $this->addSql('CREATE TEMPORARY TABLE __temp__commentaire AS SELECT id, author_id, room_id, content, note, date, status FROM commentaire');
        $this->addSql('DROP TABLE commentaire');
        $this->addSql('CREATE TABLE commentaire (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, author_id INTEGER NOT NULL, room_id INTEGER NOT NULL, content CLOB NOT NULL, note INTEGER NOT NULL, date DATETIME NOT NULL, status VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO commentaire (id, author_id, room_id, content, note, date, status) SELECT id, author_id, room_id, content, note, date, status FROM __temp__commentaire');
        $this->addSql('DROP TABLE __temp__commentaire');
        $this->addSql('CREATE INDEX IDX_67F068BCF675F31B ON commentaire (author_id)');
        $this->addSql('CREATE INDEX IDX_67F068BC54177093 ON commentaire (room_id)');
        $this->addSql('DROP INDEX IDX_42C8495554177093');
        $this->addSql('DROP INDEX IDX_42C8495519EB6921');
        $this->addSql('CREATE TEMPORARY TABLE __temp__reservation AS SELECT id, room_id, client_id, date_debut, date_fin, confirmed FROM reservation');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('CREATE TABLE reservation (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, room_id INTEGER NOT NULL, client_id INTEGER NOT NULL, date_debut DATETIME NOT NULL, date_fin DATETIME NOT NULL, confirmed BOOLEAN NOT NULL)');
        $this->addSql('INSERT INTO reservation (id, room_id, client_id, date_debut, date_fin, confirmed) SELECT id, room_id, client_id, date_debut, date_fin, confirmed FROM __temp__reservation');
        $this->addSql('DROP TABLE __temp__reservation');
        $this->addSql('CREATE INDEX IDX_42C8495554177093 ON reservation (room_id)');
        $this->addSql('CREATE INDEX IDX_42C8495519EB6921 ON reservation (client_id)');
        $this->addSql('DROP INDEX IDX_729F519B7E3C61F9');
        $this->addSql('DROP INDEX IDX_729F519B98260155');
        $this->addSql('CREATE TEMPORARY TABLE __temp__room AS SELECT id, owner_id, summary, description, capacity, superficy, price, address, image_name, image_updated_at FROM room');
        $this->addSql('DROP TABLE room');
        $this->addSql('CREATE TABLE room (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, owner_id INTEGER DEFAULT NULL, summary CLOB NOT NULL, description CLOB DEFAULT NULL, capacity INTEGER DEFAULT NULL, superficy INTEGER DEFAULT NULL, price INTEGER DEFAULT NULL, address CLOB DEFAULT NULL, image_name VARCHAR(255) DEFAULT NULL, image_updated_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        )');
        $this->addSql('INSERT INTO room (id, owner_id, summary, description, capacity, superficy, price, address, image_name, image_updated_at) SELECT id, owner_id, summary, description, capacity, superficy, price, address, image_name, image_updated_at FROM __temp__room');
        $this->addSql('DROP TABLE __temp__room');
        $this->addSql('CREATE INDEX IDX_729F519B7E3C61F9 ON room (owner_id)');
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74');
        $this->addSql('DROP INDEX UNIQ_8D93D6497E3C61F9');
        $this->addSql('DROP INDEX UNIQ_8D93D64919EB6921');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user AS SELECT id, owner_id, client_id, email, roles, password, username, firstname, lastname FROM user');
        $this->addSql('DROP TABLE user');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, owner_id INTEGER DEFAULT NULL, client_id INTEGER DEFAULT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO user (id, owner_id, client_id, email, roles, password, username, firstname, lastname) SELECT id, owner_id, client_id, email, roles, password, username, firstname, lastname FROM __temp__user');
        $this->addSql('DROP TABLE __temp__user');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D6497E3C61F9 ON user (owner_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D64919EB6921 ON user (client_id)');
    }
}
