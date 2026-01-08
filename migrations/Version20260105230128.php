<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260105230128 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE commande');
        $this->addSql('DROP TABLE facture');
        $this->addSql('DROP TABLE ligne_commande');
        $this->addSql('DROP TABLE product');
        $this->addSql('ALTER TABLE paiement DROP FOREIGN KEY `FK_B1DC7A1E82EA2E54`');
        $this->addSql('DROP INDEX IDX_B1DC7A1E82EA2E54 ON paiement');
        $this->addSql('ALTER TABLE paiement DROP commande_id');
        $this->addSql('ALTER TABLE reclamation DROP FOREIGN KEY `FK_CE6064044584665A`');
        $this->addSql('ALTER TABLE reclamation DROP FOREIGN KEY `FK_CE60640482EA2E54`');
        $this->addSql('ALTER TABLE reclamation DROP FOREIGN KEY `FK_CE606404A76ED395`');
        $this->addSql('DROP INDEX IDX_CE6064044584665A ON reclamation');
        $this->addSql('DROP INDEX IDX_CE606404A76ED395 ON reclamation');
        $this->addSql('DROP INDEX IDX_CE60640482EA2E54 ON reclamation');
        $this->addSql('ALTER TABLE reclamation ADD categorie VARCHAR(100) NOT NULL, ADD date_traitement DATETIME DEFAULT NULL, ADD nom_utilisateur VARCHAR(255) NOT NULL, ADD email_utilisateur VARCHAR(255) NOT NULL, DROP user_id, DROP commande_id, DROP product_id, CHANGE statut statut VARCHAR(50) NOT NULL, CHANGE sujet titre VARCHAR(255) NOT NULL, CHANGE message description LONGTEXT NOT NULL, CHANGE date date_creation DATETIME NOT NULL');
        $this->addSql('DROP INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 ON messenger_messages');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE commande (id INT AUTO_INCREMENT NOT NULL, date_commande DATETIME NOT NULL, status VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, montant_total DOUBLE PRECISION NOT NULL, user_id INT NOT NULL, INDEX IDX_6EEAA67DA76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE facture (id INT AUTO_INCREMENT NOT NULL, date_facture DATETIME NOT NULL, montant DOUBLE PRECISION NOT NULL, commande_id INT NOT NULL, user_id INT NOT NULL, UNIQUE INDEX UNIQ_FE86641082EA2E54 (commande_id), INDEX IDX_FE866410A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE ligne_commande (id INT AUTO_INCREMENT NOT NULL, quantite INT NOT NULL, prix_unitaire DOUBLE PRECISION NOT NULL, commande_id INT NOT NULL, product_id INT DEFAULT NULL, INDEX IDX_3170B74B82EA2E54 (commande_id), INDEX IDX_3170B74B4584665A (product_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, description VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, price DOUBLE PRECISION NOT NULL, stock INT NOT NULL, reference VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, category_id INT NOT NULL, INDEX IDX_D34A04AD12469DE2 (category_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('DROP INDEX IDX_75EA56E0FB7336F0 ON messenger_messages');
        $this->addSql('DROP INDEX IDX_75EA56E0E3BD61CE ON messenger_messages');
        $this->addSql('DROP INDEX IDX_75EA56E016BA31DB ON messenger_messages');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 ON messenger_messages (queue_name, available_at, delivered_at, id)');
        $this->addSql('ALTER TABLE paiement ADD commande_id INT NOT NULL');
        $this->addSql('ALTER TABLE paiement ADD CONSTRAINT `FK_B1DC7A1E82EA2E54` FOREIGN KEY (commande_id) REFERENCES commande (id)');
        $this->addSql('CREATE INDEX IDX_B1DC7A1E82EA2E54 ON paiement (commande_id)');
        $this->addSql('ALTER TABLE reclamation ADD sujet VARCHAR(255) NOT NULL, ADD user_id INT NOT NULL, ADD commande_id INT DEFAULT NULL, ADD product_id INT DEFAULT NULL, DROP titre, DROP categorie, DROP date_traitement, DROP nom_utilisateur, DROP email_utilisateur, CHANGE statut statut VARCHAR(255) NOT NULL, CHANGE description message LONGTEXT NOT NULL, CHANGE date_creation date DATETIME NOT NULL');
        $this->addSql('ALTER TABLE reclamation ADD CONSTRAINT `FK_CE6064044584665A` FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE reclamation ADD CONSTRAINT `FK_CE60640482EA2E54` FOREIGN KEY (commande_id) REFERENCES commande (id)');
        $this->addSql('ALTER TABLE reclamation ADD CONSTRAINT `FK_CE606404A76ED395` FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_CE6064044584665A ON reclamation (product_id)');
        $this->addSql('CREATE INDEX IDX_CE606404A76ED395 ON reclamation (user_id)');
        $this->addSql('CREATE INDEX IDX_CE60640482EA2E54 ON reclamation (commande_id)');
    }
}
