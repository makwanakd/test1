<?php
 
namespace Ironedge\TradePass\Block\Adminhtml;
 
use Magento\Framework\DataObject;
 
class Telephone extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $customer;
    /**
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     */
    public function __construct(
        \Magento\Customer\Model\Customer $customer
    ) {
        $this->customer = $customer;
    }
 
    /**
     * get category name
     * @param  DataObject $row
     * @return string
     */
    public function render(DataObject $row)
    {
		$customerID = $row->getCustomerId();
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$customerObj = $objectManager->create('Magento\Customer\Model\Customer')->load($customerID);
		if($customerObj->getPrimaryBillingAddress()){
			return $customerObj->getPrimaryBillingAddress()->getTelephone();
		}else{
			return '';
		}
		

        //$mageCateId = $row->getMageCatId();
        //$storeCat = $this->categoryFactory->create()->load($mageCateId);
        //return $storeCat->getName();
    }
}