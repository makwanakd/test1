<?php
/**
 * Icecube Business Extension 
 * 
 * Icecube_Business
 * 
 * PHP version 5.x
 *
 * @category  Massdelete
 * @preference   Icecube_Business
 * @author    Icecube Digital <support@icecubedigital.com>
 * @copyright 2016 Icecube Digital
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @link      http://www.icecubedigital.com
 */
namespace Icecube\Business\Controller\Adminhtml\Preferences;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Icecube\Business\Model\ResourceModel\Preferences\CollectionFactory;

/**
 * Icecube_Business
 *
 * @category  Massdelete
 * @preference   Icecube_Business
 * @author    Icecube Digital <support@icecubedigital.com>
 * @copyright 2016 Icecube Digital
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @link      http://www.icecubedigital.com
 */
class MassDelete extends \Icecube\Business\Controller\Adminhtml\Preferences
{
    
    /**
     * Filter Variable
     * 
     * @var Filter
     */
    protected $filter;

    /**
     * Collection Factory Variable
     * 
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Constructure
     * 
     * @param Context $context Context
     * @param Filter $filter Filter
     * @param CollectionFactory $collectionFactory Collection Factory
     */
    public function __construct(
	    Context $context, 
	    Filter $filter, 
	    CollectionFactory $collectionFactory
    )
    {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }
    /**
     * Delete action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $collectionSize = $collection->getSize();

        foreach ($collection as $template) {
            $template->delete();
        }

        $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', $collectionSize));

        /**
        * Result Redirect
        *  
        * @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect 
        */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
