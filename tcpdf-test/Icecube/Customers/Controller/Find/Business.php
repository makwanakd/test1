<?php
namespace Icecube\Customers\Controller\Find;
use Magento\Framework\App\Action\Context; 

class Business extends \Magento\Framework\App\Action\Action
{
    protected $resultJsonFactory;
    protected $_modelFollowFactory;
    public function __construct(
        Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Icecube\Businesses\Model\ResourceModel\Follow\CollectionFactory $modelFollowFactory
    )
    {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_modelFollowFactory = $modelFollowFactory; 
        parent::__construct($context);
    }
    public function execute()
    {

    	$objectManager = \Magento\Framework\App\ObjectManager::getInstance();	
    	$itemPerPage = 3;
        $followedId = '';
        $current_customerid = '';
    	$request = $this->getRequest();
    	$businessCat = $request->getParam("find_business_prefer");
    	$currentPage = $request->getParam("find_page_id");
        $mediaUrl = $objectManager->get('Magento\Store\Model\StoreManagerInterface')
                    ->getStore()
                    ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $baseUrl = $objectManager->get('Magento\Store\Model\StoreManagerInterface')
        ->getStore()
        ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
        $businessUserFound = FALSE;
 		if($businessCat!=NULL && $businessCat!=''){
			$customerObj = $objectManager->create('Magento\Customer\Model\ResourceModel\Customer\Collection');
	    	$customerCollection = $customerObj->addAttributeToSelect('*')->addAttributeToFilter('business_category',$businessCat)->load();
	    	$customerIds = array();
            $businessUserFound = TRUE;
	    	foreach($customerCollection as $customer){
	    		
				 $customerIds[] = $customer->getId();
			}

		}
        $customerSession = $objectManager->get('Magento\Customer\Model\Session');
        if($customerSession->isLoggedIn()) {
          $customerdata = $customerSession->getCustomer(); 
           $current_customerid = $customerdata->getId(); 
        }
        
 		//$customerSession = $om->get('Magento\Customer\Model\Session');
 		$businessesModel = $objectManager->get('Icecube\Businesses\Model\ResourceModel\Businesses\CollectionFactory')->create();
 		$businessesModelTotal = $objectManager->get('Icecube\Businesses\Model\ResourceModel\Businesses\CollectionFactory')->create();
        if($businessUserFound){
			$businessesModel->addFieldToFilter('businessuser_id',array('in' => $customerIds));
            $businessesModelTotal->addFieldToFilter('businessuser_id',array('in' => $customerIds));
		}
        $business_count = count($businessesModelTotal->load());
		$items = $businessesModel->setCurPage($currentPage)->setPageSize($itemPerPage)->setOrder('business_id','ASC')->load();
        //var_dump($items->getData()); die;
        $allitems = '';
        foreach($items as $item){ 
            $customerObj = $objectManager->create('Magento\Customer\Model\Customer');
            $businessuser_id = $item['businessuser_id'];
            $customerObj->load($businessuser_id);
            $businessTimeline = $objectManager->create('Icecube\Businesses\Model\Index');
            $busines_id = $item['business_id'];
            $businessTimeline->load($busines_id, 'business_id');
            $busines_url = $item['business_page_url'];
            //$followObj = $objectManager->create('Icecube\Businesses\Model\Follow');
            //echo $current_customerid; echo $busines_id; 
            //$follow_id = $followObj->getCheckFollowBusiness($current_customerid, $busines_id); 
           
            $followModel = $this->_modelFollowFactory->create();
            $items = $followModel->addFieldToFilter('business_id',$busines_id)->addFieldToFilter('customer_id',$current_customerid)->load();
            foreach($items as $item){
              $followedId = $item->getId(); 
            }
            if($followedId!='' && $followedId!='0'){

                $follow_url = '<li><a class="follow-business-link" title="Unfollow"
                 href="'.$baseUrl.'businesses/'.$busines_url.'/about/unfollow/">Unfollow</a></li>';

            }else{
                $follow_url = '<li><a class="follow-business-link" title="Follow"
                 href="'.$baseUrl.'businesses/'.$busines_url.'/about/follow/">Follow</a></li>';
            }
            $businesstimelineurl =  '<a href="javascript:void(0);" 
onclick="ViewBusiness('."'".''.$baseUrl.'businesses/'.$busines_url."'".','."'".$busines_url."'".')"><h3>'.$customerObj->getBusinessName().'</h3></a>';

            $length = strlen($businessTimeline->getCompanyDescription()); 
            if($length>40){
            $description = substr($businessTimeline->getCompanyDescription(),0,40).'...';
            }else{
            $description = $businessTimeline->getCompanyDescription();
            }


            $tab_profile = $mediaUrl.'wysiwyg/user-default.jpg';
            if($businessuser_id){
                $isimageAvailable = $customerObj->getProfileImage();
                if($isimageAvailable!=NULL && $isimageAvailable!=''){
                    $tab_profile = $mediaUrl.$isimageAvailable;         
                }else{
                    $tab_profile = $mediaUrl.'wysiwyg/user-default.jpg';
                }
            }
?>


<?php  


 $allitems .= '<div class="col-sm-4">
<div class="box-images"><img src="'.$tab_profile.'"></div>
'.$businesstimelineurl.'
<div class="business-desc">'.$description.'</div>
<ul>
'.$follow_url.'
<li><a title="Offer" href="'.$baseUrl.'businesses/'.$busines_url.'/free-offers">Offers</a></li>
</ul>
</div>';
}
$business['ajaxitems'] = $allitems;
$business['count'] = $business_count;
    
    $resultJson = $this->resultJsonFactory->create();
    return $resultJson->setData($business);      
    }
    
}