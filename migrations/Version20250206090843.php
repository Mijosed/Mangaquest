<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250206090843 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE event (id INT AUTO_INCREMENT NOT NULL, event_creator_id INT NOT NULL, title VARCHAR(255) NOT NULL, date DATETIME NOT NULL, location VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, INDEX IDX_3BAE0AA739CCD789 (event_creator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event_participants (event_id INT NOT NULL, participant_id INT NOT NULL, INDEX IDX_9C7A7A6171F7E88B (event_id), INDEX IDX_9C7A7A619D1C3019 (participant_id), PRIMARY KEY(event_id, participant_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event_organizers (event_id INT NOT NULL, organizer_id INT NOT NULL, INDEX IDX_8A75E2D371F7E88B (event_id), INDEX IDX_8A75E2D3876C4DDA (organizer_id), PRIMARY KEY(event_id, organizer_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA739CCD789 FOREIGN KEY (event_creator_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE event_participants ADD CONSTRAINT FK_9C7A7A6171F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event_participants ADD CONSTRAINT FK_9C7A7A619D1C3019 FOREIGN KEY (participant_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event_organizers ADD CONSTRAINT FK_8A75E2D371F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event_organizers ADD CONSTRAINT FK_8A75E2D3876C4DDA FOREIGN KEY (organizer_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user ADD first_name VARCHAR(255) NOT NULL, ADD last_name VARCHAR(255) NOT NULL, ADD type VARCHAR(255) NOT NULL, ADD is_subscribed_to_newsletter TINYINT(1) DEFAULT NULL, ADD preferences VARCHAR(255) DEFAULT NULL, ADD organization VARCHAR(255) DEFAULT NULL, ADD phone VARCHAR(20) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA739CCD789');
        $this->addSql('ALTER TABLE event_participants DROP FOREIGN KEY FK_9C7A7A6171F7E88B');
        $this->addSql('ALTER TABLE event_participants DROP FOREIGN KEY FK_9C7A7A619D1C3019');
        $this->addSql('ALTER TABLE event_organizers DROP FOREIGN KEY FK_8A75E2D371F7E88B');
        $this->addSql('ALTER TABLE event_organizers DROP FOREIGN KEY FK_8A75E2D3876C4DDA');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE event_participants');
        $this->addSql('DROP TABLE event_organizers');
        $this->addSql('ALTER TABLE `user` DROP first_name, DROP last_name, DROP type, DROP is_subscribed_to_newsletter, DROP preferences, DROP organization, DROP phone');
    }
}
