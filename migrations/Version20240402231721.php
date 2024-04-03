<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240402231721 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE favourite_meal (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, meal_id INT DEFAULT NULL, meal_name VARCHAR(255) NOT NULL, meal_type VARCHAR(255) NOT NULL, INDEX IDX_3AE4853DA76ED395 (user_id), INDEX IDX_3AE4853D639666D6 (meal_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE meal_plan (id INT AUTO_INCREMENT NOT NULL, userid_id INT NOT NULL, week_id INT NOT NULL, INDEX IDX_C784888958E0A285 (userid_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE meals (id INT AUTO_INCREMENT NOT NULL, meal_plan_id INT DEFAULT NULL, day_of_week VARCHAR(255) NOT NULL, breakfast JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', brunch JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', lunch JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', snack JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', dinner JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', INDEX IDX_E229E6EA912AB082 (meal_plan_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE profile (id INT AUTO_INCREMENT NOT NULL, userid_id INT NOT NULL, weight DOUBLE PRECISION NOT NULL, height DOUBLE PRECISION NOT NULL, age INT NOT NULL, gender VARCHAR(255) NOT NULL, activitylevel VARCHAR(255) NOT NULL, dietpreferences VARCHAR(255) DEFAULT NULL, allergies VARCHAR(255) DEFAULT NULL, caloricdemand DOUBLE PRECISION DEFAULT NULL, UNIQUE INDEX UNIQ_8157AA0F58E0A285 (userid_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE shopping_list (id INT AUTO_INCREMENT NOT NULL, meal_plan_id INT DEFAULT NULL, shopping_list VARCHAR(2048) DEFAULT NULL, UNIQUE INDEX UNIQ_3DC1A459912AB082 (meal_plan_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, second_password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE favourite_meal ADD CONSTRAINT FK_3AE4853DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE favourite_meal ADD CONSTRAINT FK_3AE4853D639666D6 FOREIGN KEY (meal_id) REFERENCES meals (id)');
        $this->addSql('ALTER TABLE meal_plan ADD CONSTRAINT FK_C784888958E0A285 FOREIGN KEY (userid_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE meals ADD CONSTRAINT FK_E229E6EA912AB082 FOREIGN KEY (meal_plan_id) REFERENCES meal_plan (id)');
        $this->addSql('ALTER TABLE profile ADD CONSTRAINT FK_8157AA0F58E0A285 FOREIGN KEY (userid_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE shopping_list ADD CONSTRAINT FK_3DC1A459912AB082 FOREIGN KEY (meal_plan_id) REFERENCES meal_plan (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE favourite_meal DROP FOREIGN KEY FK_3AE4853DA76ED395');
        $this->addSql('ALTER TABLE favourite_meal DROP FOREIGN KEY FK_3AE4853D639666D6');
        $this->addSql('ALTER TABLE meal_plan DROP FOREIGN KEY FK_C784888958E0A285');
        $this->addSql('ALTER TABLE meals DROP FOREIGN KEY FK_E229E6EA912AB082');
        $this->addSql('ALTER TABLE profile DROP FOREIGN KEY FK_8157AA0F58E0A285');
        $this->addSql('ALTER TABLE shopping_list DROP FOREIGN KEY FK_3DC1A459912AB082');
        $this->addSql('DROP TABLE favourite_meal');
        $this->addSql('DROP TABLE meal_plan');
        $this->addSql('DROP TABLE meals');
        $this->addSql('DROP TABLE profile');
        $this->addSql('DROP TABLE shopping_list');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
