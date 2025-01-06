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
final class Version20241223080220 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add data supplier table to store product supplier information.';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable('dtb_supplier');
        $table->addColumn('id', 'integer', ['autoincrement' => true, 'comment' => 'Supplier ID']);
        $table->addColumn('name', 'string', ['length' => 255, 'notnull' => true, 'comment' => 'Supplier Name']);
        $table->addColumn('email', 'string', ['length' => 255, 'notnull' => false, 'comment' => 'Contact Email']);
        $table->addColumn('phone', 'string', ['length' => 14, 'notnull' => false, 'comment' => 'Phone Number']);
        $table->addColumn('address', 'text', ['notnull' => false, 'comment' => 'Address']);
        $table->addColumn('created_date', 'datetime', ['notnull' => true, 'comment' => 'Date Created']);
        $table->addColumn('updated_date', 'datetime', ['notnull' => false, 'comment' => 'Date Updated']);
        $table->setPrimaryKey(['id']);
        $table->addOption('comment', 'Supplier Information');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE dtb_supplier');
    }
}
