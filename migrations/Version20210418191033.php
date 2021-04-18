<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210418191033 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE book DROP FOREIGN KEY FK_CBE5A33139759382');
        $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_CBE5A33139759382 FOREIGN KEY (borrowed_by_id) REFERENCES member (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE book DROP FOREIGN KEY FK_CBE5A33139759382');
        $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_CBE5A33139759382 FOREIGN KEY (borrowed_by_id) REFERENCES member (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
