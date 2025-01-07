<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250107040901 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE plg_customer_coupon_order (coupon_order_id SERIAL NOT NULL, coupon_id INT NOT NULL, coupon_cd VARCHAR(20) DEFAULT NULL, coupon_name VARCHAR(50) DEFAULT NULL, customer_id INT DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, order_id INT NOT NULL, discount NUMERIC(12, 2) DEFAULT \'0\' NOT NULL, available_from_date TIMESTAMP(0) WITH TIME ZONE NOT NULL, available_to_date TIMESTAMP(0) WITH TIME ZONE NOT NULL, date_of_use TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, visible BOOLEAN DEFAULT true NOT NULL, create_date TIMESTAMP(0) WITH TIME ZONE NOT NULL, update_date TIMESTAMP(0) WITH TIME ZONE NOT NULL, discriminator_type VARCHAR(255) NOT NULL, PRIMARY KEY(coupon_order_id))');
        $this->addSql('COMMENT ON COLUMN plg_customer_coupon_order.available_from_date IS \'(DC2Type:datetimetz)\'');
        $this->addSql('COMMENT ON COLUMN plg_customer_coupon_order.available_to_date IS \'(DC2Type:datetimetz)\'');
        $this->addSql('COMMENT ON COLUMN plg_customer_coupon_order.date_of_use IS \'(DC2Type:datetimetz)\'');
        $this->addSql('COMMENT ON COLUMN plg_customer_coupon_order.create_date IS \'(DC2Type:datetimetz)\'');
        $this->addSql('COMMENT ON COLUMN plg_customer_coupon_order.update_date IS \'(DC2Type:datetimetz)\'');
        $this->addSql('ALTER TABLE plg_customer_coupon DROP available_from_date');
        $this->addSql('ALTER TABLE plg_customer_coupon DROP available_to_date');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE plg_customer_coupon_order');
        $this->addSql('ALTER TABLE plg_customer_coupon ADD available_from_date TIMESTAMP(0) WITH TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE plg_customer_coupon ADD available_to_date TIMESTAMP(0) WITH TIME ZONE NOT NULL');
        $this->addSql('COMMENT ON COLUMN plg_customer_coupon.available_from_date IS \'(DC2Type:datetimetz)\'');
        $this->addSql('COMMENT ON COLUMN plg_customer_coupon.available_to_date IS \'(DC2Type:datetimetz)\'');
    }
}
