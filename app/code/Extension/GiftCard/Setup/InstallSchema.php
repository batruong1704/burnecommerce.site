<?php

namespace Extension\GiftCard\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (!$setup->tableExists('giftcard_code')) {
            $table = $setup->getConnection()->newTable(
                $setup->getTable('giftcard_code')
            )->addColumn(
                'giftcard_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'nullable' => false,
                    'primary' => true,
                    'unsigned' => true
                ], 'GiftCard ID'
            )->addColumn(
                'code',
                Table::TYPE_TEXT,
                255,
                [
                    'nullable' => false,
                ], 'GiftCard code'
            )->addColumn(
                'balance',
                Table::TYPE_DECIMAL,
                null,
                [
                    'nullable' => false,
                    'length' => '12,4',
                    'default' => 0
                ], 'GiftCard balance'
            )->addColumn(
                'amount_used',
                Table::TYPE_DECIMAL,
                null,
                [
                    'nullable' => true,
                    'length' => '12,4',
                    'default' => 0
                ], 'GiftCard amount used'
            )->addColumn(
                'created_from',
                Table::TYPE_TEXT,
                255,
                [
                    'nullable' => false,
                    'default' => 'admin'
                ]
            )->addColumn(
                'created_at',
                Table::TYPE_TIMESTAMP,
                null,
                [
                    'nullable' => false,
                    'default' => Table::TIMESTAMP_INIT
                ]
            )->setComment('GiftCard Table');
            $setup->getConnection()->createTable($table);
        }

        $setup->endSetup();
    }
}

