<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251106193402 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE joke_vote DROP CONSTRAINT fk_3610291730122c15');
        $this->addSql('ALTER TABLE joke_vote DROP CONSTRAINT fk_3610291770bee6d');
        $this->addSql('DROP INDEX idx_3610291730122c15');
        $this->addSql('DROP INDEX idx_3610291770bee6d');
        $this->addSql('ALTER TABLE joke_vote RENAME COLUMN joke_id TO joke_id_id');
        $this->addSql('ALTER TABLE joke_vote RENAME COLUMN visitor_id TO visitor_id_id');
        $this->addSql('ALTER TABLE joke_vote ADD CONSTRAINT FK_36102917B624BE7D FOREIGN KEY (joke_id_id) REFERENCES joke (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE joke_vote ADD CONSTRAINT FK_3610291771619AAE FOREIGN KEY (visitor_id_id) REFERENCES visitor (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_36102917B624BE7D ON joke_vote (joke_id_id)');
        $this->addSql('CREATE INDEX IDX_3610291771619AAE ON joke_vote (visitor_id_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE joke_vote DROP CONSTRAINT FK_36102917B624BE7D');
        $this->addSql('ALTER TABLE joke_vote DROP CONSTRAINT FK_3610291771619AAE');
        $this->addSql('DROP INDEX IDX_36102917B624BE7D');
        $this->addSql('DROP INDEX IDX_3610291771619AAE');
        $this->addSql('ALTER TABLE joke_vote RENAME COLUMN joke_id_id TO joke_id');
        $this->addSql('ALTER TABLE joke_vote RENAME COLUMN visitor_id_id TO visitor_id');
        $this->addSql('ALTER TABLE joke_vote ADD CONSTRAINT fk_3610291730122c15 FOREIGN KEY (joke_id) REFERENCES joke (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE joke_vote ADD CONSTRAINT fk_3610291770bee6d FOREIGN KEY (visitor_id) REFERENCES visitor (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_3610291730122c15 ON joke_vote (joke_id)');
        $this->addSql('CREATE INDEX idx_3610291770bee6d ON joke_vote (visitor_id)');
    }
}
