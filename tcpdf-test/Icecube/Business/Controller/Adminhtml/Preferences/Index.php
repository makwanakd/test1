<?php
/**
 * Icecube Business Extension 
 * 
 * Icecube_Business
 * 
 * PHP version 5.x
 *
 * @category  Index
 * @preference   Icecube_Business
 * @author    Icecube Digital <support@icecubedigital.com>
 * @copyright 2016 Icecube Digital
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @link      http://www.icecubedigital.com
 */
namespace Icecube\Business\Controller\Adminhtml\Preferences;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
/**
 * Icecube_Business
 *
 * @category  Index
 * @preference   Icecube_Business
 * @author    Icecube Digital <support@icecubedigital.com>
 * @copyright 2016 Icecube Digital
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @link      http://www.icecubedigital.com
 */
class Index extends \Magento\Backend\App\Action
{
    /**
     * Page Factory
     *  
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * Constructure
     * 
     * @param Context $context Context
     * @param PageFactory $resultPageFactory Result Page Factory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** 
        * Result Page
        * 
        * @var \Magento\Backend\Model\View\Result\Page $resultPage 
        */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Icecube_Business::business_preferences_ui');
        $resultPage->getConfig()->getTitle()->prepend(__('Preferences'));
        return $resultPage;
    }
    
     /**
     * Return Bool
     * 
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Icecube_Business::business_preferences');
    }
}