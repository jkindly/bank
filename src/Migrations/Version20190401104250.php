<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190401104250 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user_address_settings (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, old_street VARCHAR(255) NOT NULL, old_zipcode VARCHAR(6) NOT NULL, old_city VARCHAR(255) NOT NULL, old_country VARCHAR(255) NOT NULL, new_street VARCHAR(255) NOT NULL, new_zipcode VARCHAR(6) NOT NULL, new_city VARCHAR(255) NOT NULL, new_country VARCHAR(255) NOT NULL, is_success TINYINT(1) NOT NULL, hash VARCHAR(255) NOT NULL, INDEX IDX_66DF1C99A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_address_settings ADD CONSTRAINT FK_66DF1C99A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD verification_code INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE user_address_settings');
        $this->addSql('ALTER TABLE user DROP verification_code');
    }
}
