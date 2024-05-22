<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240522085939 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lecon DROP FOREIGN KEY FK_94E6242E62BB7AEE');
        $this->addSql('DROP INDEX IDX_94E6242E62BB7AEE ON lecon');
        $this->addSql('ALTER TABLE lecon DROP programme_id');
        $this->addSql('ALTER TABLE programme ADD session_id INT DEFAULT NULL, ADD lecon_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE programme ADD CONSTRAINT FK_3DDCB9FF613FECDF FOREIGN KEY (session_id) REFERENCES session (id)');
        $this->addSql('ALTER TABLE programme ADD CONSTRAINT FK_3DDCB9FFEC1308A5 FOREIGN KEY (lecon_id) REFERENCES lecon (id)');
        $this->addSql('CREATE INDEX IDX_3DDCB9FF613FECDF ON programme (session_id)');
        $this->addSql('CREATE INDEX IDX_3DDCB9FFEC1308A5 ON programme (lecon_id)');
        $this->addSql('ALTER TABLE session DROP FOREIGN KEY FK_D044D5D462BB7AEE');
        $this->addSql('DROP INDEX IDX_D044D5D462BB7AEE ON session');
        $this->addSql('ALTER TABLE session DROP programme_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lecon ADD programme_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE lecon ADD CONSTRAINT FK_94E6242E62BB7AEE FOREIGN KEY (programme_id) REFERENCES programme (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_94E6242E62BB7AEE ON lecon (programme_id)');
        $this->addSql('ALTER TABLE session ADD programme_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE session ADD CONSTRAINT FK_D044D5D462BB7AEE FOREIGN KEY (programme_id) REFERENCES programme (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_D044D5D462BB7AEE ON session (programme_id)');
        $this->addSql('ALTER TABLE programme DROP FOREIGN KEY FK_3DDCB9FF613FECDF');
        $this->addSql('ALTER TABLE programme DROP FOREIGN KEY FK_3DDCB9FFEC1308A5');
        $this->addSql('DROP INDEX IDX_3DDCB9FF613FECDF ON programme');
        $this->addSql('DROP INDEX IDX_3DDCB9FFEC1308A5 ON programme');
        $this->addSql('ALTER TABLE programme DROP session_id, DROP lecon_id');
    }
}
