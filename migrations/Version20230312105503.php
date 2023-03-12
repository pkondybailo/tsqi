<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230312105503 extends AbstractMigration
{
    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE users_id_seq CASCADE');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE messenger_messages');
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE users_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql(
            <<<'SQL'
            CREATE TABLE users (
                id         INT          NOT NULL,
                email      VARCHAR(180) NOT NULL,
                first_name VARCHAR(255) NOT NULL,
                last_name  VARCHAR(255) NOT NULL,
                password   VARCHAR(255) NOT NULL,
                roles      JSON         NOT NULL,
                sex        VARCHAR(255) NOT NULL,
                PRIMARY KEY (id)
            )
            SQL
        );
        $this->addSql('CREATE UNIQUE INDEX uniq_1483a5e9e7927c74 ON users (email)');
        $this->addSql(
            <<<'SQL'
            CREATE TABLE messenger_messages (
                id           BIGSERIAL    NOT NULL,
                body         TEXT         NOT NULL,
                headers      TEXT         NOT NULL,
                queue_name   VARCHAR(190) NOT NULL,
                created_at   TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
                PRIMARY KEY (id)
            )
            SQL
        );
        $this->addSql('CREATE INDEX idx_75ea56e0fb7336f0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX idx_75ea56e0e3bd61ce ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX idx_75ea56e016ba31db ON messenger_messages (delivered_at)');
        $this->addSql(
            <<<'SQL'
            CREATE
            OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                                        PERFORM
            pg_notify('messenger_messages', NEW.queue_name::text);
            RETURN new;
            END;
                                $$
            LANGUAGE plpgsql;
            SQL
        );
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql(
            <<<'SQL'
            CREATE TRIGGER notify_trigger
                AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW
            EXECUTE PROCEDURE notify_messenger_messages();
            SQL
        );
    }
}
