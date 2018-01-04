<?php
namespace Icecube\Businesses\Controller\Upload;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Filesystem;
use Magento\Framework\UrlInterface;

class Uploadcoverimage extends Action
{
	protected $_fileUploaderFactory;
 	
 	protected $subDir = 'business/cover/';
 	
 	protected $profileDir = 'business/profile/';
 	
 	protected $fileSystem;
 	
 	protected $resultJsonFactory;

 	protected $_storeManager;

 	/*protected $urlBuilder;*/
 	
 	/*protected $objectmanager;*/
 	
	public function __construct(
	    \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
	    \Magento\Framework\App\Action\Context $context,
	    \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
	    /*\Magento\Framework\ObjectManagerInterface $objectmanager,*/
	    \Magento\Store\Model\StoreManagerInterface $storeManager,
	    Filesystem $fileSystem
	   /* UrlInterface $urlBuilder*/
	) {
	/*	$this->_urlBuilder = $urlBuilder;*/
	 	$this->_filesystem = $fileSystem;
	    $this->_fileUploaderFactory = $fileUploaderFactory;
	    $this->resultJsonFactory = $resultJsonFactory;
	   /* $this->_objectManager = $objectmanager;*/
	    $this->_storeManager = $storeManager;
	    parent::__construct($context);
	}
	 
	public function execute() {
		$request = $this->getRequest();
		$id = (int)$request->getParam('id');
	    $img = $request->getParam('image');
        if($img == 'cover'):
	        $uploader = $this->_fileUploaderFactory->create(['fileId' => 'cover-image']);
		    $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
		    $uploader->setAllowRenameFiles(true);
	        $uploader->setFilesDispersion(true);
	        $uploader->setAllowCreateFolders(true);
		    $path = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath($this->subDir.'images/');
		    $filename = $uploader->save($path)['file'];
		    $data['image'] = $this->getMediaUrl($this->subDir).$filename;
		    //$this->cropthis($data['image']);
		    
		    $save = $this->getSaveUrl($this->subDir).$filename;
		    /*$this->cropthis($data['image'],$save);*/
		    $this->setSave($id,$save);
	    else:
		    $uploader = $this->_fileUploaderFactory->create(['fileId' => 'profile-img']);
		    $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
		    $uploader->setAllowRenameFiles(true);
	        $uploader->setFilesDispersion(true);
	        $uploader->setAllowCreateFolders(true);
		    $path = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath($this->profileDir.'images/');
		    $filename = $uploader->save($path)['file'];
		    
		    $save = $this->getSaveUrl($this->profileDir).$filename;
		    if($customerid = $request->getParam('customerid')):
		    	$data['image'] = $this->getMediaUrl($this->profileDir).$filename;
	    		$this->setSaveProfile($id,$save,$customerid);
	    		$this->cropthis($data['image'],$save);
	    	else:
	    		$data['error'] = 'Can\'t Upload Image';
	    	endif;
	    endif;
	    $result = $this->resultJsonFactory->create()->setData($data);
	 	return $result;
	}
	/*public function getMediaUrl($dir)
    {
        return $this->_urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA]).$dir.'images';
    }*/
    public function getMediaUrl($dir)
    {
		return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).$dir.'images';
	}
    public function getSaveUrl($dir)
    {
		return $dir.'images';
	}
	private function setSave($id,$save)
	{
		$template = $this->_objectManager->create('Icecube\Businesses\Model\Index');
		$template->load($id,'business_id');
		$template->setData('image',
	    	$save
        );
        $template->save();
	}
	private function setSaveProfile($id,$save,$customerid)
	{	
		/*$customer = $this->_objectManager->create('Magento\Customer\Model\Customer');
		$customerData = $customer->load($customerid);
		$customerData->setProfileImage($save);
		$customerData->save();*/
		$customerFactory = $this->_objectManager->create('Magento\Customer\Model\CustomerFactory');
		$customer = $this->_objectManager->create('Magento\Customer\Model\Customer');
		$customerData = $this->_objectManager->create('Magento\Customer\Model\Data\Customer');
		$customerResourceFactory = $this->_objectManager->create('Magento\Customer\Model\ResourceModel\CustomerFactory');
		$customer = $customerFactory->create();
		$customerData = $customer->getDataModel();
		$customerData->setId($customerid);
		$customerData->setCustomAttribute('profile_image', $save);
		$customer->updateData($customerData);
		$customerResource = $customerResourceFactory->create();
		if ($save != "") {
		    $customerResource->saveAttribute($customer, 'profile_image');
		}
	}
	public function cropthis($updatedImage,$savePath){		
		$imageExtension = explode('.',$updatedImage);
		$imageExtension = $imageExtension[count($imageExtension)-1];
		
		$imageFilename = explode('/',$updatedImage);
		$imageFilename = $imageFilename[count($imageFilename)-1];
		
		$imagePath = explode('/',$updatedImage);
		$imageFilename = $imageFilename[count($imageFilename)-1];
		
		$imageFilename = 'pub/media/'.$savePath;
		
		if($imageExtension=='jpg'){
			$im = imagecreatefromjpeg($updatedImage);
		}else if($imageExtension=='jpeg'){
			$im = imagecreatefromjpeg($updatedImage);
		}else if($imageExtension=='png'){
			$im = imagecreatefrompng($updatedImage);
		}else{
			$im = imagecreatefromgif($updatedImage);
		}
		
	    $uploadedImage = getimagesize($updatedImage);
		$uiwidth = $uploadedImage[0];
		$uiheight = $uploadedImage[1];
	    $xcordinate = 0;
	    $ycordinate = 0;
	    if($uiwidth){
			$xcordinate = ($uiwidth / 2) - 200;
		}
		if($uiheight){
			$ycordinate = ($uiheight / 2) - 200;
		}
	    
		//$size = min(imagesx($im), imagesy($im));
		$im2 = imagecrop($im, ['x' => $xcordinate, 'y' => $ycordinate, 'width' => 400, 'height' => 400]);
		if ($im2 !== FALSE) {
			if($imageExtension=='jpg'){
				imagejpeg($im2, $imageFilename);
			}else if($imageExtension=='jpeg'){
				imagejpeg($im2, $imageFilename);
			}else if($imageExtension=='png'){
				imagepng($im2, $imageFilename);
			}else{
				imagegif($im2, $imageFilename);
			}		    
		}
	}
}