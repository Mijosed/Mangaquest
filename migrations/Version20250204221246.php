<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250204221246 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE review ADD user_id INT NOT NULL, ADD manga_id INT DEFAULT NULL, ADD anime_id INT DEFAULT NULL, ADD comment LONGTEXT DEFAULT NULL, DROP content');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C6A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C67B6461 FOREIGN KEY (manga_id) REFERENCES manga (id)');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C6794BBE89 FOREIGN KEY (anime_id) REFERENCES anime (id)');
        $this->addSql('CREATE INDEX IDX_794381C6A76ED395 ON review (user_id)');
        $this->addSql('CREATE INDEX IDX_794381C67B6461 ON review (manga_id)');
        $this->addSql('CREATE INDEX IDX_794381C6794BBE89 ON review (anime_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C6A76ED395');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C67B6461');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C6794BBE89');
        $this->addSql('DROP INDEX IDX_794381C6A76ED395 ON review');
        $this->addSql('DROP INDEX IDX_794381C67B6461 ON review');
        $this->addSql('DROP INDEX IDX_794381C6794BBE89 ON review');
        $this->addSql('ALTER TABLE review ADD content VARCHAR(255) NOT NULL, DROP user_id, DROP manga_id, DROP anime_id, DROP comment');
    }
}
