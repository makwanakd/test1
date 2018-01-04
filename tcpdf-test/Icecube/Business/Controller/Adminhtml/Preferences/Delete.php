<?php
/**
 * Icecube Business Extension 
 * 
 * Icecube_Business
 * 
 * PHP version 5.x
 *
 * @category  Delete
 * @preference   Icecube_Business
 * @author    Icecube Digital <support@icecubedigital.com>
 * @copyright 2016 Icecube Digital
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @link      http://www.icecubedigital.com
 */
namespace Icecube\Business\Controller\Adminhtml\Preferences;

/**
 * Icecube_Business
 *
 * @category  Delete
 * @preference   Icecube_Business
 * @author    Icecube Digital <support@icecubedigital.com>
 * @copyright 2016 Icecube Digital
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @link      http://www.icecubedigital.com
 */
class Delete extends \Icecube\Business\Controller\Adminhtml\Preferences
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
                $model = $this->_objectManager->create('Icecube\Business\Model\Preferences');
                $model->load($id);
                $model->delete();
                $this->messageManager->addSuccess(__('Preference has been deleted.'));
               
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }
        
        // display error message
        $this->messageManager->addError(__('We can\'t find a Preference to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }    
}
