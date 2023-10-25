<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231024181550 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE exam (id INT AUTO_INCREMENT NOT NULL, pupil_id INT DEFAULT NULL, subject_id INT DEFAULT NULL, exam_number INT NOT NULL, exam_points INT NOT NULL, INDEX IDX_38BBA6C6D2FD11 (pupil_id), INDEX IDX_38BBA6C623EDC87 (subject_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE exam ADD CONSTRAINT FK_38BBA6C6D2FD11 FOREIGN KEY (pupil_id) REFERENCES pupil (id)');
        $this->addSql('ALTER TABLE exam ADD CONSTRAINT FK_38BBA6C623EDC87 FOREIGN KEY (subject_id) REFERENCES exam_subject (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE exam DROP FOREIGN KEY FK_38BBA6C6D2FD11');
        $this->addSql('ALTER TABLE exam DROP FOREIGN KEY FK_38BBA6C623EDC87');
        $this->addSql('DROP TABLE exam');
    }
}
