<?php
namespace Icecube\Customers\Controller\Find;
use Magento\Framework\App\Action\Context; 

class Freeoffer extends \Magento\Framework\App\Action\Action
{
    protected $resultJsonFactory;
    protected $_modelFollowFactory;
    protected $_modelAdoffersFactory;
    public function __construct(
        Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Icecube\Businesses\Model\ResourceModel\Follow\CollectionFactory $modelFollowFactory,
         \Icecube\Businesses\Model\ResourceModel\Adoffers\CollectionFactory $modelAdoffersFactory
    )
    {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_modelFollowFactory = $modelFollowFactory;
        $this->_modelAdoffersFactory = $modelAdoffersFactory; 
        parent::__construct($context);
    }
    public function execute()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();   
        $itemPerPage = 4;
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
                $businessUserFound = TRUE;
                 $customerIds[] = $customer->getId();
            }

        }
        //var_dump($customerIds); die;
        $customerSession = $objectManager->get('Magento\Customer\Model\Session');
        if($customerSession->isLoggedIn()) {
          $customerdata = $customerSession->getCustomer(); 
           $current_customerid = $customerdata->getId(); 
        }
        
        $businessesModel = $objectManager->get('Icecube\Businesses\Model\ResourceModel\Businesses\CollectionFactory')->create();
        $businessesModelTotal = $objectManager->get('Icecube\Businesses\Model\ResourceModel\Businesses\CollectionFactory')->create();
        if($businessUserFound){
            $businessesModel->addFieldToFilter('businessuser_id',array('in' => $customerIds));
            $businessesModelTotal->addFieldToFilter('businessuser_id',array('in' => $customerIds));
        }
        //$businessesModelTotal->load();

        $businessIds = array();
         $adModel = $this->_modelAdoffersFactory->create();
         if($businessUserFound){
             $adModel->addFieldToFilter('businessuser_id',array('in' => $customerIds));
        }
         $aditems = $adModel->load();
         foreach($aditems as $aditem){
             $businessIds[] = $aditem->getBusinessId(); 
        }
        $businessIds = array_unique($businessIds);

        $businessesModel->addFieldToFilter('business_id',array('in' => $businessIds));
        $total_adoffer_busines =  $businessesModelTotal->addFieldToFilter('business_id',array('in' => $businessIds))->load();        
        $items = $businessesModel->setCurPage($currentPage)->setPageSize($itemPerPage)->setOrder('business_id','ASC')->load();
        $business_count = count($total_adoffer_busines); 

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


 $allitems .= '<div class="col-sm-3">
<div class="box-images"><img src="'.$tab_profile.'"></div>
<div class="box-content">
'.$businesstimelineurl.'
<p>'.$description.'</p>
<ul>
'.$follow_url.'
<li><a title="Offer" href="'.$baseUrl.'businesses/'.$busines_url.'/free-offers">Offers</a></li>
</ul>
</div></div>';
}
$business['ajaxitems'] = $allitems;
$business['count'] = $business_count;
    
    $resultJson = $this->resultJsonFactory->create();
    return $resultJson->setData($business);      
    }
    
}
