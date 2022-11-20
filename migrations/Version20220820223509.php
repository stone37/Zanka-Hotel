<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220820223509 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE hostel_equipment (hostel_id INT NOT NULL, equipment_id INT NOT NULL, INDEX IDX_CB0D5C60FC68ACC0 (hostel_id), INDEX IDX_CB0D5C60517FE9FE (equipment_id), PRIMARY KEY(hostel_id, equipment_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE hostel_equipment ADD CONSTRAINT FK_CB0D5C60FC68ACC0 FOREIGN KEY (hostel_id) REFERENCES hostel (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE hostel_equipment ADD CONSTRAINT FK_CB0D5C60517FE9FE FOREIGN KEY (equipment_id) REFERENCES equipment (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE hostel_equipment_group');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE hostel_equipment_group (hostel_id INT NOT NULL, equipment_group_id INT NOT NULL, INDEX IDX_7D7F24B5CE975D61 (equipment_group_id), INDEX IDX_7D7F24B5FC68ACC0 (hostel_id), PRIMARY KEY(hostel_id, equipment_group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE hostel_equipment_group ADD CONSTRAINT FK_7D7F24B5CE975D61 FOREIGN KEY (equipment_group_id) REFERENCES equipment_group (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE hostel_equipment_group ADD CONSTRAINT FK_7D7F24B5FC68ACC0 FOREIGN KEY (hostel_id) REFERENCES hostel (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('DROP TABLE hostel_equipment');
    }
}
