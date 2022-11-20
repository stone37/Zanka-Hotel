<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220826002835 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE room_taxe (room_id INT NOT NULL, taxe_id INT NOT NULL, INDEX IDX_3536C58D54177093 (room_id), INDEX IDX_3536C58D1AB947A4 (taxe_id), PRIMARY KEY(room_id, taxe_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE room_taxe ADD CONSTRAINT FK_3536C58D54177093 FOREIGN KEY (room_id) REFERENCES room (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE room_taxe ADD CONSTRAINT FK_3536C58D1AB947A4 FOREIGN KEY (taxe_id) REFERENCES taxe (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE room_taxe');
    }
}
