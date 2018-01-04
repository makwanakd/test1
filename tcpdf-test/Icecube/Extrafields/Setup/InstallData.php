<?php
namespace Icecube\Extrafields\Setup;

use Magento\Framework\Module\Setup\Migration;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    /**
     * Customer setup factory
     *
     * @var \Magento\Customer\Setup\CustomerSetupFactory
     */
    private $customerSetupFactory;
    /**
     * Init
     *
     * @param \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory
     */
    public function __construct(\Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory)
    {
        $this->customerSetupFactory = $customerSetupFactory;
    }
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $installer->endSetup();
         /*$installer = $setup;
        $installer->startSetup();
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
        $entityTypeId = $customerSetup->getEntityTypeId(\Magento\Customer\Model\Customer::ENTITY);

    $customerSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, "business_phone_number_checkout",  array(
        "type"     => "varchar",
        "backend"  => "",
        "label"    => "Business Phonenumber",
        "input"    => "text",
        "source"   => '',
        "visible"  => true,
        "required" => false,
        "default" => "",
        "frontend" => "",
        "unique"     => false,
        "note"       => "",
        "position" => 998,
        "is_used_in_grid" => false,
        "is_visible_in_grid" => false,
        "is_filterable_in_grid" => false,
        "is_searchable_in_grid" => false

    ));

    $regulation   = $customerSetup->getAttribute(\Magento\Customer\Model\Customer::ENTITY, "business_phone_number_checkout");

    $regulation = $customerSetup->getEavConfig()->getAttribute(\Magento\Customer\Model\Customer::ENTITY, 'business_phone_number_checkout');
    $used_in_forms[]="customer_account_edit";
    $used_in_forms[] = "adminhtml_customer";
    $regulation->setData("used_in_forms", $used_in_forms)
        ->setData("is_used_for_customer_segment", true)
        ->setData("is_system", 0)
        ->setData("is_user_defined", 1)
        ->setData("is_visible", 1)
        ->setData("sort_order", 100);
    $regulation->save();

    $installer->endSetup();*/
    }
}