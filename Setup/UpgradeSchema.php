<?php

namespace MageSuite\Frontend\Setup;

class UpgradeSchema implements \Magento\Framework\Setup\UpgradeSchemaInterface
{
    public function upgrade(\Magento\Framework\Setup\SchemaSetupInterface $setup, \Magento\Framework\Setup\ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '0.0.5', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('cms_page'),
                'multistore_identifier',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'comment' => 'Identifier key for multistore'
                ]
            );
        }

        if (version_compare($context->getVersion(), '0.0.6', '<')) {
            $setup->getConnection()->changeColumn(
                $setup->getTable('cms_page'),
                'multistore_identifier',
                'page_group_identifier',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'comment' => 'Page Group Identifier key for multistore'
                ]
            );
        }

        $setup->endSetup();
    }
}