<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240213100732 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE meals DROP FOREIGN KEY FK_E229E6EA8BEC7BDE');
        $this->addSql('ALTER TABLE day_meals DROP FOREIGN KEY FK_5BE45A0E912AB082');
        $this->addSql('DROP TABLE day_meals');
        $this->addSql('ALTER TABLE meal_plan ADD day VARCHAR(255) NOT NULL, ADD shopping_list VARCHAR(8126) DEFAULT NULL');
        $this->addSql('DROP INDEX IDX_E229E6EA8BEC7BDE ON meals');
        $this->addSql('ALTER TABLE meals DROP day_meals_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE day_meals (id INT AUTO_INCREMENT NOT NULL, meal_plan_id INT DEFAULT NULL, day_of_week VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_5BE45A0E912AB082 (meal_plan_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE day_meals ADD CONSTRAINT FK_5BE45A0E912AB082 FOREIGN KEY (meal_plan_id) REFERENCES meal_plan (id)');
        $this->addSql('ALTER TABLE meal_plan DROP day, DROP shopping_list');
        $this->addSql('ALTER TABLE meals ADD day_meals_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE meals ADD CONSTRAINT FK_E229E6EA8BEC7BDE FOREIGN KEY (day_meals_id) REFERENCES day_meals (id)');
        $this->addSql('CREATE INDEX IDX_E229E6EA8BEC7BDE ON meals (day_meals_id)');
    }
}
