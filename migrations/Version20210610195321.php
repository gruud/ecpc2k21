<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210610195321 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ecpc_leaderboards ADD played_count INT NOT NULL');
        $this->addSql('ALTER TABLE ecpc_leaderboards ADD win_count INT NOT NULL');
        $this->addSql('ALTER TABLE ecpc_leaderboards ADD lose_count INT NOT NULL');
        $this->addSql('ALTER TABLE ecpc_leaderboards ADD winner_accurate_count INT NOT NULL');
        $this->addSql('ALTER TABLE ecpc_leaderboards ADD goal_average_accurate_count INT NOT NULL');
        $this->addSql('ALTER TABLE ecpc_leaderboards ADD perfect_count INT NOT NULL');
        $this->addSql('ALTER TABLE ecpc_leaderboards ADD prediction_with_points_count INT NOT NULL');
        $this->addSql('ALTER TABLE ecpc_leaderboards ADD jackpot_played_count INT NOT NULL');
        $this->addSql('ALTER TABLE ecpc_leaderboards ADD jackpot_points_count INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE ecpc_leaderboards DROP played_count');
        $this->addSql('ALTER TABLE ecpc_leaderboards DROP win_count');
        $this->addSql('ALTER TABLE ecpc_leaderboards DROP lose_count');
        $this->addSql('ALTER TABLE ecpc_leaderboards DROP winner_accurate_count');
        $this->addSql('ALTER TABLE ecpc_leaderboards DROP goal_average_accurate_count');
        $this->addSql('ALTER TABLE ecpc_leaderboards DROP perfect_count');
        $this->addSql('ALTER TABLE ecpc_leaderboards DROP prediction_with_points_count');
        $this->addSql('ALTER TABLE ecpc_leaderboards DROP jackpot_played_count');
        $this->addSql('ALTER TABLE ecpc_leaderboards DROP jackpot_points_count');
    }
}
