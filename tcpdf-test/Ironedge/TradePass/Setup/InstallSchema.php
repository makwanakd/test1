<?php
/**
 * Webkul Grid Schema Setup.
 * @category    Webkul
 * @package     Webkul_Grid
 * @author      Webkul Software Private Limited
 */
namespace Ironedge\TradePass\Setup;
 
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
 
/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        /*
         * Create table 'ironedge_tradepass'
         */ 
        $table = $installer->getConnection()->newTable(
            $installer->getTable('ironedge_tradepass')
        )->addColumn(
            'tradepass_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Trade Pass Id'
        )->addColumn(
            'customer_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false,'unsigned' => true],
            'Customer Id'
        )->addColumn(
            'company',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '255',
            ['nullable' => false],
            'Company'
        )->addColumn(
            'address',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '255',
            ['nullable' => false],
            'Address'
        )->addColumn(
            'industry',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            'Industry'
        )->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'Creation Time'
        )->addColumn(
            'update_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'Modification Time'
        )->setComment(
            'Trade Pass Records'
        )->addForeignKey(
        $installer->getFkName(
            'ironedge_tradepass',
            'customer_id',
            'customer_entity',
            'entity_id'
        ),
        'customer_id', $installer->getTable('customer_entity'), 'entity_id',
        \Magento\Framework\Db\Ddl\Table::ACTION_CASCADE);
 
        $installer->getConnection()->createTable($table);
        $installer->endSetup();
    }
}
