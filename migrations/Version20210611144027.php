<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210611144027 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE ecpc_crew_leaderboard_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE ecpc_crew_leaderboard (id INT NOT NULL, crew_id INT DEFAULT NULL, points DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9A59031A5FE259F6 ON ecpc_crew_leaderboard (crew_id)');
        $this->addSql('ALTER TABLE ecpc_crew_leaderboard ADD CONSTRAINT FK_9A59031A5FE259F6 FOREIGN KEY (crew_id) REFERENCES ecpc_crews (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ecpc_crews ADD leaderboard_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ecpc_crews ADD CONSTRAINT FK_A2B6C41F5CE067D8 FOREIGN KEY (leaderboard_id) REFERENCES ecpc_crew_leaderboard (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A2B6C41F5CE067D8 ON ecpc_crews (leaderboard_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE ecpc_crews DROP CONSTRAINT FK_A2B6C41F5CE067D8');
        $this->addSql('DROP SEQUENCE ecpc_crew_leaderboard_id_seq CASCADE');
        $this->addSql('DROP TABLE ecpc_crew_leaderboard');
        $this->addSql('DROP INDEX UNIQ_A2B6C41F5CE067D8');
        $this->addSql('ALTER TABLE ecpc_crews DROP leaderboard_id');
    }
}
