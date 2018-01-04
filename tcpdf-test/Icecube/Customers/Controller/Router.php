<?php
namespace Icecube\Customers\Controller;

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

       	/*if(strpos($identifier, 'customers') !== false) {
                return null ;
        }*/
		
		if(strpos($identifier, 'customers/') !== false) {
			$patharr = explode("/",$identifier);
			$urlpath = end($patharr);
			$followedBusinesses = FALSE;
			$findBusinesses = FALSE;
			if(strpos($identifier, 'followed-businesses') !== FALSE){
				$urlpath = $patharr[count($patharr)-1];
				$followedBusinesses = TRUE;
			}else if(strpos($identifier, 'find-businesses') !== FALSE){
				$urlpath = $patharr[count($patharr)-1];
				$findBusinesses = TRUE;
			}else{
				
			}
            	if($followedBusinesses){
					$request->setModuleName('customers')->setControllerName('followed')->setActionName('index');
				}
				else if($findBusinesses){
					$request->setModuleName('customers')->setControllerName('find')->setActionName('index');
				}
				else{
					$request->setModuleName('customers')->setControllerName('settings')->setActionName('index');
				}
				$request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $identifier);
        		return $this->actionFactory->create('Magento\Framework\App\Action\Forward');			
		}
		else {
            return null;
        }
    }
}