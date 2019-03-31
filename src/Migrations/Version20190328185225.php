<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190328185225 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user ADD zipcode VARCHAR(6) NOT NULL, ADD street_permanent VARCHAR(255) NOT NULL, ADD city_permanent VARCHAR(255) NOT NULL, ADD zipcode_permanent VARCHAR(6) NOT NULL, ADD country VARCHAR(255) NOT NULL, ADD country_permanent VARCHAR(255) NOT NULL, CHANGE address street VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user ADD address VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, DROP street, DROP zipcode, DROP street_permanent, DROP city_permanent, DROP zipcode_permanent, DROP country, DROP country_permanent');
    }
}
