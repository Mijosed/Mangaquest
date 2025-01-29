<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250129082904 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE manga ADD manga_dex_id VARCHAR(255) NOT NULL, ADD status VARCHAR(50) DEFAULT NULL, ADD year INT DEFAULT NULL, DROP author, DROP publication_date, DROP genre, CHANGE description description LONGTEXT DEFAULT NULL, CHANGE cover_image cover_image VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE manga ADD publication_date DATE NOT NULL, ADD genre VARCHAR(255) NOT NULL, DROP status, DROP year, CHANGE description description VARCHAR(255) NOT NULL, CHANGE cover_image cover_image VARCHAR(255) NOT NULL, CHANGE manga_dex_id author VARCHAR(255) NOT NULL');
    }
}
