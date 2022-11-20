<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220820144900 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE equipment DROP FOREIGN KEY FK_D338D5837E3C61F9');
        $this->addSql('DROP INDEX IDX_D338D5837E3C61F9 ON equipment');
        $this->addSql('ALTER TABLE equipment DROP owner_id');
        $this->addSql('ALTER TABLE equipment_group DROP FOREIGN KEY FK_849965217E3C61F9');
        $this->addSql('DROP INDEX IDX_849965217E3C61F9 ON equipment_group');
        $this->addSql('ALTER TABLE equipment_group DROP owner_id');
        $this->addSql('ALTER TABLE room_equipment DROP FOREIGN KEY FK_4F9135EA7E3C61F9');
        $this->addSql('DROP INDEX IDX_4F9135EA7E3C61F9 ON room_equipment');
        $this->addSql('ALTER TABLE room_equipment DROP owner_id');
        $this->addSql('ALTER TABLE room_equipment_group DROP FOREIGN KEY FK_47DCEB777E3C61F9');
        $this->addSql('DROP INDEX IDX_47DCEB777E3C61F9 ON room_equipment_group');
        $this->addSql('ALTER TABLE room_equipment_group DROP owner_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE equipment ADD owner_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE equipment ADD CONSTRAINT FK_D338D5837E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_D338D5837E3C61F9 ON equipment (owner_id)');
        $this->addSql('ALTER TABLE equipment_group ADD owner_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE equipment_group ADD CONSTRAINT FK_849965217E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_849965217E3C61F9 ON equipment_group (owner_id)');
        $this->addSql('ALTER TABLE room_equipment ADD owner_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE room_equipment ADD CONSTRAINT FK_4F9135EA7E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_4F9135EA7E3C61F9 ON room_equipment (owner_id)');
        $this->addSql('ALTER TABLE room_equipment_group ADD owner_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE room_equipment_group ADD CONSTRAINT FK_47DCEB777E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_47DCEB777E3C61F9 ON room_equipment_group (owner_id)');
    }
}
