<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230316100257 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE applicant (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) NOT NULL, contact_information VARCHAR(255) NOT NULL, job_preferences VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE applicant_job (applicant_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', job_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_E79CAB6D97139001 (applicant_id), INDEX IDX_E79CAB6DBE04EA9 (job_id), PRIMARY KEY(applicant_id, job_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE company (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, location VARCHAR(255) NOT NULL, contact_information VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE job (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', company_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', title VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, required_skills VARCHAR(255) NOT NULL, experience VARCHAR(255) NOT NULL, INDEX IDX_FBD8E0F8979B1AD6 (company_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE applicant_job ADD CONSTRAINT FK_E79CAB6D97139001 FOREIGN KEY (applicant_id) REFERENCES applicant (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE applicant_job ADD CONSTRAINT FK_E79CAB6DBE04EA9 FOREIGN KEY (job_id) REFERENCES job (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE job ADD CONSTRAINT FK_FBD8E0F8979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE applicant_job DROP FOREIGN KEY FK_E79CAB6D97139001');
        $this->addSql('ALTER TABLE applicant_job DROP FOREIGN KEY FK_E79CAB6DBE04EA9');
        $this->addSql('ALTER TABLE job DROP FOREIGN KEY FK_FBD8E0F8979B1AD6');
        $this->addSql('DROP TABLE applicant');
        $this->addSql('DROP TABLE applicant_job');
        $this->addSql('DROP TABLE company');
        $this->addSql('DROP TABLE job');
    }
}
