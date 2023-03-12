<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230312111419 extends AbstractMigration
{
    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE users DROP is_verified');
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE users ADD is_verified BOOLEAN NOT NULL');
    }
}