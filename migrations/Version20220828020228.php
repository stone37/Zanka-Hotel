<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220828020228 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE room_room_equipment (room_id INT NOT NULL, room_equipment_id INT NOT NULL, INDEX IDX_691C21754177093 (room_id), INDEX IDX_691C217E70DF16D (room_equipment_id), PRIMARY KEY(room_id, room_equipment_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE room_room_equipment ADD CONSTRAINT FK_691C21754177093 FOREIGN KEY (room_id) REFERENCES room (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE room_room_equipment ADD CONSTRAINT FK_691C217E70DF16D FOREIGN KEY (room_equipment_id) REFERENCES room_equipment (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE room_room_equipment_group');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE room_room_equipment_group (room_id INT NOT NULL, room_equipment_group_id INT NOT NULL, INDEX IDX_6B8FDBB25100F7A (room_equipment_group_id), INDEX IDX_6B8FDBB254177093 (room_id), PRIMARY KEY(room_id, room_equipment_group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE room_room_equipment_group ADD CONSTRAINT FK_6B8FDBB25100F7A FOREIGN KEY (room_equipment_group_id) REFERENCES room_equipment_group (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE room_room_equipment_group ADD CONSTRAINT FK_6B8FDBB254177093 FOREIGN KEY (room_id) REFERENCES room (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('DROP TABLE room_room_equipment');
    }
}
