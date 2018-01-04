<?php
 
namespace Ironedge\TradePass\Block\Adminhtml;
 
use Magento\Framework\DataObject;
 
class Products extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    protected $customer;
	public function __construct(
        \Magento\Customer\Model\Customer $customer
    ) {
        $this->customer = $customer;
    }
    public function render(DataObject $row)
    {
		$customerID = $row->getCustomerId();
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 
		 
		 $quote = $objectManager->create('Magento\Quote\Model\Quote')->loadByCustomer($customerID); 
		 $quoteItems=$quote->getAllVisibleItems();
		 $allItems = '';
		 $count = 1;
		 foreach($quoteItems as $oneItem){
		 	//$allItems .= '<b>'.$oneItem->getSku().'</b> - '.$oneItem->getName().'<br/>';
		 	$allItems .= '<b>'.$oneItem->getSku().'</b> - '.$oneItem->getName().'<br/>';
		 }
		 return $allItems;
    }
}