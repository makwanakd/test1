<?php
namespace Icecube\Businesses\Block\Frontend;

class Index extends \Magento\Framework\View\Element\Template
{
    /*protected $_storeManager;*/
   /* protected $_urlInterface;*/
 	protected $_modelIndexFactory;
 	protected $currentCustomer;
 	protected $objectmanager;
 	protected $_modelFollowFactory;
 	protected $_modelReviewsFactory;
 	protected $_modelCommentsFactory;
 	protected $_modelAdoffersFactory;
 	protected $_orderCollectionFactory;
 	public $_packageManager;
 	public $_businessFactory;
 	protected $addressCollectionFactory;
 	
 	private $_itemPerPage = 10;
    private $_pageFrame = 4;
    private $_curPage = 1;
 	
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,        
        /*\Magento\Store\Model\StoreManagerInterface $storeManager,*/
        /*\Magento\Framework\UrlInterface $urlInterface,*/
        \Icecube\Businesses\Model\ResourceModel\Index\CollectionFactory $modelIndexFactory,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \Magento\Framework\ObjectManagerInterface $objectmanager,   
        \Icecube\Businesses\Model\ResourceModel\Follow\CollectionFactory $modelFollowFactory,
        \Icecube\Businesses\Model\ResourceModel\Reviews\CollectionFactory $modelReviewsFactory,
        \Icecube\Businesses\Model\ResourceModel\Comments\CollectionFactory $modelCommentsFactory,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Icecube\Business\Model\ResourceModel\Packages\CollectionFactory $packageManager,
        \Icecube\Businesses\Model\ResourceModel\Adoffers\CollectionFactory $modelAdoffersFactory,
        \Icecube\Businesses\Model\ResourceModel\Businesses\CollectionFactory $businessesFactory,
        \Magento\Customer\Model\ResourceModel\Address\CollectionFactory $addressCollectionFactory,
        array $data = []
    )
    {
    	 $this->_modelIndexFactory = $modelIndexFactory;        
        /*$this->_storeManager = $storeManager;*/
       /* $this->_urlInterface = $urlInterface;*/
        $this->currentCustomer = $currentCustomer;  
        $this->_objectManager = $objectmanager;
        $this->_modelFollowFactory = $modelFollowFactory; 
        $this->_modelReviewsFactory = $modelReviewsFactory; 
        $this->_modelCommentsFactory = $modelCommentsFactory;
        $this->_modelAdoffersFactory = $modelAdoffersFactory;
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->_packageManager = $packageManager;
        $this->_businessFactory = $businessesFactory;
        $this->addressCollectionFactory = $addressCollectionFactory;
        parent::__construct($context, $data);
    }
    
    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
    public function getBaseUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
    }
    
    /**
     * Prining URLs using StoreManagerInterface
     */
    public function getMediaUrl()
    {
		return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
	}
    public function getStoreUrl()
    {
		return $this->_storeManager->getStore()->getBaseUrl();
	}
	public function getCollection($page_id)
	{
		$indexModel = $this->_objectManager->create('Icecube\Businesses\Model\Index');
		$item = $indexModel->load($page_id,'page_id');
        return $item;
	}
	public function getPageid($id)
	{
		$indexModel = $this->_objectManager->create('Icecube\Businesses\Model\Index');
		$item = $indexModel->load($id,'business_id');
        return $item;
	}
	public function getCollectionFactory()
	{
		$indexModel = $this->_modelIndexFactory->create();
		$item = $indexModel->load();
        return $item->getData();
	}
	public function getBusiness(){
		$id = $this->getRequest()->getParam('id');
        if ($id) {
			$model = $this->_objectManager->create('Icecube\Businesses\Model\Businesses');
			$model->load($id);
			return $model;
		}
		return false;
    }
    public function getBusinessId(){
		return $this->getRequest()->getParam('id');
    }
    
    public function getCustomerId(){
          return $this->currentCustomer->getCustomerId();
    }
    public function getBusinessWebsite($customerid)
	{	
		$customer = $this->_objectManager->create('Magento\Customer\Model\Customer');
		$customerData = $customer->load($customerid);
		foreach ($customerData->getAddresses() as $address)
		{
		    $data = $address->toArray();
		    return $data['website_name'];
		    break;
		}
	} 
	public function getBusinessCategory($customerid)
	{	
		$customer = $this->_objectManager->create('Magento\Customer\Model\Customer');
		$customerData = $customer->load($customerid);
		$category = $this->_objectManager->create('\Icecube\Extrafields\Model\Category\Types')->getAllOptions();
		foreach($category as $c):
			if($c['value'] == $customerData->getBusinessCategory()):
				return $c['label'];
				break;
			endif;
		endforeach;
	}
	public function remove_http($url) {
	   $disallowed = array('http://', 'https://');
	   foreach($disallowed as $d) {
	      if(strpos($url, $d) === 0) {
	         return str_replace($d, '', $url);
	      }
	   }
	   return $url;
	}
	public function getTotalFollows($business_id)
	{
		$followModel = $this->_modelFollowFactory->create();
		$item = $followModel->addFieldToFilter('business_id',$business_id)->load();
        return count($item->getData());
	}
	public function getFollowers($business_id)
	{
		$followModel = $this->_modelFollowFactory->create();
		$items = $followModel->addFieldToFilter('business_id',$business_id)->load();
        return $items;
	} 
	public function getCheckFollow($customer_id)
	{
		if($customer_id != null || $customer_id != ''):
			$checkFollow = $this->_objectManager->create('Icecube\Businesses\Model\Follow');
			$item = $checkFollow->load($customer_id,'customer_id');
	        if(count($item) == 0):
	        	return count($item);
	        else:
	        	return $item->getBusinessfollowId();
	        endif;
	    endif;
	}
	public function getCheckFollowBusiness($customer_id,$business_id)
	{
		$followedId = 0;
		$followModel = $this->_modelFollowFactory->create();
		$items = $followModel->addFieldToFilter('business_id',$business_id)->addFieldToFilter('customer_id',$customer_id)->load();
        foreach($items as $item){
			$followedId = $item->getId();
		}
		return $followedId;
	}
	public function getCommentsCountForBusiness($business_id)
	{
		$commentModel = $this->_modelCommentsFactory->create();
		$items = $commentModel->addFieldToFilter('business_id',$business_id)->load();
        return count($items);
	}
	public function getReviews($business_id)
	{
		$reviewModel = $this->_modelReviewsFactory->create();
		$items = $reviewModel->addFieldToFilter('business_id',$business_id)->setOrder('businessreview_id','DESC')->load();
        return $items;
	}
	public function getReviewCountForBusiness($business_id)
	{
		$reviewModel = $this->_modelReviewsFactory->create();
		$items = $reviewModel->addFieldToFilter('business_id',$business_id)->addFieldToFilter('status_id', array('null' => true))->setOrder('businessreview_id','DESC')->load();
        return $items;
	}
	public function getCustomerReviewCount($business_id,$customer_id)
	{
		$reviewModel = $this->_modelReviewsFactory->create();
		$items = $reviewModel->addFieldToFilter('business_id',$business_id)->addFieldToFilter('customer_id',$customer_id)->load();
        return count($items);
	}
	public function getCollectionReviews($business_id)
    {
    	$page = $this->getRequest()->getParam('p');
        if($page) $this->_curPage = $page;
                    
    	$reviewModel = $this->_modelReviewsFactory->create();
		$items = $reviewModel->addFieldToFilter('business_id',$business_id)
				->setOrder('businessreview_id','DESC')
				->setCurPage($this->_curPage)
				->setPageSize($this->_itemPerPage)
				->load();
        return $items;
    }

    public function getLoginUserCollectionReviews($customer_id)
    {
    	//$page = $this->getRequest()->getParam('p');
        //($page) $this->_curPage = $page;
                    
    	$reviewModel = $this->_modelReviewsFactory->create();
		$items = $reviewModel->addFieldToFilter('customer_id',$customer_id)
				->setOrder('businessreview_id','DESC')
				//->setCurPage($this->_curPage)
				//->setPageSize($this->_itemPerPage)
				->load();
        return $items;
    }

	public function getReviewPagination($business_id)
	{
		$collection = $this->getReviews($business_id);
		$html = false;
        if($collection == 'null') return;
        
        if($collection->count() > $this->_itemPerPage)
        {
        	$curPage = $this->getRequest()->getParam('p');
            $pager = (int)($collection->count() / $this->_itemPerPage);
            $count = ($collection->count() % $this->_itemPerPage == 0) ? $pager : $pager + 1 ;
            $url = $this->getPagerUrl();
            $start = 1;
            $end = $this->_pageFrame;
            
            $html .= '<ol>';
            if(isset($curPage) && $curPage != 1){
                $start = $curPage - 1;                                        
                $end = $start + $this->_pageFrame;
            }else{
                $end = $start + $this->_pageFrame;
            }
            if($end > $count){
                $start = $count - ($this->_pageFrame-1);
            }else{
                $count = $end-1;
            }            
            for($i = $start; $i<=$count; $i++)
            {
                if($i >= 1){
                    if($curPage){
                        $html .= ($curPage == $i) ? '<li class="current">'. $i .'</li>' : '<li><a href="'.$url.'?p='.$i.'">'. $i .'</a></li>';
                    }else{
                        $html .= ($i == 1) ? '<li class="current">'. $i .'</li>' : '<li><a href="'.$url.'?p='.$i.'">'. $i .'</a></li>';
                    }
                }                
            }            
            $html .= '</ol>';
        }        
        return $html;
	}
	public function getPagerUrl(){
		$urlInterface = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\UrlInterface');
		$cur_url = $urlInterface->getCurrentUrl();
        //$new_url = preg_replace('/\&p=.*/', '', $cur_url);
        $new_url = preg_replace('/\?p=.*/', '', $cur_url);
        return $new_url;
    }	
	public function getComments($reviewId)
	{
		$commentModel = $this->_modelCommentsFactory->create();
		$items = $commentModel->addFieldToFilter('review_id',$reviewId)->load();
        return $items;
	} 
	public function getCustomerDetails($customerid)
	{	
		$customer = $this->_objectManager->create('Magento\Customer\Model\Customer');
		$customerData = $customer->load($customerid);
		return $customerData;
	}
	public function getOrders($customerId)
    {
    	//$customerId = 422;    	
	    $orders = $this->_orderCollectionFactory->create()->addFieldToSelect(
	            '*'
	        )->addFieldToFilter(
	            'customer_id',
	            $customerId
	        )->setOrder(
	            'created_at',
	            'desc'
	        );
	    return $orders;
    }
    public function getAllPackages() { 
		return $this->_packageManager->create()->load();
	}
	public function getCollectionAdOffer($customerId)
    {
    	$adoffersModel = $this->_modelAdoffersFactory->create();
		$items = $adoffersModel->addFieldToFilter('businessuser_id',$customerId)
				->setOrder('adoffers_id','ASC')
				->load();
        return $items;
    }
    public function getAdOfferCollectionByBusinessId($business_id)
    {
    	$adoffersModel = $this->_modelAdoffersFactory->create();
		$items = $adoffersModel->addFieldToFilter('business_id',$business_id)
				->setOrder('adoffers_id','ASC')
				->load();
        return $items;
    }
    public function getCollectionAdOfferByUrl($unique_url,$business_id)
    {
    	$adoffersModel = $this->_modelAdoffersFactory->create();
		$items = $adoffersModel->addFieldToFilter('coupon_unique_url',$unique_url)->addFieldToFilter('business_id',$business_id)
				->setOrder('adoffers_id','ASC')
				->load();
        return $items;
    }
    function generateRandomString($length = 15) {
	    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $charactersLength = strlen($characters);
	    $randomString = '';
	    for ($i = 0; $i < $length; $i++) {
	        $randomString .= $characters[rand(0, $charactersLength - 1)];
	    }
	    return $randomString;
	}
	public function getAllBusinesses()
	{
		$businessesModel = $this->_businessFactory->create();
		$items = $businessesModel->setOrder('business_id','ASC')->load();
        return $items;
	}
	public function getAllFollowedBusinesses()
	{
		$customerId = $this->getCustomerId();		
		$followedId = 0;
		$businessIds = array();
		$followModel = $this->_modelFollowFactory->create();
		$items = $followModel->addFieldToFilter('customer_id',$customerId)->load();
        foreach($items as $item){
			$businessIds[] = $item->getBusinessId();
		}
		$businessesModel = $this->_businessFactory->create();
		$returnItems = $businessesModel->addFieldToFilter('business_id',array('in' => $businessIds))->setOrder('business_id','ASC')->load();
		return $returnItems;
	}
	public function getAllBusinessesWithFreeOffers()
	{
		$businessIds = array();
		$followModel = $this->_modelAdoffersFactory->create();
		$items = $followModel->load();
        foreach($items as $item){
			$businessIds[] = $item->getBusinessId();
		}
		$businessIds = array_unique($businessIds);
		$businessesModel = $this->_businessFactory->create();
		$returnItems = $businessesModel->addFieldToFilter('business_id',array('in' => $businessIds))->setOrder('business_id','ASC')->load();
		return $returnItems;
	}
	function getLatLong($address) {
	   $array = array('lat'=>'none', 'lng'=>'none');
	   $geo = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyB0EFOO6YRsvR4JtELf6ypGzqLo-_be4ns&address='.urlencode($address).'&sensor=false');

	   // We convert the JSON to an array
	   $geo = json_decode($geo, true);
	   
	   // If everything is cool
	   if ($geo['status'] == 'OK') {
	      $latitude = $geo['results'][0]['geometry']['location']['lat'];
	      $longitude = $geo['results'][0]['geometry']['location']['lng'];
	      $array['lat'] = $latitude;
	      $array['lng'] = $longitude;
	   }	   
	   return $array;
	}
	public function getFreeboxesUsers($postcode)
	{
		//$this->getCustomerDetails($returnItem->getParentId())->getGroupId()
		$addressModel = $this->addressCollectionFactory->create();
		$returnItems = $addressModel->addFieldToFilter('postcode',$postcode)->addFieldToSelect('parent_id')->load();
		$freeboxesUsers = array();
		foreach($returnItems as $returnItem){
			if($this->getCustomerDetails($returnItem->getParentId())->getGroupId()!=4){
				$freeboxesUsers[] = $returnItem->getParentId();
			}
		}
		$freeboxesUsers = array_unique($freeboxesUsers);
		return $freeboxesUsers;
	}
	public function getAddressesByPostalcode($postcode)
	{
		//$this->getCustomerDetails('422')->getGroupId()
		$addressModel = $this->addressCollectionFactory->create();
		$returnItems = $addressModel->addFieldToFilter('postcode',$postcode)->load();
		return $returnItems;
	}
}