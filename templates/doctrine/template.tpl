<?php

declare(strict_types=1);

namespace <namespace>;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class <className> extends AbstractMigration
{
    public function down(Schema $schema): void
    {
    <down>
    }

    public function up(Schema $schema): void
    {
    <up>
    }
}