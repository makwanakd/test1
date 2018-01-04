<?php
/**
 * Icecube Business Extension 
 * 
 * Icecube_Business
 * 
 * PHP version 5.x
 *
 * @category  Delete
 * @package   Icecube_Business
 * @author    Icecube Digital <support@icecubedigital.com>
 * @copyright 2016 Icecube Digital
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @link      http://www.icecubedigital.com
 */
namespace Icecube\Business\Controller\Adminhtml\Packages;

/**
 * Icecube_Business
 *
 * @category  Delete
 * @package   Icecube_Business
 * @author    Icecube Digital <support@icecubedigital.com>
 * @copyright 2016 Icecube Digital
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @link      http://www.icecubedigital.com
 */
class Delete extends \Icecube\Business\Controller\Adminhtml\Packages
{
	public function __construct(
        \Magento\Backend\App\Action\Context $context,        
        \Magento\Catalog\Model\ProductRepository $productRepository,
        array $data = []
    )
    {
        $this->_productRepository = $productRepository;
        parent::__construct($context);
    }
    public function getProductById($id)
    {
        return $this->_productRepository->getById($id);
    }
    
    public function getProductBySku($sku)
    {
        return $this->_productRepository->get($sku);
    }
    /**
     * Delete action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('id');
        /** 
        * Result Redirect 
        * 
        * @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect 
        */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            $title = "";
            try {
                // init model and delete
                $model = $this->_objectManager->create('Icecube\Business\Model\Packages');
                $model->load($id);
                $model->delete();
                $this->setCustomOption();
                // display success message
                $this->messageManager->addSuccess(__('Package has been deleted.'));
                // go to grid
               
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addError($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }
        
        // display error message
        $this->messageManager->addError(__('We can\'t find a Package to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
    public function setCustomOption()
    {
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			$packageCollection =  $objectManager->create('Icecube\Business\Model\ResourceModel\Packages\CollectionFactory');
		    $collectionPackage = $packageCollection->create()->load();
			$sku = 'business-packages';
			$_product = $this->getProductBySku($sku);
			$values = array();
			foreach($collectionPackage as $collection){
                    		$values[] = array(
			                        'title'=>$collection->getName(),
			                        'price'=>intval(str_replace(',','',$collection->getPrice())),
			                        'price_type'=>"fixed",
			                        'sku'=>strtolower(str_replace(" ","-",$collection->getName())),
			                        'sort_order'=>1,
			                        'is_delete'=>0,
			                        'option_type_id'=>-1,
                    			);
                    		}
			$customOption = $objectManager->create('Magento\Catalog\Api\Data\ProductCustomOptionInterface');
			$customOption->setTitle('Package')
			    ->setType('drop_down')
			    ->setIsRequire(true)
			    ->setSortOrder(1)
			    ->setPriceType('fixed')
			    ->setValues($values)
			    ->setProductSku($_product->getSku());
			$customOptions[] = $customOption;
			$_product->setOptions($customOptions)->save();
	}
}
