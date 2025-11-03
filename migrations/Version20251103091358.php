<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251103091358 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE completed_quiz (id SERIAL NOT NULL, visitor_id_id UUID NOT NULL, quiz_id_id INT NOT NULL, score INT NOT NULL, completed_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C0900FAE71619AAE ON completed_quiz (visitor_id_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C0900FAE8337E7D7 ON completed_quiz (quiz_id_id)');
        $this->addSql('COMMENT ON COLUMN completed_quiz.visitor_id_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE quiz (id SERIAL NOT NULL, fk_visitor_id_id UUID NOT NULL, name VARCHAR(255) NOT NULL, quiz_title VARCHAR(255) DEFAULT NULL, questions JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A412FA92152BEF6C ON quiz (fk_visitor_id_id)');
        $this->addSql('COMMENT ON COLUMN quiz.fk_visitor_id_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE snake (id SERIAL NOT NULL, fk_visitor_id_id UUID NOT NULL, pkt INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_516D6B9E152BEF6C ON snake (fk_visitor_id_id)');
        $this->addSql('COMMENT ON COLUMN snake.fk_visitor_id_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE tetris (id SERIAL NOT NULL, fk_visitor_id_id UUID NOT NULL, pkt INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_BEB86DD4152BEF6C ON tetris (fk_visitor_id_id)');
        $this->addSql('COMMENT ON COLUMN tetris.fk_visitor_id_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE visitor (id UUID NOT NULL, name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN visitor.id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE completed_quiz ADD CONSTRAINT FK_C0900FAE71619AAE FOREIGN KEY (visitor_id_id) REFERENCES visitor (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE completed_quiz ADD CONSTRAINT FK_C0900FAE8337E7D7 FOREIGN KEY (quiz_id_id) REFERENCES quiz (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE quiz ADD CONSTRAINT FK_A412FA92152BEF6C FOREIGN KEY (fk_visitor_id_id) REFERENCES visitor (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE snake ADD CONSTRAINT FK_516D6B9E152BEF6C FOREIGN KEY (fk_visitor_id_id) REFERENCES visitor (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tetris ADD CONSTRAINT FK_BEB86DD4152BEF6C FOREIGN KEY (fk_visitor_id_id) REFERENCES visitor (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE completed_quiz DROP CONSTRAINT FK_C0900FAE71619AAE');
        $this->addSql('ALTER TABLE completed_quiz DROP CONSTRAINT FK_C0900FAE8337E7D7');
        $this->addSql('ALTER TABLE quiz DROP CONSTRAINT FK_A412FA92152BEF6C');
        $this->addSql('ALTER TABLE snake DROP CONSTRAINT FK_516D6B9E152BEF6C');
        $this->addSql('ALTER TABLE tetris DROP CONSTRAINT FK_BEB86DD4152BEF6C');
        $this->addSql('DROP TABLE completed_quiz');
        $this->addSql('DROP TABLE quiz');
        $this->addSql('DROP TABLE snake');
        $this->addSql('DROP TABLE tetris');
        $this->addSql('DROP TABLE visitor');
    }
}
