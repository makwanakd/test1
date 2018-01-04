<?php
namespace Icecube\Businesses\Controller;

class Router implements \Magento\Framework\App\RouterInterface
{
    protected $actionFactory;
 
    protected $_response;
 
    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Framework\App\ResponseInterface $response
    ) {
        $this->actionFactory = $actionFactory;
        $this->_response = $response;
    }
 
    public function match(\Magento\Framework\App\RequestInterface $request)
    {
		$identifier = trim($request->getPathInfo(), '/');

        if(strpos($identifier, 'businesses/view/index/id') !== false) {
                return null ;
        }
		else if(strpos($identifier, 'businesses/') !== false) {
			$patharr = explode("/",$identifier);
			$urlpath = end($patharr);
			$adOfferManager = FALSE;			
			$profilesettings = FALSE;
			$about = FALSE;
			$analytics = FALSE;
			$freeoffers = FALSE;
			$locations = FALSE;
			$coupon_details = FALSE;
			$coupon_details_download = FALSE;
			$coupon_details_print = FALSE;
			if(strpos($identifier, 'ad-offer-manager') !== FALSE){
				$urlpath = $patharr[count($patharr)-2];
				$adOfferManager = TRUE;
			}else if(strpos($identifier, 'profile-settings') !== FALSE){
				$urlpath = $patharr[count($patharr)-2];
				$profilesettings = TRUE;
			}else if(strpos($identifier, 'analytics') !== FALSE){
				$urlpath = $patharr[count($patharr)-2];
				$analytics = TRUE;
			}else if(strpos($identifier, 'about/follow') !== FALSE || strpos($identifier, 'about/unfollow') !== FALSE){
				$urlpath = $patharr[count($patharr)-3];
				$about = TRUE;
			}           
			else if(strpos($identifier, 'about') !== FALSE){
				$urlpath = $patharr[count($patharr)-2];
				$about = TRUE;
			} 
			else if(strpos($identifier, 'free-offers') !== FALSE){
				$urlpath = $patharr[count($patharr)-2];
				$freeoffers = TRUE;
			}else if(strpos($identifier, 'locations') !== FALSE){
				$urlpath = $patharr[count($patharr)-2];
				$locations = TRUE;
			}else if(strpos($identifier, 'coupon-details-download') !== FALSE){
				$urlpath = $patharr[count($patharr)-2];
				$coupon_details_download = TRUE;
			}else if(strpos($identifier, 'coupon-details-printpdf') !== FALSE){
				$urlpath = $patharr[count($patharr)-2];
				$coupon_details_print = TRUE;
			}else if(strpos($identifier, 'coupon-details') !== FALSE){
				$urlpath = $patharr[count($patharr)-2];
				$coupon_details = TRUE;
			}else{
				
			}
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			$modelcollection = $objectManager->get('\Icecube\Businesses\Model\ResourceModel\Businesses\CollectionFactory')->create();
			$modelcollection->addFieldToFilter('business_page_url' , $urlpath);
			//var_dump($modelcollection->count());
			if($modelcollection->count() >=1 && $business = $modelcollection->getFirstItem()){
				if($adOfferManager){
					$request->setModuleName('businesses')->setControllerName('adoffermanager')->setActionName('index')->setParam('id',$business->getId());
				}
				else if($profilesettings){
					$request->setModuleName('businesses')->setControllerName('profilesettings')->setActionName('index')->setParam('id',$business->getId());
				}
				else if($about){
					$request->setModuleName('businesses')->setControllerName('about')->setActionName('index')->setParam('id',$business->getId());
				}
				else if($analytics){
					$request->setModuleName('businesses')->setControllerName('analytics')->setActionName('index')->setParam('id',$business->getId());
				}
				else if($freeoffers){
					$request->setModuleName('businesses')->setControllerName('freeoffers')->setActionName('index')->setParam('id',$business->getId());
				}
				else if($locations){
					$request->setModuleName('businesses')->setControllerName('locations')->setActionName('index')->setParam('id',$business->getId());
				}
				else if($coupon_details_download){
					$request->setModuleName('businesses')->setControllerName('coupondetails')->setActionName('download')->setParam('id',$business->getId());
				}
				else if($coupon_details_print){
					$request->setModuleName('businesses')->setControllerName('coupondetails')->setActionName('printpdf')->setParam('id',$business->getId());
				}
				else if($coupon_details){
					$request->setModuleName('businesses')->setControllerName('coupondetails')->setActionName('index')->setParam('id',$business->getId());
				}
				else{
					$request->setModuleName('businesses')->setControllerName('view')->setActionName('index')->setParam('id',$business->getId());
				}
 				
				
				$request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $identifier);
        		return $this->actionFactory->create('Magento\Framework\App\Action\Forward');
				/*$request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $identifier);
                $request->setAlias(\Magento\Framework\UrlInterface::REWRITE_REQUEST_PATH_ALIAS, '/'.$identifier);
                $request->setPathInfo('/' . $identifier);
                return;*/
			}
			else
			{
				return null;
			}
		}
		else {
            return null;
        }
    }
}