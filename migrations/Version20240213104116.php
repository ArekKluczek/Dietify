<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240213104116 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE meal_plan DROP shopping_list');
        $this->addSql('ALTER TABLE meals CHANGE breakfast breakfast JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', CHANGE second_breakfast second_breakfast JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', CHANGE lunch lunch JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', CHANGE snack snack JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', CHANGE dinner dinner JSON DEFAULT NULL COMMENT \'(DC2Type:json)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE meal_plan ADD shopping_list VARCHAR(8126) DEFAULT NULL');
        $this->addSql('ALTER TABLE meals CHANGE breakfast breakfast LONGTEXT DEFAULT NULL, CHANGE second_breakfast second_breakfast LONGTEXT DEFAULT NULL, CHANGE lunch lunch LONGTEXT DEFAULT NULL, CHANGE snack snack LONGTEXT DEFAULT NULL, CHANGE dinner dinner LONGTEXT DEFAULT NULL');
    }
}
