<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251104101515 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE joke_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE joke_vote_id_seq CASCADE');
        $this->addSql('ALTER TABLE joke_vote DROP CONSTRAINT fk_3610291771619aae');
        $this->addSql('ALTER TABLE joke_vote DROP CONSTRAINT fk_36102917b624be7d');
        $this->addSql('DROP TABLE joke_vote');
        $this->addSql('DROP TABLE joke');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE joke_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE joke_vote_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE joke_vote (id SERIAL NOT NULL, joke_id_id INT NOT NULL, visitor_id_id UUID NOT NULL, vote INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_3610291771619aae ON joke_vote (visitor_id_id)');
        $this->addSql('CREATE INDEX idx_36102917b624be7d ON joke_vote (joke_id_id)');
        $this->addSql('COMMENT ON COLUMN joke_vote.visitor_id_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE joke (id SERIAL NOT NULL, text VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE joke_vote ADD CONSTRAINT fk_3610291771619aae FOREIGN KEY (visitor_id_id) REFERENCES visitor (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE joke_vote ADD CONSTRAINT fk_36102917b624be7d FOREIGN KEY (joke_id_id) REFERENCES joke (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
