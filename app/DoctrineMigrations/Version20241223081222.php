<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 * 
 * @author trungnq <trungnq@unitech.vn>
 */
final class Version20241223081222 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add supplier_id column to dtb_product table to link with dtb_supplier table.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            ALTER TABLE dtb_product 
            ADD supplier_id INT DEFAULT NULL COMMENT "Supplier ID";
        ');

        $this->addSql('
            ALTER TABLE dtb_product
            ADD CONSTRAINT FK_DTB_PRODUCT_SUPPLIER_ID FOREIGN KEY (supplier_id) REFERENCES dtb_supplier (id);
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE dtb_product DROP FOREIGN KEY FK_DTB_PRODUCT_SUPPLIER_ID');
        $this->addSql('ALTER TABLE dtb_product DROP COLUMN supplier_id');
    }
}
