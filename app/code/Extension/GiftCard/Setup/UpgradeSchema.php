<?php

namespace Extension\GiftCard\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '2.0.0', '<')) {
            $table = $setup->getConnection()->newTable(
                $setup->getTable('giftcard_history')
            )->addColumn(
                'history_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'nullable' => false,
                    'primary' => true,
                    'unsigned' => true
                ], 'GiftCard History ID'
            )->addColumn(
                'amount',
                Table::TYPE_DECIMAL,
                null,
                [
                    'nullable' => false,
                    'length' => '12,4',
                    'default' => 0
                ], 'balance change'
            )->addColumn(
                'action',
                Table::TYPE_TEXT,
                255,
                [
                    'nullable' => false,
                ], 'Action: create/redeem/used for order'
            )->addColumn(
                'action_time',
                Table::TYPE_TIMESTAMP,
                null,
                [
                    'nullable' => false,
                    'default' => Table::TIMESTAMP_INIT
                ],
                'Time that the action is made'
            )->addColumn(
                'giftcard_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'nullable' => false,
                    'unsigned' => true,
                    'index' => true
                ], 'GiftCard ID'
            )->addColumn(
                'customer_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'nullable' => true,
                    'unsigned' => true,
                    'index' => true
                ], 'Customer ID'
            )->addForeignKey(
                $setup->getFkName(
                    'giftcard_history',
                    'giftcard_id',
                    'giftcard_code',
                    'giftcard_id'),
                'giftcard_id',
                $setup->getTable('giftcard_code'),
                'giftcard_id',
                Table::ACTION_CASCADE
            )->addForeignKey(
                $setup->getFkName('giftcard_history',
                    'customer_id',
                    'customer_entity',
                    'entity_id'),
                'customer_id',
                $setup->getTable('customer_entity'),
                'entity_id',
                Table::ACTION_CASCADE
            )->setComment('GiftCard History Table');

            $setup->getConnection()->createTable($table);
        }

        if (version_compare($context->getVersion(), '2.0.0', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('customer_entity'),
                'giftcard_balance',
                [
                    'size' => null,
                    'type' => Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'default' => 0,
                    'comment' => 'Gift Card Balance',
                ]
            );
        }

        $setup->getConnection()->addColumn(
            $setup->getTable('quote'),
            'gift_code',
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'Gift Code of Gift Card'
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable('quote'),
            'gift_code_discount',
            [
                'type' => Table::TYPE_DECIMAL,
                'nullable' => true,
                'default' => 0,
                'comment' => 'Gift Code discount amount'
            ]
        );

        $setup->endSetup();
    }
}
