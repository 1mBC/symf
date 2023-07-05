<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230705153829 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("ALTER TABLE account ADD credit INT NOT NULL DEFAULT 0, ADD subs DATE DEFAULT NULL, ADD picurl VARCHAR(255) DEFAULT NULL, ADD season VARCHAR(255) NOT NULL DEFAULT 'summer', ADD created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE account DROP credit, DROP subs, DROP picurl, DROP season, DROP created');
    }
}
