<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210606145829 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE ecpc_crews_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE ecpc_leaderboards_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE ecpc_predictions_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE ecpc_users_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE ecpc_crews (id INT NOT NULL, name VARCHAR(128) NOT NULL, points DOUBLE PRECISION DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE ecpc_game_rules (id INT NOT NULL, label VARCHAR(128) NOT NULL, points_winner INT NOT NULL, points_ga INT NOT NULL, points_perfect INT NOT NULL, jackpot_enabled BOOLEAN NOT NULL, jackpot_multiplicator INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE ecpc_games (id INT NOT NULL, team_home_id INT DEFAULT NULL, team_away_id INT DEFAULT NULL, rule_id INT DEFAULT NULL, kickoff TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, goals_home INT DEFAULT NULL, goals_away INT DEFAULT NULL, extra_time BOOLEAN DEFAULT NULL, penalties_home INT DEFAULT NULL, penalties_away INT DEFAULT NULL, game_phase VARCHAR(128) NOT NULL, game_group VARCHAR(1) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_637DBBC5D7B4B9AB ON ecpc_games (team_home_id)');
        $this->addSql('CREATE INDEX IDX_637DBBC5729679A8 ON ecpc_games (team_away_id)');
        $this->addSql('CREATE INDEX IDX_637DBBC5744E0351 ON ecpc_games (rule_id)');
        $this->addSql('CREATE TABLE ecpc_leaderboards (id INT NOT NULL, user_id INT DEFAULT NULL, points INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1496C628A76ED395 ON ecpc_leaderboards (user_id)');
        $this->addSql('CREATE TABLE ecpc_predictions (id INT NOT NULL, user_id INT DEFAULT NULL, game_id INT DEFAULT NULL, goals_home INT NOT NULL, goals_away INT NOT NULL, jackpot BOOLEAN NOT NULL, points INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E59F9FCA76ED395 ON ecpc_predictions (user_id)');
        $this->addSql('CREATE INDEX IDX_E59F9FCE48FD905 ON ecpc_predictions (game_id)');
        $this->addSql('CREATE TABLE ecpc_teams (id INT NOT NULL, name VARCHAR(64) NOT NULL, abbreviation VARCHAR(3) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE ecpc_users (id INT NOT NULL, crew_id INT DEFAULT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, department VARCHAR(256) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_88DD351DF85E0677 ON ecpc_users (username)');
        $this->addSql('CREATE INDEX IDX_88DD351D5FE259F6 ON ecpc_users (crew_id)');
        $this->addSql('ALTER TABLE ecpc_games ADD CONSTRAINT FK_637DBBC5D7B4B9AB FOREIGN KEY (team_home_id) REFERENCES ecpc_teams (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ecpc_games ADD CONSTRAINT FK_637DBBC5729679A8 FOREIGN KEY (team_away_id) REFERENCES ecpc_teams (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ecpc_games ADD CONSTRAINT FK_637DBBC5744E0351 FOREIGN KEY (rule_id) REFERENCES ecpc_game_rules (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ecpc_leaderboards ADD CONSTRAINT FK_1496C628A76ED395 FOREIGN KEY (user_id) REFERENCES ecpc_users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ecpc_predictions ADD CONSTRAINT FK_E59F9FCA76ED395 FOREIGN KEY (user_id) REFERENCES ecpc_users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ecpc_predictions ADD CONSTRAINT FK_E59F9FCE48FD905 FOREIGN KEY (game_id) REFERENCES ecpc_games (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ecpc_users ADD CONSTRAINT FK_88DD351D5FE259F6 FOREIGN KEY (crew_id) REFERENCES ecpc_crews (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE ecpc_users DROP CONSTRAINT FK_88DD351D5FE259F6');
        $this->addSql('ALTER TABLE ecpc_games DROP CONSTRAINT FK_637DBBC5744E0351');
        $this->addSql('ALTER TABLE ecpc_predictions DROP CONSTRAINT FK_E59F9FCE48FD905');
        $this->addSql('ALTER TABLE ecpc_games DROP CONSTRAINT FK_637DBBC5D7B4B9AB');
        $this->addSql('ALTER TABLE ecpc_games DROP CONSTRAINT FK_637DBBC5729679A8');
        $this->addSql('ALTER TABLE ecpc_leaderboards DROP CONSTRAINT FK_1496C628A76ED395');
        $this->addSql('ALTER TABLE ecpc_predictions DROP CONSTRAINT FK_E59F9FCA76ED395');
        $this->addSql('DROP SEQUENCE ecpc_crews_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE ecpc_leaderboards_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE ecpc_predictions_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE ecpc_users_id_seq CASCADE');
        $this->addSql('DROP TABLE ecpc_crews');
        $this->addSql('DROP TABLE ecpc_game_rules');
        $this->addSql('DROP TABLE ecpc_games');
        $this->addSql('DROP TABLE ecpc_leaderboards');
        $this->addSql('DROP TABLE ecpc_predictions');
        $this->addSql('DROP TABLE ecpc_teams');
        $this->addSql('DROP TABLE ecpc_users');
    }
}
