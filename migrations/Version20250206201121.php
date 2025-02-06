<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250206201121 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE anime_topic (id INT NOT NULL, anime_title VARCHAR(255) NOT NULL, episode VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE manga_topic (id INT NOT NULL, manga_title VARCHAR(255) NOT NULL, chapter VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE anime_topic ADD CONSTRAINT FK_F1E9BE24BF396750 FOREIGN KEY (id) REFERENCES topic (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE manga_topic ADD CONSTRAINT FK_B16227CBF396750 FOREIGN KEY (id) REFERENCES topic (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event_participants DROP FOREIGN KEY FK_9C7A7A619D1C3019');
        $this->addSql('DROP INDEX IDX_9C7A7A619D1C3019 ON event_participants');
        $this->addSql('DROP INDEX `primary` ON event_participants');
        $this->addSql('ALTER TABLE event_participants CHANGE participant_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE event_participants ADD CONSTRAINT FK_9C7A7A61A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_9C7A7A61A76ED395 ON event_participants (user_id)');
        $this->addSql('ALTER TABLE event_participants ADD PRIMARY KEY (event_id, user_id)');
        $this->addSql('ALTER TABLE topic ADD topic_type VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE anime_topic DROP FOREIGN KEY FK_F1E9BE24BF396750');
        $this->addSql('ALTER TABLE manga_topic DROP FOREIGN KEY FK_B16227CBF396750');
        $this->addSql('DROP TABLE anime_topic');
        $this->addSql('DROP TABLE manga_topic');
        $this->addSql('ALTER TABLE event_participants DROP FOREIGN KEY FK_9C7A7A61A76ED395');
        $this->addSql('DROP INDEX IDX_9C7A7A61A76ED395 ON event_participants');
        $this->addSql('DROP INDEX `PRIMARY` ON event_participants');
        $this->addSql('ALTER TABLE event_participants CHANGE user_id participant_id INT NOT NULL');
        $this->addSql('ALTER TABLE event_participants ADD CONSTRAINT FK_9C7A7A619D1C3019 FOREIGN KEY (participant_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_9C7A7A619D1C3019 ON event_participants (participant_id)');
        $this->addSql('ALTER TABLE event_participants ADD PRIMARY KEY (event_id, participant_id)');
        $this->addSql('ALTER TABLE topic DROP topic_type');
    }
}
