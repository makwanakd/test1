<?php
namespace Icecube\Profile\Controller\Settings;
use Magento\Framework\App\Action\Context;
use Magento\Checkout\Model\Cart as CustomerCart;
class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $cart;

    /**
     * @param Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param CustomerCart $cart
     */
    public function __construct(
        Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        CustomerCart $cart,
        \Magento\Sales\Model\Order\AddressRepository $repositoryAddress
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->cart = $cart;
        $this->repositoryAddress = $repositoryAddress;
        parent::__construct($context);
    }

    public function execute()
    {
    	$om = \Magento\Framework\App\ObjectManager::getInstance(); 
 		$customerSession = $om->get('Magento\Customer\Model\Session');
        $baseUrl = $om->get('Magento\Store\Model\StoreManagerInterface')
                    ->getStore()
                    ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB); 
	    if($customerSession->isLoggedIn()) {

	    	$checklogin = $customerSession->getFirstLogin();
            if($checklogin == "firsttimelogin"){

            }else{
                $customerSession->setFirstLogin('firsttimelogin');
                $this->deleteQuoteItems();
            }

	            $id = $customerSession->getCustomer()->getId(); 
	            $customerGroupid = $customerSession->getCustomer()->getGroupId(); 
	            if($customerGroupid == 4){
	               	$this->_view->loadLayout();
			        $this->_view->getLayout()->initMessages();
			        $this->_view->renderLayout();
	            }
	            else{
	                header('Location: '.$baseUrl.'customers/settings/');
	               exit();
	            }
	     }else{
	     	header('Location: '.$baseUrl.'/login/');
	        exit();

	     }
        
    }
    public function deleteQuoteItems(){
        $checkoutSession = $this->getCheckoutSession();
        $allItems = $checkoutSession->getQuote()->getAllVisibleItems();//returns all teh items in session
        foreach ($allItems as $item) {
            $itemId = $item->getItemId();//item id of particular item
           // $quoteItem=$this->getItemModel()->load($itemId);//load particular item which you want to delete by his item id
            //$quoteItem->delete();//deletes the item
            //$this->cart->removeItem($itemId)->save();
            $this->cart->truncate()->save();
        }
    }
    public function getCheckoutSession(){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();//instance of object manager 
        $checkoutSession = $objectManager->get('Magento\Checkout\Model\Session');//checkout session
        return $checkoutSession;
    }
     
    public function getItemModel(){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();//instance of object manager
        $itemModel = $objectManager->create('Magento\Quote\Model\Quote\Item');//Quote item model to load quote item
        return $itemModel;
    }
    
}