<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20250206071240 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'change description type';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE anime CHANGE description description LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE anime CHANGE description description VARCHAR(255) NOT NULL');
    }
}
