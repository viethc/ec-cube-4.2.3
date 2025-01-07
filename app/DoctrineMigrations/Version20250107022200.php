<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250107022200 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dtb_delivery_fee DROP CONSTRAINT fk_4915524e171ef5f');
        $this->addSql('ALTER TABLE dtb_order DROP CONSTRAINT fk_1d66d807e171ef5f');
        $this->addSql('ALTER TABLE dtb_customer DROP CONSTRAINT fk_8298bbe3e171ef5f');
        $this->addSql('ALTER TABLE dtb_base_info DROP CONSTRAINT fk_1d3655f4e171ef5f');
        $this->addSql('ALTER TABLE dtb_regional_discount DROP CONSTRAINT fk_5ffec1abe171ef5f');
        $this->addSql('ALTER TABLE dtb_shipping DROP CONSTRAINT fk_2ebd22cee171ef5f');
        $this->addSql('ALTER TABLE dtb_tax_rule DROP CONSTRAINT fk_59f696dee171ef5f');
        $this->addSql('ALTER TABLE dtb_customer_address DROP CONSTRAINT fk_6c38c0f8e171ef5f');
        $this->addSql('CREATE TABLE plg_customer_coupon (coupon_id SERIAL NOT NULL, coupon_cd VARCHAR(20) DEFAULT NULL, coupon_name VARCHAR(50) DEFAULT NULL, discount_rate NUMERIC(10, 0) DEFAULT \'0\', available_from_date TIMESTAMP(0) WITH TIME ZONE NOT NULL, available_to_date TIMESTAMP(0) WITH TIME ZONE NOT NULL, visible BOOLEAN DEFAULT true NOT NULL, coupon_lower_limit NUMERIC(12, 2) DEFAULT \'0\', create_date TIMESTAMP(0) WITH TIME ZONE NOT NULL, update_date TIMESTAMP(0) WITH TIME ZONE NOT NULL, discriminator_type VARCHAR(255) NOT NULL, PRIMARY KEY(coupon_id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4E99BDAC9C2A7D91 ON plg_customer_coupon (coupon_cd)');
        $this->addSql('COMMENT ON COLUMN plg_customer_coupon.available_from_date IS \'(DC2Type:datetimetz)\'');
        $this->addSql('COMMENT ON COLUMN plg_customer_coupon.available_to_date IS \'(DC2Type:datetimetz)\'');
        $this->addSql('COMMENT ON COLUMN plg_customer_coupon.create_date IS \'(DC2Type:datetimetz)\'');
        $this->addSql('COMMENT ON COLUMN plg_customer_coupon.update_date IS \'(DC2Type:datetimetz)\'');
        $this->addSql('DROP TABLE mtb_pref');
        $this->addSql('ALTER TABLE dtb_base_info DROP site_kit_site_id');
        $this->addSql('ALTER TABLE dtb_base_info DROP site_kit_site_secret');
        $this->addSql('ALTER TABLE dtb_customer DROP plg_mailmagazine_flg');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE TABLE mtb_pref (id SMALLINT NOT NULL, name VARCHAR(255) NOT NULL, sort_no SMALLINT NOT NULL, discriminator_type VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('DROP TABLE plg_customer_coupon');
        $this->addSql('ALTER TABLE dtb_customer ADD plg_mailmagazine_flg SMALLINT DEFAULT 0');
        $this->addSql('ALTER TABLE dtb_base_info ADD site_kit_site_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE dtb_base_info ADD site_kit_site_secret VARCHAR(255) DEFAULT NULL');
    }
}
