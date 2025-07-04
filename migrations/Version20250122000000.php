<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration initiale - État actuel de la base de données
 */
final class Version20250122000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Migration initiale - Création de toutes les tables';
    }

    public function up(Schema $schema): void
    {
        // Cette migration représente l'état actuel de la base
        // Toutes les tables ont déjà été créées via doctrine:schema:create
        $this->addSql('SELECT 1'); // Commande neutre
    }

    public function down(Schema $schema): void
    {
        // Suppression de toutes les tables
        $this->addSql('DROP TABLE IF EXISTS rencontre');
        $this->addSql('DROP TABLE IF EXISTS licencie');
        $this->addSql('DROP TABLE IF EXISTS avis');
        $this->addSql('DROP TABLE IF EXISTS article');
        $this->addSql('DROP TABLE IF EXISTS user');
        $this->addSql('DROP TABLE IF EXISTS messenger_messages');
    }
} 