<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251228224429 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE avis_client ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE avis_client ADD CONSTRAINT FK_708E90EFA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_708E90EFA76ED395 ON avis_client (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE avis_client DROP FOREIGN KEY FK_708E90EFA76ED395');
        $this->addSql('DROP INDEX IDX_708E90EFA76ED395 ON avis_client');
        $this->addSql('ALTER TABLE avis_client DROP user_id');
    }
}
