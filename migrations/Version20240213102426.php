<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240213102426 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE meal_plan DROP day');
        $this->addSql('ALTER TABLE meals ADD day_of_week VARCHAR(255) NOT NULL, ADD second_breakfast LONGTEXT DEFAULT NULL, ADD lunch LONGTEXT DEFAULT NULL, ADD snack LONGTEXT DEFAULT NULL, ADD dinner LONGTEXT DEFAULT NULL, DROP calories, DROP time, DROP meal, CHANGE description breakfast LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE meal_plan ADD day VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE meals ADD description LONGTEXT DEFAULT NULL, ADD calories INT DEFAULT NULL, ADD time TIME DEFAULT NULL, ADD meal VARCHAR(8126) DEFAULT NULL, DROP day_of_week, DROP breakfast, DROP second_breakfast, DROP lunch, DROP snack, DROP dinner');
    }
}
