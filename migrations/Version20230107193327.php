<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230107193327 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE hostel ADD plan_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE hostel ADD CONSTRAINT FK_38FBB167E899029B FOREIGN KEY (plan_id) REFERENCES plan (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_38FBB167E899029B ON hostel (plan_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE hostel DROP FOREIGN KEY FK_38FBB167E899029B');
        $this->addSql('DROP INDEX UNIQ_38FBB167E899029B ON hostel');
        $this->addSql('ALTER TABLE hostel DROP plan_id');
    }
}
