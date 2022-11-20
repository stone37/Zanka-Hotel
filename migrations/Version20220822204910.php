<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220822204910 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE bedding (id INT AUTO_INCREMENT NOT NULL, room_id INT NOT NULL, name VARCHAR(255) DEFAULT NULL, number INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_6E24EFFF54177093 (room_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bedding ADD CONSTRAINT FK_6E24EFFF54177093 FOREIGN KEY (room_id) REFERENCES room (id)');
        $this->addSql('ALTER TABLE room ADD perfect_name VARCHAR(255) DEFAULT NULL, ADD specification VARCHAR(255) DEFAULT NULL, ADD feature VARCHAR(255) DEFAULT NULL, ADD amenities VARCHAR(255) DEFAULT NULL, ADD occupant INT DEFAULT NULL, DROP maximum_adults, DROP maximum_of_children, CHANGE smoker smoker INT DEFAULT NULL, CHANGE couchage type VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE bedding');
        $this->addSql('ALTER TABLE room ADD maximum_of_children INT DEFAULT NULL, ADD couchage VARCHAR(255) DEFAULT NULL, DROP type, DROP perfect_name, DROP specification, DROP feature, DROP amenities, CHANGE smoker smoker VARCHAR(255) DEFAULT NULL, CHANGE occupant maximum_adults INT DEFAULT NULL');
    }
}
