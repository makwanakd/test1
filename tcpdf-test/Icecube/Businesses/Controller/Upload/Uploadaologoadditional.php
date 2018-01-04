<?php
namespace Icecube\Businesses\Controller\Upload;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Filesystem;
use Magento\Framework\UrlInterface;

class Uploadaologoadditional extends Action
{
	protected $_fileUploaderFactory;
 	
 	protected $subDir = 'business/adoffers/';
 	
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
	    /*UrlInterface $urlBuilder*/
	) {
		/*$this->_urlBuilder = $urlBuilder;*/
	 	$this->_filesystem = $fileSystem;
	    $this->_fileUploaderFactory = $fileUploaderFactory;
	    $this->resultJsonFactory = $resultJsonFactory;
	   /* $this->_objectManager = $objectmanager;*/
	   $this->_storeManager = $storeManager;
	    parent::__construct($context);
	}
	 
	public function execute() {
		$request = $this->getRequest();
		if($request->getParam("currentselection")=='1'){
			$uploader = $this->_fileUploaderFactory->create(['fileId' => 'offerlogoadditional1']);
		}elseif($request->getParam("currentselection")=='2'){
			$uploader = $this->_fileUploaderFactory->create(['fileId' => 'offerlogoadditional2']);
		}elseif($request->getParam("currentselection")=='3'){
			$uploader = $this->_fileUploaderFactory->create(['fileId' => 'offerlogoadditional3']);
		}else{
			$uploader = $this->_fileUploaderFactory->create(['fileId' => 'offerlogoadditional']);
		}
		//$uploader = $this->_fileUploaderFactory->create(['fileId' => 'offerlogo1']);
	    $uploader->setAllowedExtensions(['pdf', 'ai', 'eps']);
	    $uploader->setAllowRenameFiles(true);
        $uploader->setFilesDispersion(true);
        $uploader->setAllowCreateFolders(true);
	    $path = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath($this->subDir.'images/');
	    $filename = $uploader->save($path)['file'];
	    //$data['image'] = $this->getMediaUrl($this->subDir).$filename;
	    $data['image'] = $this->getSaveUrl($this->subDir).$filename;
	    $save = $this->getSaveUrl($this->subDir).$filename;
		//$save
	    
	    $result = $this->resultJsonFactory->create()->setData($data);
	 	return $result;
	}
	public function getMediaUrl($dir)
    {
		return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).$dir.'images';
	}
    public function getSaveUrl($dir)
    {
		return $dir.'images';
	}
}