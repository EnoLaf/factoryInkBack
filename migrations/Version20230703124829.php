<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230703124829 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tattoo ADD artist_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tattoo ADD CONSTRAINT FK_DEE4C6FB7970CF8 FOREIGN KEY (artist_id) REFERENCES artist (id)');
        $this->addSql('CREATE INDEX IDX_DEE4C6FB7970CF8 ON tattoo (artist_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tattoo DROP FOREIGN KEY FK_DEE4C6FB7970CF8');
        $this->addSql('DROP INDEX IDX_DEE4C6FB7970CF8 ON tattoo');
        $this->addSql('ALTER TABLE tattoo DROP artist_id');
    }
}
