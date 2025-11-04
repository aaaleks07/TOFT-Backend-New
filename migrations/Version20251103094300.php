<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251103094300 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE joke (id SERIAL NOT NULL, text VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE joke_vote (id SERIAL NOT NULL, joke_id_id INT NOT NULL, visitor_id_id UUID NOT NULL, vote INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_36102917B624BE7D ON joke_vote (joke_id_id)');
        $this->addSql('CREATE INDEX IDX_3610291771619AAE ON joke_vote (visitor_id_id)');
        $this->addSql('COMMENT ON COLUMN joke_vote.visitor_id_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE joke_vote ADD CONSTRAINT FK_36102917B624BE7D FOREIGN KEY (joke_id_id) REFERENCES joke (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE joke_vote ADD CONSTRAINT FK_3610291771619AAE FOREIGN KEY (visitor_id_id) REFERENCES visitor (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE joke_vote DROP CONSTRAINT FK_36102917B624BE7D');
        $this->addSql('ALTER TABLE joke_vote DROP CONSTRAINT FK_3610291771619AAE');
        $this->addSql('DROP TABLE joke');
        $this->addSql('DROP TABLE joke_vote');
    }
}
