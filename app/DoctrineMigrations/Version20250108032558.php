<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250108032558 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE plg_customer_coupon ADD coupon_release INT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE plg_customer_coupon ADD coupon_use_time INT DEFAULT NULL');
        $this->addSql('ALTER TABLE plg_customer_coupon ADD enable_flag BOOLEAN DEFAULT true NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE plg_customer_coupon DROP coupon_release');
        $this->addSql('ALTER TABLE plg_customer_coupon DROP coupon_use_time');
        $this->addSql('ALTER TABLE plg_customer_coupon DROP enable_flag');
    }
}
