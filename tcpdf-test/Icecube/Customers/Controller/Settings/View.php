<?php
namespace Icecube\Customers\Controller\Settings;

class View extends \Magento\Framework\App\Action\Action
{
    public function execute()
    {
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $baseUrl = $om->get('Magento\Store\Model\StoreManagerInterface')
                    ->getStore()
                    ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB); 
        
        if(isset($_GET['profile_id'])){
			if($_GET['profile_id']!=NULL && $_GET['profile_id']!=''){
				$accountUrl = $om->create('Magento\Backend\Model\Url')->getAdminUrlCustom('*','customer/index/edit',array('id'=>$_GET['profile_id']));
				header("Location: ".$accountUrl);
               	exit();
			}else{
				header('Location: '.$baseUrl.'');
               	exit();
			}
		}else{
			header('Location: '.$baseUrl.'');
            exit();
		}
    }
    
}