<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250131212154 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE topic ADD is_approved TINYINT(1) NOT NULL, ADD is_nsfw TINYINT(1) NOT NULL, ADD has_spoiler TINYINT(1) NOT NULL, ADD spoiler_warning VARCHAR(255) DEFAULT NULL, ADD image_filename VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD birth_date DATE DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `user` DROP birth_date');
        $this->addSql('ALTER TABLE topic DROP is_approved, DROP is_nsfw, DROP has_spoiler, DROP spoiler_warning, DROP image_filename');
    }
}
